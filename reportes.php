<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CLTP</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .left-side img {
            max-width: 100%;
            height: auto;
        }

        .right-side {
            flex-grow: 1;
            padding: 0 20px;
        }

        .right-side h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .right-side div {
            margin-bottom: 20px;
        }

        .right-side h3 {
            color: #333;
        }

        .right-side p {
            color: #666;
            margin-top: 5px;
        }

        .download-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php
    require 'connect/DB.php';

    // Consulta SQL para obtener el número total de usuarios registrados
    $sql_users_count = "SELECT COUNT(*) AS total_users FROM users";
    $result_users_count = DB::query($sql_users_count);
    $total_users = $result_users_count[0]['total_users'];

    // Consulta SQL para obtener el número total de publicaciones
    $sql_posts_count = "SELECT COUNT(*) AS total_posts FROM post";
    $result_posts_count = DB::query($sql_posts_count);
    $total_posts = $result_posts_count[0]['total_posts'];

    // Consulta SQL para obtener el número total de mensajes
    $sql_messages_count = "SELECT COUNT(*) AS total_messages FROM messages";
    $result_messages_count = DB::query($sql_messages_count);
    $total_messages = $result_messages_count[0]['total_messages'];
    ?>
    <div class="header">
        <div class="logo"><a href="sign.php">CLTP</a></div>
    </div>
    <div class="main" style="width:100%;">
        <div class="left-side">
            <img src="assets/image/cltp%20Signin%20image.jpg" alt="">
        </div>
        <div class="right-side">
            <h2>Informes</h2>
            <div>
                <h3>Usuarios Registrados:</h3>
                <p>Total: <?php echo $total_users; ?></p>
            </div>
            <div>
                <h3>Publicaciones:</h3>
                <p>Total: <?php echo $total_posts; ?></p>
            </div>
            <div>
                <h3>Mensajes:</h3>
                <p>Total: <?php echo $total_messages; ?></p>
            </div>
            <!-- Botón de descarga -->
            <button id="download-btn" class="download-btn">Descargar Informe</button>
        </div>
    </div>

    <script>
        document.getElementById('download-btn').addEventListener('click', function() {
            // Crear un objeto FormData y agregar los datos del informe
            var formData = new FormData();
            formData.append('total_users', '<?php echo $total_users; ?>');
            formData.append('total_posts', '<?php echo $total_posts; ?>');
            formData.append('total_messages', '<?php echo $total_messages; ?>');

            // Realizar una solicitud POST para generar el archivo CSV
            fetch('generar_informe.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                // Crear un enlace para descargar el archivo CSV
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'informe.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            });
        });
    </script>
</body>
</html>
