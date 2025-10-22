<?php

$servidor = "fdb1034.awardspace.net";  
$usuario_db = "4667269_particularbooks"; 
$password_db = "sS1/11/1Ss"; 
$nombre_db = "4667269_particularbooks";  


$conexion = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);


if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>