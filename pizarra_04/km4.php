<HTML>
	<meta http-equiv="REFRESH" content="5">
	<HEAD>
		<TITLE>Pizarra</TITLE>
		
	</HEAD>
		
		
	<BODY>
		
		<?php
		
			echo '<TABLE WIDTH="100%" BORDER="0" cellpadding="0" cellspacing="0">';
			echo '<td align="center" style="center-align: top; font-family: Arial Black; font-weight: bold; color: green; background-color: white";><div style="text-align: center; font-size:22px">KM 4</font></td>';
		?>
		<!---
		<table> 
				
			<tr>
				<td align="center">
					www.cambiosalberdi.com
				</td>
			</tr>
		</table>  
		--->
			
		<?php
			//ibase_connect("10.168.192.138:aliadocambios", "SYSDBA", "masterkey");
			ibase_connect("10.168.190.130:aliadocambios", "sysdba", "dorotea");
			
			echo '<TABLE WIDTH="100%" BORDER="0" cellpadding="0" cellspacing="0">';
			//echo '<tr><td align="center" whidth="10%">. </td>';
			//echo '<td align="center" whidth="50%"><font face="Arial Black" size="2" bgcolor="white" font color="black">Moneda</font></td>';
			//echo '<td align="center" whidth="20%"><font face="Arial Black" size="2" bgcolor="white" font color="#8A0808">Compra</font></td>';
			//echo '<td align="center" whidth="20%"><font face="Arial Black" size="2" bgcolor="white" font color="#0B0B61">Venta</font></td>';
			//echo '</tr>';

			
			$Q = ibase_query("SELECT * FROM COTIZACIONESMONEDAS WHERE ID_TIPOCOTIZACION = 1");

			while ($R = ibase_fetch_object($Q))
			{
				switch ($R->ID_MONEDA)
				{
					case 3:
						$DS_MONEDA="Dolar ";
						$VL_MONEDAC=$R->TCCOMPRABB;
						$VL_MONEDAV=$R->TCVENTABB;
						$VL_DOLARC=$R->TCCOMPRABB;
						$VL_DOLARV=$R->TCVENTABB;
						
						break;
					case 4:
						$DS_MONEDA="Real";
						$VL_MONEDAC=$R->TCCOMPRABB;
						$VL_MONEDAV=$R->TCVENTABB;
						$VL_REALC=$R->TCCOMPRABB;
						$VL_REALV=$R->TCVENTABB;
						
						break;
					
					case 6:
						$DS_MONEDA="Euro";
						$VL_MONEDAC=$R->TCCOMPRABB;
						$VL_MONEDAV=$R->TCVENTABB;
						$VL_EUROC=$R->TCCOMPRABB;
						$VL_EUROV=$R->TCVENTABB;
						
						break;
					case 5:
						$DS_MONEDA="Peso";
						$VL_MONEDAC=$R->TCCOMPRABB;
						$VL_MONEDAV=$R->TCVENTABB;
						$VL_PESOC=$R->TCCOMPRABB;
						$VL_PESOV=$R->TCVENTABB;
						
						break;
				}
				$color='green';
			
				$ini_moneda='<td style="center-align: top; font-family: Arial Black; font-weight: bold; color: black; background-color: white";><div style="text-align: left; font-size:22px">';
				$ini_compra='<td style="center-align: top; font-family: Arial Black; font-weight: bold; color: black; background-color: white";><div style="text-align: center; font-size:22px">';
				$ini_venta='<td style="center-align: top; font-family: Arial Black; font-weight: bold; color: black; background-color: white";><div style="text-align: center; font-size:22px">';
				$fin_moneda="</text></td>";
				$fin_cambio="</font></div></td>";
				
				if(($R->ID_MONEDA) == 3 OR ($R->ID_MONEDA) == 4 OR ($R->ID_MONEDA) == 5 OR ($R->ID_MONEDA) == 6)
				{
					if(($R->TCCCOMPRABB != 0) OR ($R->TCVENTABB != 0))
					{
						echo "<TR>";
						//echo "<td align='center'> $BANDERA </td>";
						echo "$ini_moneda $DS_MONEDA $fin_moneda";
						echo "$ini_compra".number_format($VL_MONEDAC,0,',','.')."$fin_cambio";
						echo "$ini_venta".number_format($VL_MONEDAV,0,',','.')."$fin_cambio";
						echo "</TR>";
					}
				}
			}

			//---> Dolar x Real
			 $buscareal = ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 1 AND ID_MONEDA = 4");
			 $R = ibase_fetch_object($buscareal);
			 $realc = $R->PARIDAD_C;
			 $realv = $R->PARIDAD_V;

				 echo "<TR>";
				 echo "$ini_moneda D$ x R$ $fin_moneda";
				 echo "$ini_compra".number_format($realc,3,',','.')."$fin_cambio";
				 echo "$ini_compra".number_format($realv,3,',','.')."$fin_cambio";
				 echo "</TR>";
			
			
				 
				 //---> Dolar x Euro
			$buscaeuro = ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 3 AND ID_MONEDA = 3");
			$R = ibase_fetch_object($buscaeuro);
			$euroc = $R->PARIDAD_C;
			$eurov = $R->PARIDAD_V;

				echo "<TR>";
				echo "$ini_moneda D$ x Eu $fin_moneda";
				echo "$ini_compra".number_format($euroc,3,',','.')."$fin_cambio";
				echo "$ini_compra".number_format($eurov,3,',','.')."$fin_cambio";
				echo "</TR>";	
		
		//__> Dolar x Peso
			$buscarpeso = ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 1 AND ID_MONEDA = 5");
			 $P = ibase_fetch_object($buscarpeso);
			 $pesoc = $P->PARIDAD_C;
			 $pesov = $P->PARIDAD_V;

				 echo "<TR>";
				 echo "$ini_moneda D$ x P$ $fin_moneda";
				 echo "$ini_compra".number_format($pesoc,3,',','.')."$fin_cambio";
				 echo "$ini_compra".number_format($pesov,3,',','.')."$fin_cambio";
				 echo "</TR>";

				 //---> Dolar cheque
			$buscadolarch = ibase_query("SELECT * FROM COTIZACIONESMONEDAS WHERE ID_TIPOCOTIZACION = 1 and id_cotizacionmoneda=1 order by ID_MONEDA");
			$R 			  = ibase_fetch_object($buscadolarch);
			$dolarchc 	  = $R->TCCOMPRACH;
			$dolarchv 	  = $R->TCVENTACH;
			echo "<tr>";
			echo "$ini_moneda D$ Ch. P/L $fin_moneda";
			echo "$ini_compra".number_format($dolarchc,0,',','.')."$fin_cambio";
			echo "$ini_compra".number_format($dolarchv,0,',','.')."$fin_cambio";
			echo "</tr>";	

		//---> Cheque Dolar x Real
			$buscachreal= ibase_query("SELECT * FROM PARIDAD WHERE ID_COTIZACIONMONEDA = 1 AND ID_MONEDA = 4");
			$R 			= ibase_fetch_object($buscachreal);
			$realcc		= $R->PARIDAD_C_CH;
			$realvc		= $R->PARIDAD_V_CH;
				echo "<TR>";
				echo "$ini_moneda D$ Ch x R$ $fin_moneda";
				echo "$ini_compra".number_format($realcc,3,',','.')."$fin_cambio";
				echo "$ini_compra".number_format($realvc,3,',','.')."$fin_cambio";
				echo "</TR>";
			echo "</TABLE>";

			
		?>
		
		
	</BODY>
</HTML>
