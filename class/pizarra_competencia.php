<?php
	include '../class/function.php';

	$var01	= $_GET['id1'];
	$var02	= $_GET['id2'];

	$result = getSucursalId($var02);

	foreach($result as $data) {
		$varEmp = $data['empresa_nombre'];
		$varSuc	= $data['sucursal_nombre'];
		$varCiu	= $data['ciudad_nombre'];
	}

?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="refresh" content="300">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Pizarra</title>

		<style>
			.color-igual{
				color: #000000;
			}

			.color-baja{
				color: #CC0000;
			}

			.color-sube{
				color: #006633;
			}

			.cotizacion-igual{
				background-color:#fff;
				color: #000000;
			}

			.cotizacion-baja{
				background-color:#fff;
				color: #CC0000;
				-webkit-animation-name: key_varia; /* Safari 4.0 - 8.0 */
				-webkit-animation-duration: 2s; /* Safari 4.0 - 8.0 */
				-webkit-animation-iteration-count: 30; /* Safari 4.0 - 8.0 */
				animation-name: key_varia;
				animation-duration: 2s;
				animation-iteration-count: 30;
			}

			.cotizacion-sube{
				background-color:#fff;
				color: #006633;
				-webkit-animation-name: key_varia; /* Safari 4.0 - 8.0 */
				-webkit-animation-duration: 2s; /* Safari 4.0 - 8.0 */
				-webkit-animation-iteration-count: 30; /* Safari 4.0 - 8.0 */
				animation-name: key_varia;
				animation-duration: 2s;
				animation-iteration-count: 30;
			}

			@-webkit-keyframes key_varia {
				0%   {background-color:#a9a9a9;}
				25%  {background-color:#fff;}
				50%  {background-color:#a9a9a9;}
				75%  {background-color:#fff;}
				100% {background-color:#a9a9a9;}
			}

			@keyframes key_varia {
				0%   {background-color:#a9a9a9;}
				25%  {background-color:#fff;}
				50%  {background-color:#a9a9a9;}
				75%  {background-color:#fff;}
				100% {background-color:#a9a9a9;}
			}
		</style>
	</head>
	<body>	
		
		<table width="100%" border="0" cellpadding="0" cellspacing="2">
			<tr>
				<td style="text-align:center; center-align:center; font-family:Arial Black; font-weight:bold; color:green; background-color:white; font-size:18px;" colspan="3">
					<?php echo $varEmp.' - '.$varSuc; ?>
				</td>
			<tr>
<?php
	$result		= getPizarra2($var01, $var02);

	foreach($result as $data) {
		if ($data['cotizacion_detalle_css_estado'] == 'A') {
			$classCompra = $data['cotizacion_detalle_css_compra'];
			$classVenta	 = $data['cotizacion_detalle_css_venta'];
		} else {
			$classCompra = formatColor($data['cotizacion_detalle_css_compra']);
			$classVenta	 = formatColor($data['cotizacion_detalle_css_venta']);
		}
		
		if ($data['moneda_base_bcp'] == 'PYG' || $data['moneda_relacionada_bcp'] == 'PYG'){
?>
			<tr>
				<td style="text-align: left; font-size:18px; center-align:top; font-family:Arial Black; background-color:white"><?php echo $data['moneda_base_bcp']; ?></td>
				<td style="text-align: right; font-size:22px; center-align:top; font-family:Arial Black;" class="<?php echo $classCompra; ?>"><?php echo number_format($data['cotizacion_detalle_compra'], 0, ',', '.'); ?></td>
				<td style="text-align: right; font-size:22px; center-align:top; font-family:Arial Black;" class="<?php echo $classVenta; ?>"><?php echo number_format($data['cotizacion_detalle_venta'], 0, ',', '.'); ?></td>
			</tr>
<?php
		} else {
?>
			<tr>
				<td style="text-align: left; font-size:18px; center-align:top; font-family:Arial Black; background-color:white"><?php echo $data['moneda_base_bcp'].' vs '.$data['moneda_relacionada_bcp']; ?></td>
				<td style="text-align: right; font-size:22px; center-align:top; font-family:Arial Black;" class="<?php echo $classCompra; ?>"><?php echo number_format($data['cotizacion_detalle_compra'], 3, ',', '.'); ?></td>
				<td style="text-align: right; font-size:22px; center-align:top; font-family:Arial Black;" class="<?php echo $classVenta; ?>"><?php echo number_format($data['cotizacion_detalle_venta'], 3, ',', '.'); ?></td>
			</tr>
<?php
		}

		$varFecHor = $data['cotizacion_detalle_fecha_pizarra'];
	}

	if (isset($varFecHor)) {
?>
			<tr>
				<td style="text-align:center; center-align:top; font-family:Arial Black; font-weight:bold; color:green; background-color:white; font-size:18px;" colspan="3">
					<?php echo date("d-m-Y H:i", strtotime($varFecHor)); ?>
				</td>
			<tr>
<?php
	}
?>
		</table>
	</body>
</html>
