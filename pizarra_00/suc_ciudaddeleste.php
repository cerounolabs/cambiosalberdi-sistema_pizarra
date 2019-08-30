<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="refresh" content="5">
		<title>Pizarra - Sucursal Ciudad del Este</title>
		<style>
			html{
				width: 100%;
				height: 100%;
			}

			body{
				width: 100%;
				height: 100%;
				background-color: rgba(255, 255, 204, 0.50);
				color: #000000;
				margin: 0px;
			}
		</style>
	</head>
	<body>
		<?php
			ibase_connect("10.168.192.138:aliadocambios", "sysdba", "dorotea");
			
			echo '<table style="width:100%; height:100%; text-align:center; padding:2px;" border="1" cellpadding="1" cellspacing="1">';
			echo '<tr><td style="width:100%; font-family:Arial Black; font-weight:bold; font-size:22px; color:green;" colspan="3">SUCURSAL 1 CIUDAD DEL ESTE</td></tr>';
			
			$Q = ibase_query("SELECT * FROM COTIZACIONESMONEDAS WHERE ID_TIPOCOTIZACION = 1");

			while ($R = ibase_fetch_object($Q)) {
				switch ($R->ID_MONEDA) {
					case 3:
						$DS_MONEDA	="Dólar";
						$VL_MONEDAC	= $R->TCCOMPRABB;
						$VL_MONEDAV	= $R->TCVENTABB;
						$VL_DOLARC	= $R->TCCOMPRABB;
						$VL_DOLARV	= $R->TCVENTABB;
						break;

					case 4:
						$DS_MONEDA	= "Real";
						$VL_MONEDAC	= $R->TCCOMPRABB;
						$VL_MONEDAV	= $R->TCVENTABB;
						$VL_REALC	= $R->TCCOMPRABB;
						$VL_REALV	= $R->TCVENTABB;
						break;

					case 6:
						$DS_MONEDA	= "Euro";
						$VL_MONEDAC	= $R->TCCOMPRABB;
						$VL_MONEDAV	= $R->TCVENTABB;
						$VL_EUROC	= $R->TCCOMPRABB;
						$VL_EUROV	= $R->TCVENTABB;
						break;
						
					case 5:
						$DS_MONEDA	= "Peso";
						$VL_MONEDAC	= $R->TCCOMPRABB;
						$VL_MONEDAV	= $R->TCVENTABB;
						$VL_PESOC	= $R->TCCOMPRABB;
						$VL_PESOV	= $R->TCVENTABB;
						break;
				}

				$color='green';
			
				$ini_moneda='<td style="text-align:left; 	font-family:Arial Black; font-weight:bold; color:black; font-size:22px;">';
				$ini_compra='<td style="text-align:center; 	font-family:Arial Black; font-weight:bold; color:black; font-size:22px;">';
				$ini_venta= '<td style="text-align:center; 	font-family:Arial Black; font-weight:bold; color:black; font-size:22px;">';
				$fin_moneda="</td>";
				
				if (($R->ID_MONEDA) == 3 OR ($R->ID_MONEDA) == 4 OR ($R->ID_MONEDA) == 5 OR ($R->ID_MONEDA) == 6) {
					if (($R->TCCCOMPRABB != 0) OR ($R->TCVENTABB != 0)) {
						echo "<tr>";
						echo "$ini_moneda $DS_MONEDA $fin_moneda";
						echo "$ini_compra".number_format($VL_MONEDAC, 0, ',', '.')."$fin_moneda";
						echo "$ini_venta".number_format($VL_MONEDAV, 0, ',', '.')."$fin_moneda";
						echo "</tr>";
					}
				}
			}

			//---> Dolar x Real
			$buscareal 	= ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 1 AND ID_MONEDA = 4");
			$R 			= ibase_fetch_object($buscareal);
			$realc 		= $R->PARIDAD_C;
			$realv 		= $R->PARIDAD_V;
			echo "<tr>";
			echo "$ini_moneda D$ x R$ $fin_moneda";
			echo "$ini_compra".number_format($realc, 3, ',', '.')."$fin_moneda";
			echo "$ini_compra".number_format($realv, 3, ',', '.')."$fin_moneda";
			echo "</tr>";
		
			//---> Dolar x EURO
			$buscaeuro 	= ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 3 AND ID_MONEDA = 3");
			$R 			= ibase_fetch_object($buscaeuro);
			$euroc 		= $R->PARIDAD_C;
			$eurov 		= $R->PARIDAD_V;
			echo "<tr>";
			echo "$ini_moneda D$ x Eu $fin_moneda";
			echo "$ini_compra".number_format($euroc, 3, ',', '.')."$fin_moneda";
			echo "$ini_compra".number_format($eurov, 3, ',', '.')."$fin_moneda";
			echo "</tr>";	
		
			//---> Dolar x Peso
			$buscarpeso = ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 1 AND ID_MONEDA = 5");
			$P 			= ibase_fetch_object($buscarpeso);
			$pesoc 		= $P->PARIDAD_C;
			$pesov 		= $P->PARIDAD_V;
			echo "<tr>";
			echo "$ini_moneda D$ x P$ $fin_moneda";
			echo "$ini_compra".number_format($pesoc, 3, ',', '.')."$fin_moneda";
			echo "$ini_compra".number_format($pesov, 3, ',', '.')."$fin_moneda";
			echo "</tr>";
		
			//---> Dolar cheque
			$buscadolarch = ibase_query("SELECT * FROM COTIZACIONESMONEDAS WHERE ID_TIPOCOTIZACION = 1 and id_cotizacionmoneda=1 order by ID_MONEDA");
			$R 			  = ibase_fetch_object($buscadolarch);
			$dolarchc 	  = $R->TCCOMPRACH;
			$dolarchv 	  = $R->TCVENTACH;
			echo "<tr>";
			echo "$ini_moneda D$ Ch. P/L $fin_moneda";
			echo "$ini_compra".number_format($dolarchc,0,',','.')."$fin_moneda";
			echo "$ini_compra".number_format($dolarchv,0,',','.')."$fin_moneda";
			echo "</tr>";	
		
			//---> Cheque Dolar x Real
			$buscachreal= ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 1 AND ID_MONEDA = 4");
			$R 			= ibase_fetch_object($buscachreal);
			$realcc		= $R->PARIDAD_C_CH;
			$realvc		= $R->PARIDAD_V_CH;
			echo "<tr>";
			echo "$ini_moneda D$ Ch x R$ $fin_moneda";
			echo "$ini_compra".number_format($realcc, 3, ',', '.')."$fin_moneda";
			echo "$ini_compra".number_format($realvc, 3, ',', '.')."$fin_moneda";
			echo "</tr>";
			echo "</table>";	
		?>
	</body>
</html>