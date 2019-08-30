<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="300">

        <link rel="shortcut icon" href="favicon.png" />

		<title>Cierre BCP x Sucursal</title>
		<style>
			html{
				width: 100%;
				height: 100%;
			}

			body{
				width: 100%;
				height: 100%;
				background-color: rgba(204, 255, 255, 0.50);
				color: #000000;
				margin: 0px;
			}
		</style>
	</head>
	<body>
        <table style="width:100%; height:100%; text-align:center; padding:2px;" border="1" cellpadding="1" cellspacing="1">
		<tr><td style="width:100%; font-family:Arial Black; font-weight:bold; font-size:30px; color:green;" colspan="13">CIERRE BCP</td></tr>
		<?php
            $banMoneda  = TRUE;
            $banCont    = TRUE;
            $nroCont    = 0;
            $estilo1    = 'background-color: rgba(204, 255, 255, 0.50);';
            $estilo2    = 'background-color: rgba(255, 255, 204, 0.50);';
            $mon_array  = array();
            $sucursales = array();
            $suc_nombre = array('CASA MATRIZ', 'SUCURSAL VILLA MORRA', 'AGENCIA SAN LORENZO', 'SUCURSAL 1 CIUDAD DEL ESTE', 'AGENCIA JEBAI', 'AGENCIA LAI LAI', 'AGENCIA UNIAMERICA', 'AGENCIA RUBIO ÑU', 'AGENCIA KM4', 'SUCURSAL SALTO DEL GUAIRA', 'AGENCIA SALTO DEL GUAIRA', 'SUCURSAL ENCARNACIÓN');
            $suc_array  = array(
                "suc_matriz"            => "192.168.0.200:aliadocambios",
                "suc_villamorra"        => "10.168.196.130:aliadocambios",
                "age_sanlorenzo"        => "10.168.191.130:aliadocambios",
                "suc_ciudaddeleste"     => "10.168.192.138:aliadocambios",
                "age_jebai"             => "10.168.193.130:aliadocambios",
                "age_lailai"            => "10.168.194.130:aliadocambios",
                "age_uniamerica"        => "10.168.199.131:aliadocambios",
                "age_rubionu"           => "10.168.195.130:aliadocambios",
                "age_km4"               => "10.168.190.130:aliadocambios",
                "suc_saltodelguaira"    => "10.168.198.130:aliadocambios",
                "age_saltodelguaira"    => "10.168.197.130:aliadocambios",
                "suc_encarnacion"       => "10.168.189.130:aliadocambios"
            );

            foreach($suc_array as $suc_key => $suc_ip) {
                $str_db         = $suc_ip;
                $str_user       = 'sysdba';
                $str_pass       = 'dorotea';
                $str_connect    = ibase_connect($str_db, $str_user, $str_pass) OR DIE("NO SE CONECTO AL SERVIDOR: ".ibase_errmsg());
                
                $str_query 		= ibase_query("SELECT t1.ID_MONEDA, t2.DESCRIPCION, t1.TCCOMPRABB 
                                                FROM COTIZACIONESMONEDAS t1
                                                INNER JOIN  MONEDAS t2 ON t1.ID_MONEDA = t2.ID_MONEDA
                                                WHERE t1.ID_TIPOCOTIZACION = 2 AND t2.ID_MONEDA <> 7
                                                ORDER BY t1.ID_MONEDA", $str_connect);
                $detalle        = array();

                while ($row01 = ibase_fetch_row($str_query)) {
                    $detalle[] = array("idmoneda" => $row01[0], "moneda" => $row01[1], "importe" => $row01[2]);

                    if ($banCont === TRUE) {
                        $nroCont = $nroCont + 1;
                    }
                }

                if ($banMoneda === TRUE) {
                    $str_query2 = ibase_query("SELECT t1.ID_MONEDA, t1.DESCRIPCION 
                                                FROM MONEDAS t1
                                                WHERE t1.ID_MONEDA <> 7
                                                ORDER BY t1.ID_MONEDA", $str_connect);

                    while ($row02 = ibase_fetch_row($str_query2)) {
                        $detalle2[] = array("idmoneda" => $row02[0], "moneda" => $row02[1]);
                    }

                    $mon_array  = $detalle2;
                    $banMoneda  = FALSE;

                    ibase_free_result($str_query);
                }

                $sucursales[$suc_key]   = $detalle;
                $banCont                = FALSE;

                ibase_free_result($str_query2);
                ibase_close($str_db);
            }

            echo '<tr>';
            echo '<td style="text-align:left; font-family:Arial Black; font-weight:bold; color:black; font-size:16px;  color:green;">MONEDA</td>';
            foreach ($mon_array as $mon_key => $mon_nom) {
                echo '<td style="text-align:center; font-family:Arial Black; font-weight:bold; color:black; font-size:16px; color:green;">'.$mon_nom['moneda'].'</td>';
            }
            echo '</tr>'; 

            $nroRow     = 0;
            //foreach ($suc_nombre as $suc_nom) {
                

                foreach ($sucursales as $suc_key => $suc_val) {
                    if ($nroRow%2 == 0){
                        $estilo = $estilo2;
                    }else{
                        $estilo = $estilo1;
                    }
                    echo '<tr style="'.$estilo.'">';
                    echo '<td style="text-align:left; font-family:Arial Black; font-weight:bold; color:black; font-size:16px; '.$estilo.' color:green;">'.$suc_nombre[$nroRow].'</td>';
                    
                    //$suc_col = $suc_val[$nroRow];
                    foreach ($suc_val as $data_key => $data_val) {
                        echo '<td style="text-align:center; font-family:Arial Black; font-weight:bold; color:black; font-size:20px; '.$estilo.'">'.number_format($data_val['importe'], 2, ',', '.').'</td>';
                    }
                    echo '</tr>';
                    $nroRow = $nroRow + 1;
                }
               
            //}
		?>
        </table>
	</body>
</html>