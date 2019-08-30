<?php
    include 'function.php';
    
    $hora = date("H");
    $dia  = date("N");

    if (intval($hora) >= 5 && intval($hora) <= 17 && $dia >=1 && $dia <= 6 ) {
        if (intval($hora) >= 5 && intval($hora) <= 17 && $dia >=1 && $dia <= 5 ) {
            //ADRIAN JARA
            $result = get_curl('http://www.cambioschaco.com.py/api/branch_office/9/exchange');
            extraerCambiosChaco($result['items'], 15, 16, 19, 17, 20, 18, 21, 'SISTEMA');

            sleep(10);

            //MONSEÑOR RODRIGUEZ 
            $result = get_curl('http://www.cambioschaco.com.py/api/branch_office/32/exchange');
            extraerCambiosChaco($result['items'], 169, 170, 173, 171, 174, 172, 175, 'SISTEMA');
        }

        if (intval($hora) >= 5 && intval($hora) <= 12 && $dia = 6 ) {
            //ADRIAN JARA
            $result = get_curl('http://www.cambioschaco.com.py/api/branch_office/9/exchange');
            extraerCambiosChaco($result['items'], 15, 16, 19, 17, 20, 18, 21, 'SISTEMA');

            sleep(10);

            //MONSEÑOR RODRIGUEZ 
            $result = get_curl('http://www.cambioschaco.com.py/api/branch_office/32/exchange');
            extraerCambiosChaco($result['items'], 169, 170, 173, 171, 174, 172, 175, 'SISTEMA');
        }
    }
?>