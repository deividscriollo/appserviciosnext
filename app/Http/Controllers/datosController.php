<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Empresas;
use App\Sucursales;
use App\Personas;
use App\libs\DatosCedula;
use App\libs\getSRI;
class datosController extends Controller
{
    public function getDatos(Request $request)
    {
        if ($request->input('tipodocumento') == "RUC") {
            $getsri   = new getsri();
            $datosSri = array_map('utf8_encode', $getsri->consultar_ruc($request->input('nrodocumento')));
            $establecimientos=$getsri->establecimientoSRI($request->input('nrodocumento'));
            // foreach ($establecimientos['adicional'] as $representante) {
            //     print_r($representante);
            // }
            return response()->json(array('datosEmpresa' =>$datosSri,'establecimientos'=>$establecimientos));
        } else {
            $cedulaclass  = new DatosCedula();
            $datospersona = array_map('utf8_encode', $cedulaclass->consultar_cedula($request->input('nrodocumento')));
            return response()->json(array_map('utf8_encode', $datospersona));
        }
    }
}
