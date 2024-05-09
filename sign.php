<?php
require 'connect/DB.php';
require 'core/load.php';

$tipo = '1'; // Definir un valor predeterminado para $tipo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['first-name'], $_POST['last-name'], $_POST['email-mobile'], $_POST['up-password'], $_POST['birth-day'], $_POST['birth-month'], $_POST['birth-year'], $_POST['pet-name'], $_POST['pet-raza'], $_POST['pet-especie'], $_POST['gen'])) {

        // Obtener y limpiar los datos del formulario
        $upFirst = $_POST['first-name'];
        $upLast = $_POST['last-name'];
        $upEmailMobile = $_POST['email-mobile'];
        $upPassword = $_POST['up-password'];
        $birthDay = $_POST['birth-day'];
        $birthMonth = $_POST['birth-month'];
        $birthYear = $_POST['birth-year'];
        $uppetName = $_POST['pet-name'];
        $uppetRaza = $_POST['pet-raza'];
        $uppetEspecie = $_POST['pet-especie'];
        $upgen = $_POST['gen'];

        // Validar campos
        if (empty($upFirst) || empty($upLast) || empty($upEmailMobile) || empty($upgen) || empty($uppetName) || empty($uppetRaza) || empty($uppetEspecie)) {
            $error = 'Todos los campos son requeridos';
        } else {
            // Limpieza de datos
            $first_name = $loadFromUser->checkInput($upFirst);
            $last_name = $loadFromUser->checkInput($upLast);
            $email_mobile = $loadFromUser->checkInput($upEmailMobile);
            $password = $loadFromUser->checkInput($upPassword);
            $pet_name = $loadFromUser->checkInput($uppetName);
            $pet_raza = $loadFromUser->checkInput($uppetRaza);
            $pet_especie = $loadFromUser->checkInput($uppetEspecie);
            $screenName = $first_name . '_' . $last_name;

            // Validar correo electrónico o número de teléfono
            if (!filter_var($email_mobile, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9]{11}$/', $email_mobile)) {
                $error = 'El correo electrónico o el número de teléfono no son válidos. Por favor, inténtalo de nuevo.';
            } else {
                // Validar longitud de contraseña
                if (strlen($password) < 5 || strlen($password) >= 60) {
                    $error = 'La contraseña no es válida';
                } else {
                    // Verificar si el correo electrónico o número de teléfono ya está en uso
                    if (DB::query('SELECT email FROM users WHERE email=:email', array(':email' => $email_mobile)) || DB::query('SELECT mobile FROM users WHERE mobile=:mobile', array(':mobile' => $email_mobile))) {
                        $error = 'El correo electrónico o número de teléfono ya está en uso';
                    } else {
                        // Crear usuario
                        $user_id = $loadFromUser->create('users', array(
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'email' => $email_mobile,
                            'password' => password_hash($password, PASSWORD_BCRYPT),
                            'screenName' => $screenName,
                            'userLink' => $screenName,
                            'birthday' => $birthYear . '-' . $birthMonth . '-' . $birthDay,
                            'gender' => $upgen,
                            'pet_name' => $pet_name,
                            'pet_raza' => $pet_raza,
                            'pet_especie' => $pet_especie
                        ));

                        // Crear perfil de usuario
                        $loadFromUser->create('profile', array(
                            'userId' => $user_id,
                            'birthday' => $birthYear . '-' . $birthMonth . '-' . $birthDay,
                            'firstName' => $first_name,
                            'lastName' => $last_name,
                            'profilePic' => 'assets/image/defaultProfile.png',
                            'coverPic' => 'assets/image/defaultCover.png',
                            'gender' => $upgen,
                            'pet_name' => $pet_name,
                            'pet_raza' => $pet_raza,
                            'pet_especie' => $pet_especie
                        ));

                        // Generar token y crear sesión
                        $tstrong = true;
                        $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                        $loadFromUser->create('token', array('token' => sha1($token), 'user_id' => $user_id));
                        setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);

                        // Redirigir a la página de inicio
                        header('Location: index.php');
                        exit();
                    }
                }
            }
        }
    }

    // Inicio de sesión
    if (isset($_POST['in-email-mobile'], $_POST['in-pass'])) {
        $email_mobile = $_POST['in-email-mobile'];
        $in_pass = $_POST['in-pass'];

        // Validar correo electrónico o número de teléfono
        if (!filter_var($email_mobile, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico o el número de teléfono no son válidos. Por favor, inténtalo de nuevo.';
        } else {
            // Buscar usuario en la base de datos
            $user = DB::query('SELECT * FROM users WHERE email=:email OR mobile=:mobile', array(':email' => $email_mobile, ':mobile' => $email_mobile));
            if ($user && password_verify($in_pass, $user[0]['password'])) {
                // Generar token y crear sesión
                $tstrong = true;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                $loadFromUser->create('token', array('token' => sha1($token), 'user_id' => $user[0]['user_id']));
                setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);

                // Redirigir a la página de inicio
                header('Location: index.php');
                exit();
            } else {
                $error = 'El correo electrónico o la contraseña no son válidos';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>cltp</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="header">
        <div class="logo"><a href="sign.php">CLTP</a></div>
        <form action="sign.php" method="post">
            <div class="sign-in-form">
                <div class="mobile-input">
                    <div class="input-text">Email o celular</div>
                    <input type="text" name="in-email-mobile" id="email-mobile" class="input-text-field">
                </div>
                <div class="password-input">
                    <div style="font-size: 12px;padding-bottom: 5px;">Contraseña</div>
                    <input type="password" name="in-pass" id="in-password" class="input-text-field">
                    <div class="forgotten-acc"><a href="forgot_password.php" target="_blank"> Olvidaste tu contraseña</a></div>
                </div>
                <div class="login-button">
                    <input type="submit" value="Log in" class="sign-in login">
                </div>
                
            </div>
        </form>
        <div class="forgotten-acc"><a href="inicio_admin.php" target="_blank">administrador</a></div>
    </div>

    <div class="main" style="width:100%;">
        <div class="left-side">
            <img src="assets/image/facebook%20Signin%20image.jpg" alt="">
        </div>
        <div class="right-side">
            <div class="error">
                <?php if (!empty($error)) {
                    echo $error;
                } ?>
            </div>
            <h1 style="color:#212121;">Crear una cuenta</h1>
            <div style="color:#212121; font-size:20px">Es gratis para todos los aficionados a las mascotas</div>
            <form action="sign.php" method="post" name="user-sign-up">
                <div class="sign-up-form">
                    <div class="sign-up-name">
                        <input type="text" name="first-name" id="first-name" class="text-field" placeholder="Primer Nombre">
                        <input type="text" name="last-name" id="last-name" placeholder="Primer apellido" class="text-field">
                    </div>
                    <div class="sign-wrap-mobile">
                        <input type="text" name="email-mobile" id="up-email" placeholder="Mobile number or email address" class="text-input">
                    </div>
                    <div class="sign-up-password">
                        <input type="password" name="up-password" id="up-password" class="text-input" placeholder="Password">
                    </div>
                    <div class="sign-up-birthday">
                        <div class="bday">Cumpleaños</div>
                        <div class="form-birthday">
                            <select name="birth-day" id="days" class="select-body"></select>
                            <select name="birth-month" id="months" class="select-body"></select>
                            <select name="birth-year" id="years" class="select-body"></select>
                        </div>

                    </div>
                    <div class="gender-wrap">
                        <input type="radio" name="gen" id="fem" value="female" class="m0">
                        <label for="fem" class="gender">Mujer</label>
                        <input type="radio" name="gen" id="male" value="male" class="m0">
                        <label for="male" class="gender">Hombre</label>
                    </div>
                    <div class="sign-up-pet">
                        <input type="text" name="pet-name" id="pet-name" class="text-field" placeholder="Nombre mascota">
                        <input type="text" name="pet-raza" id="pet-raza" placeholder="Raza mascota" class="text-field">
                        <input type="text" name="pet-especie" id="pet-especie" placeholder="Especie mascota" class="text-field">
                    </div>
                    <div id="terms-container">
                      <input type="checkbox" id="terms-checkbox" required>
                      <label for="terms-checkbox">Acepto los <a href="#" id="open">Términos y Condiciones</a></label>
                    </div>
                    <div id="modal_container" class="modal-container">
                      <div class="modal" role="dialog">
                        <h1>Terminos y condiciones</h1>
                        <p>
                          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Itaque assumenda dignissimos illo explicabo natus quia repellat, praesentium voluptatibus harum ipsam dolorem cumque labore sunt dicta consectetur, nesciunt maiores delectus maxime?
                        </p>
                        <button id="close" role="button" aria-label="Cerrar términos y condiciones" tabindex="-1">Aceptar</button>
                      </div>
                    </div>               
                    <input type="submit" value="Sign Up" class="sign-up">
                    <script>
                      const open = document.getElementById('open');
                      const modal_container = document.getElementById('modal_container');
                      const close = document.getElementById('close');
                      const checkbox = document.getElementById('terms-checkbox');

                      // Restore checkbox state from local storage
                      if (localStorage.getItem('checkboxState') === 'true') {
                        checkbox.checked = true;
                      }

                      open.addEventListener('click', () => {
                        modal_container.classList.add('show');
                      });

                      close.addEventListener('click', () => {
                        modal_container.classList.remove('show');
                        checkbox.checked = true;
                        // Save checkbox state to local storage
                        localStorage.setItem('checkboxState', checkbox.checked);
                      });

                      // Clear checkbox state from local storage when "Sign Up" button is clicked
                      const signUpButton = document.querySelector('.sign-up');
                      signUpButton.addEventListener('click', () => {
                        localStorage.removeItem('checkboxState');
                      });
                    </script>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/js/jquery.js"></script>


    <script>
        for (i = new Date().getFullYear(); i > 1900; i--) {
            //    2019,2018, 2017,2016.....1901
            $("#years").append($('<option/>').val(i).html(i));

        }
        for (i = 1; i < 13; i++) {
            $('#months').append($('<option/>').val(i).html(i));
        }
        updateNumberOfDays();

        function updateNumberOfDays() {
            $('#days').html('');
            month = $('#months').val();
            year = $('#years').val();
            days = daysInMonth(month, year);
            for (i = 1; i < days + 1; i++) {
                $('#days').append($('<option/>').val(i).html(i));
            }

        }
        $('#years, #months').on('change', function() {
            updateNumberOfDays();
        })

        function daysInMonth(month, year) {
            return new Date(year, month, 0).getDate();

        }
    </script>


</body>

</html>