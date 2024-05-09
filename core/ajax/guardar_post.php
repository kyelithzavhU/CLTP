<?php
include '../load.php';
include '../../connect/login.php';


$location = $_POST['location'];

// Insertar datos en la tabla post
$sql = "INSERT INTO post (location) VALUES ('$location')";

if ($conn->query($sql) === TRUE) {
    echo "Publicación creada exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>