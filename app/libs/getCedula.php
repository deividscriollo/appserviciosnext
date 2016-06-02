<?php 

namespace App\libs;
/* ------------------------------------------------------------ Include lib ----------------------------------------------------*/
	
	//require_once('nusoap/lib/nusoap.php');
include_once('simple_html_dom.php');

/* --------------------------------------- Instancias, consumir sri usando curt modo desarrollo --------------------------------*/
	class DatosCedula {

		var $user_agent = array();
		var $url;
		var $proxy;
		function __construct() {
			$this->url = "https://app03.cne.gob.ec/domicilioelectoral/Default.aspx";					
			$user_agent[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; FDM)";
			$user_agent[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; Avant Browser [avantbrowser.com]; Hotbar 4.4.5.0)";
			$user_agent[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en; rv:1.8.1.14) Gecko/20080409 Camino/1.6 (like Firefox/2.0.0.14)";
			$user_agent[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Version/3.1 Safari/525.13";
			$user_agent[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; NeosBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
			$user_agent[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; es-ES; rv:1.8.1) Gecko/20061010 Firefox/2.0";
			$this->user_agent = $user_agent;
		}
		/* -------------------- inicializacion de direccion funcionamienbto method_exists(object, method_name) -----------------*/
		function method_curt_cedula($cedula) {
			$rnd = rand(0, count($this->user_agent)-1);
			$agent = $this->user_agent[$rnd];
			//define('POSTVARS', 'pagina=resultado&opcion=1&texto='. $ruc );
			$post = '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUJNjQ0MDA5OTE3D2QWAgIDD2QWBAIDD2QWAgIJDzwrAAkAZAIHDxYCHgdWaXNpYmxlaGRkXxohJvk%2FFRc%2Fmd7g%2BgEPDjAvA8A%3D&__VIEWSTATEGENERATOR=5DC121C0&__EVENTVALIDATION=%2FwEWEAKyzs3OAwKT%2F7%2BkBwKvtu%2FvDgKVq7KvCAKW3tOADQLgi9DGAQLD4bK5DgL07IzDBwK6sdbUCwLI4efODQLY2bGaDQK0z86qDQLkhMqaAQL3iPOkDQKSleCiDAKg1%2FfwB9u82Xww59GgLwq%2B%2F7Ir33k37zrf&dropTipo=C&txtCriterio='. $cedula.'&btnConsultar=Consultar&hdnCriterio=&_lat=&_lon=&_recinto=&_junta=&_direccion=&_provincia=&_circunscripcion=&_canton=&_parroquia=&_zona=&resultado=FALSE&_codrecinto=';
			//$ch = curl_init("https://declaraciones.sri.gov.ec/facturacion-internet/consultas/publico/ruc-datos2.jspa");
			$ch = curl_init($this->url);			
			//print_r($ch);
			curl_setopt($ch, CURLOPT_POST      ,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				
			//curl_setopt($ch, CURLOPT_POSTFIELDS    , POSTVARS);
			curl_setopt($ch, CURLOPT_POSTFIELDS    , $post);
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
			curl_setopt($ch, CURLOPT_HEADER      ,0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);			

			/// PROXY
			//Si tiene salida a Internet por Proxy, debe poner ip y puerto
			if($this->proxy) {
				curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy['url']);  // '172.20.18.6:8080'
				if(isset($this->proxy['user']) && isset($this->proxy['password'])){
					$cred = $this->proxy['user'].':'.$this->proxy['password'];
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $cred);
				}
				//curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'user:password');
			}
			$res = curl_exec($ch);
			curl_close($ch);
			return $res;
		}
		/* ------------------------------------ verificar existencia del numero de  ruc en la base de datos --------------------*/
		function verificar_existencia_cedula($html){
			$existencia = 'true';
			if(strpos($html, 'La cedula no se encuentra registrado en nuestra base de datos') !== false)
				$existencia = 'false';
			if(strpos($html, 'Error en el Sistema') !== false)
				$existencia = 'false';
			return $existencia;
		}
		/* ------------------------------------------- proceso consulta cedula como empresa ----------------------------------------*/
		function consultar_cedula($cedula) {
			$html = $this->method_curt_cedula($cedula);
			 $encontrado= str_get_html($html)->find('table[id=tblControles] tr td div[id=pnlResults] p span[id=lblNro]', 0)->plaintext;
			if ($encontrado!="0") {
				$html = str_get_html($html);
				$htmlreturn = $html->find('table[id=dlDirElec]', 0);
				$i=0;
				// echo $htmlreturn;
				foreach($html->find('table[id=dlDirElec] table', 0)->find('td span') as $e){
				// echo $e->plaintext."<br>";
					if(strpos($e, 'colspan') !== false){					
					}else{
						if ($e->plaintext) 
							$results[] = $e->plaintext;
						else
							$results[] = 'no disponible';
						$i++;
					}
			    }
			    return   $acumulador = array('nombres_apelidos'=> $results[0],
			    					'cedula'=> $cedula,
									'provincia'=> $results[1], 
									'canton'=> $results[2], 
									'parroquia'=> $results[4], 
									'zona'=> trim($results[5]),
									'valid' => 'true'				
								);
			}else
				return $results[] = array('valid' => 'false');
		}


	}
?>