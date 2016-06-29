<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Empresas;
use App\Sucursales;
use App\Personas;
use App\libs\DatosCedula;
use App\libs\DatosMovil;
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

    public function estado_fac_electronica(Request $request)
    {
         // $getsri   = new getsri();
         // $getsri->estado_factura_electronica($request->input('clave'));
         // return response()->json(["estado"=>$getsri]);
         
            $wsdl = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl"; // Ambiente Produccion
            $client = new \SoapClient($wsdl, array('encoding'=>'UTF-8'));
            $res = $client->AutorizacionComprobante(array('claveAccesoComprobante'=> $request->input('clave')));
            // print_r($res->RespuestaAutorizacionComprobante);
            return response()->json([$res->RespuestaAutorizacionComprobante]);
    }

     public function consultar_Movil(Request $request)
    {
            $movilclass  = new DatosMovil(); 
            $resultado=$movilclass->verificar_existencia_movil($request->input('celular')); 
            return response()->json($resultado); 
        
    }
}
