<?php
// Obtener los datos del informe del cuerpo de la solicitud POST
$total_users = $_POST['total_users'];
$total_posts = $_POST['total_posts'];
$total_messages = $_POST['total_messages'];

// Generar el contenido del archivo CSV
$data = [
    ['Usuarios Registrados', 'Publicaciones', 'Mensajes'],
    [$total_users, $total_posts, $total_messages]
];

// Crear un archivo CSV en memoria
$fp = fopen('php://temp', 'w');
foreach ($data as $fields) {
    fputcsv($fp, $fields);
}
rewind($fp);

// Configurar las cabeceras para forzar la descarga del archivo
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="informe.csv"');

// Transmitir el contenido del archivo CSV al navegador
fpassthru($fp);
fclose($fp);
