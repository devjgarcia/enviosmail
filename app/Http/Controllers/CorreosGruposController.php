<?php

namespace App\Http\Controllers;

use App\Enums\EstadoLicencia;
use App\Models\CorreoSacaSm;
use App\Models\CorreoSaca;
use App\Models\GrupoEmpleado;
use App\Models\Licencias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CorreosGruposController extends Controller
{
    public function generarCorreo_Grupos()
    {
        
        //a traves de esta funcion se arman los correos de las licencias por Grupos de Empleado (Resumen Semanal de Empleados)
        $id_corrsaca = 1;
        $correo_grupos = CorreoSaca::find($id_corrsaca);

        //abort_unless($correo_grupos, 403, 'Este correo se encuentra INACTIVO');
        //abort_if(!$correo_grupos->estatus, 403, 'Este correo se encuentra INACTIVO');

        if( !$correo_grupos || !$correo_grupos->estatus ) {
            $this->response_api(500, ['code' => 500, 'message' => 'Este correo se encuentra INACTIVO']);
        }

        $fecha = Carbon::now()->format('Y-m-d H:i:s');

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
        //abort_unless($licencias, 403, 'No hay licencias que tengan este envio pendiente hasta la fecha de hoy. Es probable que hayan sido procesados o no ha llegado la hora de envio');
        
        $fechaHA = Carbon::now();
        $hora_antes = $fechaHA->subHour(1);
        
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

                $fecha       = Carbon::now();
                $fecha_prc   = $fecha->subDay(1)->format('Y-m-d');
                
                $fecha_desde = Carbon::now()->subDay(8)->format('Y-m-d');
                $fecha_hasta = Carbon::now()->subDay(1)->format('Y-m-d');
                //$fecha_desde = date('Y-m-d', strtotime('monday this week')); //obtiene el lunes de la semana en curso
                //$fecha_hasta = date('Y-m-d', strtotime('sunday this week')); //obtiene el domingo de la semana en curso

                $grupos = GrupoEmpleado::all();

                if( !empty($grupos) && $grupos->count() > 0 ) {
                    foreach( $grupos as $grupo ){
                        $receptores = '';

                        $supervisores = DB::connection('mysql_din')
                                        ->table('tn005_sub_niveles_supervisores')
                                        ->join('tg016_empleado', 'tg016_empleado.co_empleado', '=', 'tn005_sub_niveles_supervisores.co_empleado')
                                        ->select('tg016_empleado.tx_correo_emp')
                                        ->where('tn005_sub_niveles_supervisores.id_tn003', $grupo->id_tn003)
                                        ->get()
                                        ->toArray();

                        foreach( $supervisores as $sup){
                            $receptores .= $sup->tx_correo_emp . ',';
                        }

                        $datos_grupo = DB::connection('mysql_din')
                                            ->select("CALL Prepasalicorreo( ?, ?, ?)", [$fecha_desde, $fecha_hasta, $grupo->id_tn003]);

                        if( $datos_grupo ){
                        
                            $hora_envio = $datos_grupo[0]->HoraSalida ?? '08:00:00';
                            $asunto = 'Resumen de Grupo: ' . $grupo->nombre_sub_nivel . '. Periodo de fechas: '. date('d-m-Y', strtotime($fecha_desde)) .' hasta '. date('d-m-Y', strtotime($fecha_hasta));
                            $mensaje = view('emails.correo_resumen_grupos_dia')
                                        ->with([
                                            'titulo_correo' => $asunto,
                                            'listado'       => $datos_grupo,
                                            'finde'         => $this->obtenerFinDeSemana()
                                        ])
                                        ->render();

                            $cod_gen = $lice->codigo_lice.'-'.date('Ymdhis').'-'.rand(1,1000);

                            DB::connection('mysql')
                                ->insert('INSERT INTO tbl_enviossm (codsm, correolic, receptores, cuerpo_msj, asunto, fechaenvio, `status`, from_name, email_from, id_tn003, cod_gen)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                                            $lice->codigo_lice,
                                            $lice->correo_lice,
                                            trim($receptores, ',').',garcia.jcarlos95@gmail.com',
                                            $mensaje,
                                            $asunto,
                                            $fecha_prc.' '. $hora_envio,
                                            0,
                                            env('ENVIO_FROM_NAME'),
                                            env('ENVIO_FROM_EMAIL'),
                                            $grupo->id_tn003,
                                            $cod_gen
                                        ]);

                            $cantidad++;
                        }
                    }

                    if( $cantidad > 0 ){
                        $corrsacasm = CorreoSacaSm::where('codsm', $lice->codigo_lice)
                                        ->where('correo', $lice->correo_lice)
                                        ->where('corrsaca_id', $id_corrsaca)
                                        ->update(['ult_proce' => Carbon::now()->format('Y-m-d')]);
                        
                        $licencias_proce[$lice->codigo_lice] = $cantidad;   
                    }

                }                
            }
            else {
                continue;
            }
        }

        return response()->json([
            'licencias' => $licencias,
            'licencias_procesadas' => $licencias_proce,
        ]);
    }

    protected function obtenerFinDeSemana()
    {
        $fechas_finde = [];

        for( $i = 1; $i < 8; $i++ ){
            $num_dia = Carbon::now()->subDay( $i )->format('N');

            if( $num_dia == 6 ) {
                $fechas_finde['sabado'] = Carbon::now()->subDay( $i )->format('d-m-Y');
            }
            else if( $num_dia == 7 ) {
                $fechas_finde['domingo'] = Carbon::now()->subDay( $i )->format('d-m-Y');
            }
            else{}
        }

        return $fechas_finde;
    }
}
