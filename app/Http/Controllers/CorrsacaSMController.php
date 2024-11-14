<?php

namespace App\Http\Controllers;

use App\Models\CorreoSaca;
use App\Models\CorreoSacaSm;
use App\Models\Licencias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CorrsacaSMController extends Controller
{
    public function saveCorrsacaSM( Request $request )
    {
        $rules = [
            'id' => 'nullable|integer',
            'codsm'       => [
                'required',
                'string',
                'min:4',
                Rule::exists(Licencias::class, 'codigo')->where('correo', $request->correo)
            ], 
            'correo'      => 'required|email', 
            'corrsaca_id' => 'required|exists:' .CorreoSaca::class. ',id',
            'estatus'     => 'required|integer',
            'henvio'      => 'required',
        ];

        //abort_unless($datos, 500, 'Verifique la informacion enviada');
        

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'saved'   => false,
                'errors'  => $validator->errors()->all()
            ];
        }

        $corrsaca = CorreoSacaSm::updateOrCreate([
            'codsm'       => $request->codsm,
            'correo'      => $request->correo,
            'corrsaca_id' => $request->corrsaca_id,
        ]);

        $corrsaca->estatus = $request->estatus;
        $corrsaca->henvio  = $request->henvio;
        $corrsaca->save();

        return response()->json(['corrsaca' => $corrsaca]);
    }

    public function getBySM( Request $request )
    {
        $rules = [
            'codsm'       => [
                'required',
                'string',
                'min:4',
                Rule::exists(Licencias::class, 'codigo')->where('correo', $request->correo)
            ], 
            'correo'      => 'required|email', 
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'saved'   => false,
                'errors'  => $validator->errors()->all()
            ];
        }

        //verifico y creo algun nuevo correo en el que no tenga registro esta licencia
        $this->verifyAndGenerate( $request->codsm, $request->correo );

        $sql = "SELECT 
                    corrsaca.descripcion,
                    corrsaca.detalles,
                    corrsaca.estatus AS estatus_corr,
                    corrsaca_sm.id,
                    corrsaca_sm.codsm,
                    corrsaca_sm.correo,
                    corrsaca_sm.corrsaca_id,
                    corrsaca_sm.estatus,
                    corrsaca_sm.estatus_admin AS estado_corrsm_admin,
                    corrsaca_sm.henvio
                FROM
                    corrsaca 
                LEFT JOIN corrsaca_sm ON corrsaca.id = corrsaca_sm.corrsaca_id
                
                WHERE 
                    corrsaca_sm.codsm = ? AND 
                    corrsaca_sm.correo = ?";

        $datos = DB::select($sql, [$request->codsm, $request->correo]);

        return $datos;
    }

    protected function verifyAndGenerate( $sm, $correo )
    {
        //se consulta que existan los registros para la sm, caso contrario se crean
        $sqlCorreos    = "SELECT * FROM corrsaca";
        $buscarCorreos = DB::select($sqlCorreos);

        if( $buscarCorreos ){
            foreach( $buscarCorreos as $corrsaca ){

                $corrsaca_sm = CorreoSacaSm::where('codsm', $sm)
                                    ->where('correo', $correo)
                                    ->where('corrsaca_id', $corrsaca->id)
                                    ->first();

                if( !$corrsaca_sm ) {
                    $crear = CorreoSacaSm::create([
                        'codsm'         => $sm,
                        'correo'        => $correo,
                        'corrsaca_id'   => $corrsaca->id,
                        'estatus'       => 0,
                        'estatus_admin' => 1,
                        'ult_proce'     => Carbon::yesterday(),
                        'henvio'        => '00:00:00' 
                    ]);

                }
            }
        }
    }
}
