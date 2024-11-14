<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaltasMesResource;
use App\Models\CorreoSaca;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CorreoFaltaEmpleadoController extends Controller
{
    public function __construct()
    {
        setlocale(LC_ALL, 'es_VE');
        date_default_timezone_set('America/Caracas');
    }

    public function armarCorreo( Request $request )
    {
        //a traves de esta funcion se arman los correos con las inasistencias de la empresa

        //en este correo se usan 3 tipos de $idcorrsaca; 4 = llegadas tarde / 5 = inasistencias / 6 = salidas anticipadas
        $id_corrsaca = $request->get('corrid'); // es el id de la tabla corrsaca 
        $correo_grupos = CorreoSaca::find($id_corrsaca); //verifico que exista
        abort_unless($correo_grupos, 403, 'Este correo se encuentra INACTIVO'); 
        abort_if(!$correo_grupos->estatus, 403, 'Este correo se encuentra INACTIVO');

        $fecha = Carbon::now()->format('Y-m-d');

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
                        corrsaca_sm.ult_proce IS NULL OR
                        CAST(corrsaca_sm.ult_proce AS DATE) < CAST(? AS DATE)
                    ) AND
                    corrsaca_sm.corrsaca_id = ? AND
                    corrsaca_sm.estatus = ? AND
                    corrsaca_sm.estatus_admin = ? AND
                    corrsaca.estatus = ?";

        $licencias = DB::select($sql,[$fecha, $id_corrsaca, 1, 1, 1]);
        $licencias_proce = [];
        
        abort_unless($licencias, 403, 'No hay licencias que tengan este envio pendiente hasta la fecha de hoy. Es probable que hayan sido procesados o no ha llegado la hora de envio');
        
        
        foreach( $licencias as $lice ) {

            $fHoraActual = Carbon::now();
            $fHoraEnvio  = Carbon::parse( $lice->henvio );
            $cantidad = 0;

            if( $fHoraActual > $fHoraEnvio ){
                //echo "Si -> {$lice->codigo_lice} --> {$fHoraActual->format('H:i:s')} > {$fHoraEnvio->format('H:i:s')}";

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
                    $fechaBusqueda = (!empty($request->get('fecha'))) ? Carbon::parse($request->get('fecha')) : $fecha_ac;
                    $nuevaFechaUlt = Carbon::now()->format('Y-m-d');
                    $tipo_falta = $this->getTipoFalta($id_corrsaca);

                    $faltas = DB::connection('mysql_din')
                                ->select("SELECT 
                                        VJ.*,
                                        E.tx_correo_emp
                                    FROM 
                                    `justificacionesemplevis` VJ
                                    LEFT JOIN `tg016_empleado` E ON VJ.co_empleado = E.co_empleado
                                    WHERE 
                                        CAST(VJ.fecha_justuficada AS DATE) = CAST('{$fechaBusqueda->format('Y-m-d')}' AS DATE) AND
                                        VJ.grupo = '{$tipo_falta}'
                                ");

                    $faltasMes = DB::connection('mysql_din')
                                    ->select("SELECT *
                                        FROM 
                                            `justificacionesemplevis` VJ
                                        WHERE 
                                            YEAR(VJ.fecha_justuficada) = YEAR('{$fechaBusqueda->format('Y-m-d')}') AND
                                            MONTH(VJ.fecha_justuficada) = MONTH('{$fechaBusqueda->format('Y-m-d')}')
                                    ");

                    if( !empty($faltas) ){

                        foreach( $faltas as $falta ){

                            $co_emple = $falta->co_empleado;

                            //filtro en el array de faltas de mes, solo las del empleado en curso
                            $faltasMesEmple = array_filter($faltasMes, function($f) use( $co_emple ){
                                return $f->co_empleado == $co_emple;
                            });

                            $tipoFalta = $this->obtenerDatosTipoFalta( $falta );

                            $hora_envio = $lice->henvio ?? '08:00:00';
                            $asunto = $tipoFalta['asunto'] ?? "Sacamovil";
                            $mensaje = view('emails.correo_falta_empleado')
                                    ->with([
                                        'falta'         => $falta,
                                        'lice'          => $lice,
                                        'titulo_correo' => $tipoFalta['titulo'],
                                        'faltas_mes'    => $faltasMesEmple,
                                        'fecha'         => $fechaBusqueda->format('d-m-Y'),
                                        'fecha_sf'      => $fechaBusqueda->format('Y-m-d'),
                                        'link_justificacion' => env('URL_SACA').'llic/'.md5($lice->codigo_lice).'/?rd=justificaemple'.'/'.$fechaBusqueda->format('Y-m-d')
                                    ])
                                    ->render();

                            $cod_gen = $lice->codigo_lice.'-'.date('Ymdhis').'-'.rand(1,10000).'-'.$id_corrsaca;

                            DB::connection('mysql')
                                ->insert('INSERT INTO tbl_enviossm (codsm, correolic, receptores, cuerpo_msj, asunto, fechaenvio, status, from_name, email_from, id_tn003, cod_gen)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                                            $lice->codigo_lice,
                                            $lice->correo_lice,
                                            trim($falta->tx_correo_emp),
                                            //'garcia.jcarlos95@gmail.com',
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

                        $licencias_proce[] = $lice;
                    }
                }
                else {
                    continue;
                }
                /**/
            }
            else{
                //no ha llegado la hora de envio
                continue;
            }
        }

        
        return response()->json([
            'licencias_encontradas' => $licencias,
            'licencias_procesadas' => $licencias_proce,
            'hora_servidor' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        /**/
    }

    private function obtenerDatosTipoFalta( $falta )
    {
        $fecha     = Carbon::parse($falta->fecha_justuficada);
        $fechaShow = Carbon::parse($falta->fecha_justuficada)->format('d-m-Y');

        $faltas = [
            'INASISTENCIAS' => [
                'asunto' => "{$falta->nb_empleado} - Inasistencia ({$fechaShow})",
                'titulo' => "Inasistencia del dia: {$fecha->format('d-m-Y')}"
            ],
            'LLEGADAS TARDE' => [
                'asunto' => "{$falta->nb_empleado} - Llegada Tarde ({$fechaShow})",
                'titulo' => "Llegada Tarde del dia: {$fecha->format('d-m-Y')}"
            ],
            'SALIDAS ANTICIPADAS' => [
                'asunto' => "{$falta->nb_empleado} - Salida Anticipada ({$fechaShow})",
                'titulo' => "Salida Anticipada del dia: {$fecha->format('d-m-Y')}"
            ],
        ];

        return $faltas[ $falta->grupo ];
    }

    private function getTipoFalta( $idcorrsaca )
    {
        return [
            4 => 'LLEGADAS TARDE',
            5 => 'INASISTENCIAS',
            6 => 'SALIDAS ANTICIPADAS'
        ][$idcorrsaca];
    }
}
