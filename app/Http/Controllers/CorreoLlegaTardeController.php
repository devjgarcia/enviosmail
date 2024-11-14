<?php

namespace App\Http\Controllers;

use App\Models\CorreoSaca;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CorreoLlegaTardeController extends Controller
{
    public function __invoke()
    {
        parent::__construct();    
    }
    
    public function armarCorreosLlegaTarde( Request $request )
    {
        //a traves de esta funcion se arman los correos con las inasistencias de la empresa
        $id_corrsaca = 3; // es el id de la tabla corrsaca 
        $correo_grupos = CorreoSaca::find($id_corrsaca); //verifico que exista
        
        if( !$correo_grupos || !$correo_grupos->estatus ) {
            $this->response_api(500, ['code' => 500, 'message' => 'Este correo se encuentra INACTIVO']);
        }

        $fecha = Carbon::now()->format('Y-m-d H:i:s');

        //obtengo las Licencias que tengan agregado este correo para su salida
        $sql = "SELECT
                    corrsaca_sm.henvio,
                    corrsaca_sm.ult_proce,
                    lice_sm.codigo AS codigo_lice,
                    lice_sm.correo AS correo_lice
                FROM
                    corrsaca_sm
                LEFT JOIN corrsaca ON corrsaca_sm.corrsaca_id = corrsaca.id
                LEFT JOIN lice_sm ON corrsaca_sm.codsm = lice_sm.codigo
                
                WHERE
                    (
                        (CAST(corrsaca_sm.ult_proce AS DATE) < CAST(? AS DATE)) AND
                        (CAST( CONCAT(corrsaca_sm.ult_proce,' ', corrsaca_sm.henvio) AS DATETIME) < CAST(? AS DATETIME) )
                    ) AND
                    corrsaca_sm.corrsaca_id = ? AND
                    corrsaca_sm.estatus = ? AND
                    corrsaca_sm.estatus_admin = ? AND
                    corrsaca.estatus = ?";

        $licencias = DB::select($sql,[$fecha, $fecha, $id_corrsaca, 1, 1, 1]);
        $licencias_proce = [];
        
        if( !$licencias ) {
            $this->response_api(500, [
                'code' => 500, 
                'message' => 'No hay licencias que tengan este envio pendiente hasta la fecha de hoy. Es probable que hayan sido procesados o no ha llegado la hora de envio'
            ]);
        }
        
        foreach( $licencias as $lice ) {

            $cantidad = 0;

            $datos_conn = DB::select( "CALL `ActProtecSMKey`('{$lice->codigo_lice}', '{$lice->correo_lice}', 300)" );
            
            
            if( isset($datos_conn[0]->webdb) && !empty($datos_conn[0]->webdb) ) {
                //setea valores del .env para la conexion dinamica
                Config::set([
                    'database.connections.mysql_din.host'     => 'localhost',
                    'database.connections.mysql_din.database' => $datos_conn[0]->webdb,
                    'database.connections.mysql_din.username' => $datos_conn[0]->webuser,
                    'database.connections.mysql_din.password' => $datos_conn[0]->webpass,
                ]);

                //limpieza de cache del .env
                DB::purge('mysql_din');
                DB::reconnect('mysql_din');

                $fecha_ac      = Carbon::now();
                $fechaBusqueda = $fecha_ac;
                $nuevaFechaUlt = Carbon::now()->format('Y-m-d');

                //buscar configuracion de correos gerenciales
                $config_correos = DB::connection('mysql')
                                    ->table('correos_sm')
                                    ->where('codigo_licencia', $lice->codigo_lice)
                                    ->whereNotNull('from_to')
                                    ->select('from_to', 'from_cc')
                                    ->limit(1)
                                    ->get();

                if( !empty($config_correos) && isset($config_correos[0]) ) {

                    $receptores = $config_correos[0]->from_to;

                    $llegadas = DB::connection('mysql_din')
                                ->select("SELECT 
                                        LT.*,
                                        CAST( LT.fe_hh_entrada AS DATE) AS fechallega,
                                        DATE_FORMAT(CAST( LT.fe_hh_entrada AS DATETIME), '%h:%i %p') AS horallega_format,
                                        E.nb_empleado, 
                                        C.nb_cargo,
                                        EM.nb_empresas,
                                        DP.nb_dpto,
                                        (
                                            SELECT COUNT(*) 
                                            FROM tr001_llegada_tarde LTE
                                            WHERE 
                                                LTE.co_empleado = LT.co_empleado AND 
                                                LTE.fe_hh_entrada BETWEEN DATE_SUB( CAST('{$fechaBusqueda->format('Y-m-d')}' AS DATE), INTERVAL 7 DAY) AND CAST('{$fechaBusqueda->format('Y-m-d')}' AS DATE)
                                        ) AS canti_llegadas
                                    FROM 
                                    tr001_llegada_tarde LT
                                    LEFT JOIN tg016_empleado E ON LT.co_empleado = E.co_empleado
                                    LEFT JOIN tg015_cargos C ON E.co_cargo = C.co_cargo
                                    LEFT JOIN tg012_empresas EM ON E.co_empresas = EM.co_empresas
                                    LEFT JOIN tg014_departamento DP ON E.co_dpto = DP.co_dpto
                                    
                                    WHERE 
                                        CAST(LT.fe_hh_entrada AS DATE) = CAST('{$fechaBusqueda->format('Y-m-d')}' AS DATE)
                                ");

                    if( !empty($llegadas) ){

                        $hora_envio = $lice->henvio ?? '08:00:00';
                        $asunto = "Llegadas tarde del día: {$fechaBusqueda->format('d-m-Y')} ({$lice->codigo_lice})";
                        $mensaje = view('emails.llegadas_tarde')
                                    ->with([
                                        'titulo_correo' => $asunto,
                                        'listado'       => $llegadas,
                                        'fecha'         => $fechaBusqueda->format('d-m-Y'),
                                    ])
                                    ->render();

                        $cod_gen = $lice->codigo_lice.'-'.date('Ymdhis').'-'.rand(1,1000);

                        DB::connection('mysql')
                            ->insert('INSERT INTO tbl_enviossm (codsm, correolic, receptores, cuerpo_msj, asunto, fechaenvio, status, from_name, email_from, id_tn003, cod_gen)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                                        $lice->codigo_lice,
                                        $lice->correo_lice,
                                        trim($receptores),
                                        $mensaje,
                                        $asunto,
                                        $fechaBusqueda->format('Y-m-d').' '. $hora_envio,
                                        0,
                                        env('ENVIO_FROM_NAME'),
                                        env('ENVIO_FROM_EMAIL'),
                                        0,
                                        $cod_gen
                                    ]);

                        $cantidad++;
                    }
                }
                
                if( $cantidad > 0 ){
                    DB::select("UPDATE corrsaca_sm SET ult_proce = '{$nuevaFechaUlt}' WHERE
                                    codsm = ? AND correo = ? AND corrsaca_id = ?", [
                                        $lice->codigo_lice,
                                        $lice->correo_lice,
                                        $id_corrsaca
                                    ]);
                    $licencias_proce[$lice->codigo_lice] = $cantidad;
                }
            }
            else {
                continue;
            }
        }
        
        return response()->json([
            'licencias' => $licencias,
            'licencias_procesadas' => $licencias_proce
        ]);
    }
}
