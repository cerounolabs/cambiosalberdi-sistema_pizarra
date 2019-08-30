<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Cierre BCP - Sucursal Villa Morra</title>
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
			$str_db         = '10.168.196.130:aliadocambios';
			$str_user       = 'sysdba';
			$str_pass       = 'dorotea';
			$str_connect    = ibase_connect($str_db, $str_user, $str_pass) OR DIE("NO SE CONECTO AL SERVIDOR: ".ibase_errmsg());
			
			$str_query 		= ibase_query("SELECT t1.ID_MONEDA, t2.DESCRIPCION, t1.TCCOMPRABB 
											FROM COTIZACIONESMONEDAS t1
											INNER JOIN  MONEDAS t2 ON t1.ID_MONEDA = t2.ID_MONEDA
											WHERE t1.ID_TIPOCOTIZACION = 2 AND t2.ID_MONEDA <> 7
											ORDER BY t1.ID_MONEDA", $str_connect);

			echo '<table style="width:100%; height:100%; text-align:center; padding:2px;" border="1" cellpadding="1" cellspacing="1">';
			echo '<tr><td style="width:100%; font-family:Arial Black; font-weight:bold; font-size:16px; color:green;" colspan="3">SUCURSAL VILLA MORRA</td></tr>';
			
			while ($row01 = ibase_fetch_row($str_query)) {
				echo '<tr>';
				echo '<td style="text-align:left; font-family:Arial Black; font-weight:bold; color:black; font-size:16px;" colspan="2">'.$row01[1].'</td>';
				echo '<td style="text-align:center; font-family:Arial Black; font-weight:bold; color:black; font-size:16px;">'.number_format($row01[2], 2, ',', '.').'</td>';
				echo '</tr>';
			}

			echo '</table>';
			ibase_free_result($str_query);
			ibase_close($str_connect);
		?>
	</body>
</html>