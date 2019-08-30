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
		<meta http-equiv="refresh" content="5">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Pizarra</title>
	</head>
	<body>	
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td style="text-align:center; center-align:center; font-family:Arial Black; font-weight:bold; color:green; background-color:white; font-size:18px;" colspan="3">
					<?php echo $varEmp.' - '.$varSuc; ?>
				</td>
			<tr>
<?php
	$result		= getPizarra2($var01, $var02);

	foreach($result as $data) {
		if ($data['moneda_base_bcp'] == 'PYG' || $data['moneda_relacionada_bcp'] == 'PYG'){
?>
			<tr>
				<td style="text-align: left; font-size:26px; center-align:top; font-family:Arial Black; font-weight:bold; color:black; background-color:white"><?php echo $data['moneda_base_nombre']; ?></td>
				<td style="text-align: right; font-size:26px; center-align:top; font-family:Arial Black; color:black; background-color:white"><?php echo number_format($data['cotizacion_detalle_compra'], 0, ',', '.'); ?></td>
				<td style="text-align: right; font-size:26px; center-align:top; font-family:Arial Black; color:black; background-color:white"><?php echo number_format($data['cotizacion_detalle_venta'], 0, ',', '.'); ?></td>
			</tr>
<?php
		} else {
?>
			<tr>
				<td style="text-align: left; font-size:26px; center-align:top; font-family:Arial Black; font-weight:bold; color:black; background-color:white"><?php echo $data['moneda_base_bcp'].' vs '.$data['moneda_relacionada_bcp']; ?></td>
				<td style="text-align: right; font-size:26px; center-align:top; font-family:Arial Black; color:black; background-color:white"><?php echo number_format($data['cotizacion_detalle_compra'], 3, ',', '.'); ?></td>
				<td style="text-align: right; font-size:26px; center-align:top; font-family:Arial Black; color:black; background-color:white"><?php echo number_format($data['cotizacion_detalle_venta'], 3, ',', '.'); ?></td>
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
