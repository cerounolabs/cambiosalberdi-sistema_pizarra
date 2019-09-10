<?php
    function getRealIP() {
        if (isset($_SERVER["HTTP_CLIENT_IP"])){
            return $_SERVER["HTTP_CLIENT_IP"];
        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){
            return $_SERVER["HTTP_X_FORWARDED"];
        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }elseif (isset($_SERVER["HTTP_FORWARDED"])){
            return $_SERVER["HTTP_FORWARDED"];
        }else{
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    function get_curl($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $dataJSON   = curl_exec($ch);
        curl_close($ch);
        $result     = json_decode($dataJSON, TRUE);
        return $result;
    }

    function formatColor($var01){
        switch ($var01) {
            case 'cotizacion-igual':
                $result = 'color-igual';
                break;
            
            case 'cotizacion-sube':
                $result = 'color-sube';
                break;
            
            case 'cotizacion-baja':
                $result = 'color-baja';
                break;
        }

        return $result;              
    }

    function post_curl($url, $data, $headers){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function getConexion() {
        $host	        = "10.168.196.187";
        $user 	        = "tablero_admin";
        $pass 	        = "t4bl3r02019";
        $db 	        = "tablero";
        $mysqli         = new mysqli($host, $user, $pass, $db);
        $mysqli->set_charset("utf8");

        if ($mysqli->connect_errno) {
            echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        return $mysqli;
    }

    function getSucursalId($var01){
        $str_conn       = getConexion();
        $str_qry        = "SELECT
        c.codCiudad         AS      ciudad_codigo,
        c.nomCiudad         AS      ciudad_nombre,
        b.codEmpresa        AS      empresa_codigo,
        b.nomEmpresa        AS      empresa_nombre,
        b.urlEmpresa        AS      empresa_url,
        a.codEstado         AS      estado_codigo,
        a.codSucursal       AS      sucursal_codigo,
        a.nomSucursal       AS      sucursal_nombre

        FROM SUCURSAL a
        INNER JOIN EMPRESA b ON a.codEmpresa = b.codEmpresa
        INNER JOIN CIUDAD c ON a.codCiudad = c.codCiudad
        WHERE a.codSucursal = '$var01'

        ORDER BY b.nomEmpresa, c.nomCiudad, a.nomSucursal";

        if ($query = $str_conn->query($str_qry)) {
            while($row00 = $query->fetch_assoc()) {
                if ($row00['estado_codigo'] == 'A'){
                    $estado = 'ACTIVO';
                } else {
                    $estado = 'INACTIVO';
                }

                $result[]  = array(
                    "ciudad_codigo"     => $row00['ciudad_codigo'],
                    "ciudad_nombre"     => $row00['ciudad_nombre'],
                    "empresa_codigo"    => $row00['empresa_codigo'],
                    "empresa_nombre"    => $row00['empresa_nombre'],
                    "empresa_url"       => $row00['empresa_url'],
                    "estado_codigo"     => $row00['estado_codigo'],
                    "estado_nombre"     => $estado,
                    "sucursal_codigo"   => $row00['sucursal_codigo'],
                    "sucursal_nombre"   => $row00['sucursal_nombre']
                );
            }

            $query->free();
        }        

        $str_conn->close();

        return $result;
    }

    function getPizarra2($var01, $var02){
        $str_conn       = getConexion();
        $str_qry        = "SELECT
        i.codCotizacionDetalle AS      cotizacion_detalle_codigo,
        i.cssEstado            AS      cotizacion_detalle_css_estado,
        i.cssCompra            AS      cotizacion_detalle_css_compra,
        i.cssVenta             AS      cotizacion_detalle_css_venta,
        i.impCompra            AS      cotizacion_detalle_compra,
        i.impVenta             AS      cotizacion_detalle_venta,
        i.fecPizarra           AS      cotizacion_detalle_fecha_pizarra,
        i.fecAlta              AS      cotizacion_detalle_alta_fecha,
        i.horAlta              AS      cotizacion_detalle_alta_hora,
        i.usuAlta              AS      cotizacion_detalle_alta_usuario,

        h.codMoneda            AS      moneda_relacionada_codigo,
        h.nomMoneda            AS      moneda_relacionada_nombre,
        h.bcpMoneda            AS      moneda_relacionada_bcp,
        h.patMoneda            AS      moneda_relacionada_path,

        g.codMoneda            AS      moneda_base_codigo,
        g.nomMoneda            AS      moneda_base_nombre,
        g.bcpMoneda            AS      moneda_base_bcp,
        g.patMoneda            AS      moneda_base_path,
        
        f.codCotizacion        AS      cotizacion_codigo,

        e.codCiudad             AS      ciudad_codigo,
        e.nomCiudad             AS      ciudad_nombre,
        d.codEmpresa            AS      empresa_codigo,
        d.nomEmpresa            AS      empresa_nombre,
        d.urlEmpresa            AS      empresa_url,
        c.codSucursal           AS      sucursal_codigo,
        c.nomSucursal           AS      sucursal_nombre,
        
        b.codTableroDetalle     AS      tablero_detalle_codigo,

        a.codEstado             AS      estado_codigo,
        a.codTablero            AS      tablero_codigo,
        a.nomTablero            AS      tablero_nombre

        FROM TABLERO a
        INNER JOIN TABLERODETALLE b ON a.codTablero = b.codTablero
        INNER JOIN SUCURSAL c ON b.codSucursal = c.codSucursal
        INNER JOIN EMPRESA d ON c.codEmpresa = d.codEmpresa
        INNER JOIN CIUDAD e ON c.codCiudad = e.codCiudad

        INNER JOIN COTIZACION f ON c.codSucursal = f.codSucursal
        INNER JOIN MONEDA g ON f.codMonedaBase = g.codMoneda
        INNER JOIN MONEDA h ON f.codMonedaRelacion = h.codMoneda
        INNER JOIN COTIZACIONDETALLE i ON f.codCotizacion = i.codCotizacion

        WHERE a.codTablero = '$var01' AND c.codSucursal = '$var02' AND b.codEstado = 'A' AND i.CodEstado = 'A' AND f.CodEstado = 'A'

        ORDER BY d.nomEmpresa, c.nomSucursal, e.nomCiudad";

        if ($query = $str_conn->query($str_qry)) {
            while($row00 = $query->fetch_assoc()) {
                if ($row00['estado_codigo'] == 'A'){
                    $estado = 'ACTIVO';
                } else {
                    $estado = 'INACTIVO';
                }

                $result[]  = array(
                    "estado_codigo"                             => $row00['estado_codigo'],
                    "estado_nombre"                             => $estado,
                    "tablero_codigo"                            => $row00['tablero_codigo'],
                    "tablero_nombre"                            => $row00['tablero_nombre'],

                    "tablero_detalle_codigo"                    => $row00['tablero_detalle_codigo'],

                    "ciudad_codigo"                             => $row00['ciudad_codigo'],
                    "ciudad_nombre"                             => $row00['ciudad_nombre'],
                    "empresa_codigo"                            => $row00['empresa_codigo'],
                    "empresa_nombre"                            => $row00['empresa_nombre'],
                    "empresa_url"                               => $row00['empresa_url'],
                    "sucursal_codigo"                           => $row00['sucursal_codigo'],
                    "sucursal_nombre"                           => $row00['sucursal_nombre'],

                    "cotizacion_codigo"                         => $row00['cotizacion_codigo'],
                    "moneda_base_codigo"                        => $row00['moneda_base_codigo'],
                    "moneda_base_nombre"                        => $row00['moneda_base_nombre'],
                    "moneda_base_bcp"                           => $row00['moneda_base_bcp'],
                    "moneda_base_path"                          => $row00['moneda_base_path'],
                    "moneda_relacionada_codigo"                 => $row00['moneda_relacionada_codigo'],
                    "moneda_relacionada_nombre"                 => $row00['moneda_relacionada_nombre'],
                    "moneda_relacionada_bcp"                    => $row00['moneda_relacionada_bcp'],
                    "moneda_relacionada_path"                   => $row00['moneda_relacionada_path'],

                    "cotizacion_detalle_codigo"                 => $row00['cotizacion_detalle_codigo'],
                    "cotizacion_detalle_css_estado"             => $row00['cotizacion_detalle_css_estado'],
                    "cotizacion_detalle_css_compra"             => $row00['cotizacion_detalle_css_compra'],
                    "cotizacion_detalle_css_venta"              => $row00['cotizacion_detalle_css_venta'],
                    "cotizacion_detalle_compra"                 => $row00['cotizacion_detalle_compra'],
                    "cotizacion_detalle_venta"                  => $row00['cotizacion_detalle_venta'],
                    "cotizacion_detalle_fecha_pizarra"          => $row00['cotizacion_detalle_fecha_pizarra'],
                    "cotizacion_detalle_alta_fecha"             => $row00['cotizacion_detalle_alta_fecha'],
                    "cotizacion_detalle_alta_hora"              => $row00['cotizacion_detalle_alta_hora'],
                    "cotizacion_detalle_alta_usuario"           => $row00['cotizacion_detalle_alta_usuario']
                );
            }

            $query->free();
        }        

        $str_conn->close();

        return $result;
    }

    function extraerCambiosChaco($resultJSON, $var01, $var02, $var03, $var04, $var05, $var06, $var07, $var08){
        foreach($resultJSON as $data) {
            $codBCP = $data['isoCode'];
            $fecHor = substr(str_replace('T', ' ', $data['updatedAt']), 0, 16);
            $impCom = $data['purchasePrice'];
            $impVen = $data['salePrice'];
            $arbCom = $data['purchaseArbitrage'];
            $arbVen = $data['saleArbitrage'];
          
            switch ($codBCP) {
                case 'USD':
                    $insert = setCotizacionDetalle('A', $var01, 1, $impCom, $impVen, $fecHor, $var08);
                    break;
                
                case 'BRL':
                    $insert = setCotizacionDetalle('A', $var02, 1, $impCom, $impVen, $fecHor, $var08);
                    $insert = setCotizacionDetalle('A', $var03, 1, $arbCom, $arbVen, $fecHor, $var08);
                    break;
    
                case 'ARS':
                    $insert = setCotizacionDetalle('A', $var04, 1, $impCom, $impVen, $fecHor, $var08);
                    $insert = setCotizacionDetalle('A', $var05, 1, $arbCom, $arbVen, $fecHor, $var08);
                    break;
    
                case 'EUR':
                    $insert = setCotizacionDetalle('A', $var06, 1, $impCom, $impVen, $fecHor, $var08);
                    $insert = setCotizacionDetalle('A', $var07, 1, $arbCom, $arbVen, $fecHor, $var08);
                    break;
            }
        }
    }

    function setCotizacionDetalle($var01, $var02, $var03, $var04, $var05, $var06, $sysUse){
        $str_conn       = getConexion();
        $var07          = date('Y-m-d');
        $var08          = date('H:i:s');
        $result         = '';
        $str_qry        = "SELECT
        a.impCompra     AS      importe_compra,
        a.impVenta      AS      importe_venta
        FROM COTIZACIONDETALLE a
        WHERE a.codCotizacion = '$var02' AND a.codCotizacionTipo = '$var03' AND a.codEstado = '$var01'";

        if ($query = $str_conn->query($str_qry)) {
            while($row00 = $query->fetch_assoc()) {
                if (($row00['importe_compra'] != $var04) || ($row00['importe_venta'] != $var05)){
                    $str_qry = "UPDATE COTIZACIONDETALLE SET codEstado = 'H' WHERE codCotizacion = '$var02' AND codCotizacionTipo = '$var03' AND codEstado = '$var01'";
                    
                    if ($str_conn->query($str_qry) === TRUE) {
                        $str_qry = "INSERT INTO COTIZACIONDETALLE(codEstado, codCotizacion, codCotizacionTipo, impCompra, impVenta, fecPizarra, fecAlta, horAlta, usuAlta) VALUES ('".$var01."', '$var02', '$var03', '$var04', '$var05', '".$var06."', '".$var07."', '".$var08."', '".$sysUse."')";
                        
                        if ($str_conn->query($str_qry) === TRUE) {
                            $result = $str_conn->insert_id;
                            $result = 'Se inserto el registro de forma correcta';
                        }
                    }
                } else {
                    $str_qry = "UPDATE COTIZACIONDETALLE SET cssEstado = 'H' WHERE codCotizacion = '$var02' AND codCotizacionTipo = '$var03' AND codEstado = '$var01'";

                    if ($str_conn->query($str_qry) === TRUE) {
                        $result = 'Se actualizo el registro de forma correcta';
                    }
                }
            }
            $query->free();
        }

        $str_conn->close();

        return $result;
    }
?>