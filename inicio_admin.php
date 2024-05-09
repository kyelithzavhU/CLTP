<?php
require 'connect/DB.php';
require 'core/load.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CLTP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="header">
        <div class="logo"><a href="sign.php">CLTP</a></div>        
    </div>
    <div class="main" style="width:100%;">
        <div class="left-side">
            <img src="assets/image/cltp%20Signin%20image.jpg" alt="">
        </div>
        <div class="right-side">            
           <h1 style="color:#212121;">Iniciar sesión</h1>
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="user-login">
                <div class="login-form">
                    <div class="login-wrap-email">
                        <input type="text" name="in-email-mobile" id="in-email-mobile" placeholder="Correo electrónico o número de teléfono" class="text-input">
                    </div>
                    <div class="login-wrap-pass">
                        <input type="password" name="in-pass" id="in-pass" placeholder="Contraseña" class="text-input">
                    </div>                             
                    <input type="submit" value="Iniciar sesión" class="login-up"> 
                </div>
            </form>
            <?php
            // Inicio de sesión
            if (isset($_POST['in-email-mobile'], $_POST['in-pass'])) {
                $email_mobile = $_POST['in-email-mobile'];
                $in_pass = $_POST['in-pass'];

                // Validar correo electrónico o número de teléfono
                if (!filter_var($email_mobile, FILTER_VALIDATE_EMAIL)) {
                    $error = 'El correo electrónico o el número de teléfono no son válidos. Por favor, inténtalo de nuevo.';
                } else {
                    // Buscar usuario en la base de datos
                    $user = DB::query('SELECT * FROM users WHERE email=:email', array(':email' => $email_mobile));
                    if ($user && password_verify($in_pass, $user[0]['password'])) {
                        if ($user[0]['email'] == 'karen.vargash@uniagustiniana.edu.co') {
                            // Generar token y crear sesión
                            $tstrong = true;
                            $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                            $loadFromUser->create('token', array('token' => sha1($token), 'user_id' => $user[0]['user_id']));
                            setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);

                            // Redirigir a la página de inicio
                            header('Location: reportes.php');
                            exit();
                        } else {
                            $error = 'El correo electrónico o la contraseña no son válidos';
                        }
                    } else {
                        $error = 'El correo electrónico o la contraseña no son válidos';
                    }
                }
            }
           ?>
            <?php if (isset($error)):?>
                <p style="color: red;"><?php echo htmlspecialchars($error);?></p>
            <?php endif;?>
        </div>
    </div>    
</body>
</html>