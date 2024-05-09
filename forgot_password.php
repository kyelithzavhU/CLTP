<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>cltp</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<?php
require 'lib/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

require 'connect/DB.php';

try {
    if (isset($_POST['email'])) {
        $email = DB::query('SELECT email, userLink FROM users WHERE email = :email', array(':email' => $_POST['email']));

        if (!empty($email)) {
            $email = $email[0];
            $user = DB::query('SELECT user_id FROM users WHERE email = :email', array(':email' => $email['email']))[0];
            $token = uniqid();

            // Update password here
            $new_password = generateRandomPassword(); // Generate a new random password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the password

            DB::query('UPDATE users SET password = :password WHERE user_id = :user_id', array(':password' => $hashed_password, ':user_id' => $user['user_id']));

            DB::query('DELETE FROM token_res WHERE user_id = :user_id', array(':user_id' => $user['user_id']));
            DB::query('INSERT INTO token_res (user_id, token_for, expiration_date) VALUES (:user_id, :token_for, :expiration_date)', array(':user_id' => $user['user_id'], ':token_for' => $token, ':expiration_date' => date('Y-m-d H:i:s', strtotime('+1 hour'))));

            $userLink = $email['userLink'];

            $email_to = $email['email'];
            $email_subject = "Cambio de contraseña";
            $email_from = "mailtrap@demomailtrap.com";

            $email_message = "Hola " . $userLink . ", has solicitado cambiar tu contraseña. Tu nueva contraseña es: " . $new_password . "\n\n";
            $email_message .= "Después de iniciar sesión, se te pedirá que cambies esta contraseña.\n\n";

            //Server settings
            $mail = new PHPMailer(true);
            $mail->isSMTP();                                           
            $mail->Host       = 'live.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'api'; 
            $mail->Password   = '7c8110fb602ec9b17ad39c1b1387c3fa'; 
            $mail->SMTPSecure = 'tls';         
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom($email_from, 'Tu Sitio Web');
            $mail->addAddress($email_to);

            // Content
            $mail->isHTML(false);                                 
            $mail->Subject = $email_subject;
            $mail->Body    = $email_message;

            $mail->send();
            $success = 'Te hemos enviado un email con tu nueva contraseña';
             header('Location: sign.php');
        } else {
            $error = 'No se encontró una cuenta asociada a ese correo electrónico.';
        }
    }
} catch (Exception $e) {
    $error = 'Error al enviar el correo electrónico: ' . $e->getMessage();
}

function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
?>
<div class="header">
        <div class="logo"><a href="sign.php">CLTP</a></div>        
    </div>
    <div class="main" style="width:100%;">
        <div class="left-side">
            <img src="assets/image/cltp%20Signin%20image.jpg" alt="">
        </div>
        <div class="right-side">            
           <h1 style="color:#212121;">Restablece tu contraseña</h1>
            <form action="forgot_password.php" method="post" name="user-forgot-password">
                <div class="forgot-password-form">
                    <div class="forgot-wrap-email">
                        <input type="text" name="email" id="up-email" placeholder="Email address" class="text-input">
                    </div>                             
                    <input type="submit" value="Recuperar" class="forgot-up"> 
                </div>
            </form>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        </div>
    </div>    
</body>
</html>