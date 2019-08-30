<?php
    require('class.phpmailer.php');
    
    $mail = new PHPMailer();

    function setEmail ($sucursal, $sistema, $ip, ) {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.cambiosalberdi.com";
        $mail->Username = "czelaya@cambiosalberdi.com";
        $mail->Password = "Z3l4y4P&";
        $mail->Port = 465; 
        $mail->From = "czelaya@cambiosalberdi.com";
        $mail->FromName = "Notificación"; 
        $mail->AddAddress("czelaya@cambiosalberdi.com");
        $mail->IsHTML(true);
        $mail->Subject = "ALERTA: ".$sucursal;
        $body = "<b> Sucursal con inconveniente </b>"; 
        $body .= "\n -> Sucursal:   ".$sucursal;
        $body .= "\n -> Sistema:    ".$sistema;
        $body .= "\n -> IP:         ".$ip;
        $body .= "\n -> Fecha:      ".date("d-m-Y");
        $body .= "\n -> Hora:       ".date("H-i-s.u");;
        $mail->Body = $body;
        $exito = $mail->Send();
        if($exito){
            echo "El correo fue enviado correctamente."; 
        }else{ 
            echo "Hubo un problema. Contacta a un administrador."; 
        } 
    }
?>