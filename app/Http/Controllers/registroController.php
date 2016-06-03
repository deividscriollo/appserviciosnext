<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Empresas;
use App\Personas;
use App\libs\DatosCedula;
use App\libs\Funciones;
use App\libs\getSRI;
class registroController extends Controller
{
    public function getDatos(Request $request)
    {
        if ($request->input('tipodocumento') == "RUC") {
            $getsri   = new getsri();
            $datosSri = array_map('utf8_encode', $getsri->consultar_ruc($request->input('nrodocumento')));
            return response()->json(array_map('utf8_encode', $datosSri));
        } else {
            $cedulaclass  = new DatosCedula();
            $datospersona = array_map('utf8_encode', $cedulaclass->consultar_cedula($request->input('nrodocumento')));
            return response()->json(array_map('utf8_encode', $datospersona));
        }
    }
    public function registrarEmpresa(Request $request)
    {
        $getsri   = new getsri();
        $datosSri = array_map('utf8_encode', $getsri->consultar_ruc($request->input('ruc')));
        if ($datosSri['valid'] == "true") {
            $funciones                    = new Funciones();
            $tabla                        = new Empresas();
            $tabla->id                    = $funciones->generarId();
            $tabla->razon_social          = $datosSri['razon_social'];
            $tabla->nombre_comercial      = $datosSri['nombre_comercial'];
            $tabla->estado_contribuyente  = $datosSri['estado_contribuyente'];
            $tabla->tipo_contribuyente    = $datosSri['tipo_contribuyente'];
            $tabla->obligado_contabilidad = $datosSri['obligado_llevar_contabilidad'];
            $tabla->actividad_economica   = $datosSri['actividad_economica'];
            // $tabla->telefono=$datosSri['telefono'];
            // $tabla->correo=$datosSri['correo'];
            $tabla->telefono              = "---telefono";
            $tabla->correo                = "---correo";
            $tabla->save();
            return response()->json($datosSri);
            // return response()->json(array_map('utf8_encode', $datosSri));
            return array(
                "mensaje" => "Empresa registrada correctamente"
            );
        } else {
            return response()->json(array(
                "mensaje" => "Ruc Incorrecto"
            ));
        }
    }
    public function registrarPersona(Request $request)
    {
        $cedulaclass  = new DatosCedula();
        $datospersona = array_map('utf8_encode', $cedulaclass->consultar_cedula($request->input('cedula')));
        if ($datospersona['valid'] == "true") {
            $funciones                = new Funciones();
            $tabla                    = new Personas();
            $tabla->id                = $funciones->generarId();
            $tabla->Nombres_apellidos = $datospersona['nombres_apelidos'];
            $tabla->cedula            = $datospersona['cedula'];
            $tabla->provincia         = $datospersona['provincia'];
            $tabla->canton            = $datospersona['canton'];
            $tabla->parroquia         = $datospersona['parroquia'];
            $tabla->zona              = $datospersona['zona'];
            // $tabla->telefono=$datospersona['telefono'];
            // $tabla->correo=$datospersona['correo'];
            $tabla->telefono          = "---telefono";
            $tabla->correo            = "---correo";
            $tabla->save();
            // return response()->json(array_map('utf8_encode', $datospersona));
            return array(
                "mensaje" => "Persona registrada correctamente"
            );
        } else {
            return response()->json(array(
                "mensaje" => "Cedula incorrecta"
            ));
        }
    }
}
