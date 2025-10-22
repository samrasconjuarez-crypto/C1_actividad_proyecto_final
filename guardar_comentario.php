<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include 'db_conexion.php';

    $id_receta = (int)$_POST['id_receta'];
    $nombre_usuario = $_POST['usuario'];
    $carrera = $_POST['carrera'];
    $texto_comentario = $_POST['comentario'];

    if (empty($id_receta) || empty($nombre_usuario) || empty($carrera) || empty($texto_comentario)) {
        die("Error: Faltan datos.");
    }

    $id_usuario_actual = null;


    $sql_buscar_usr = "SELECT ID_Usuario FROM corea_Usuarios WHERE NombreUsuario = ?";
    $stmt_buscar = $conexion->prepare($sql_buscar_usr);
    $stmt_buscar->bind_param("s", $nombre_usuario);
    $stmt_buscar->execute();
    $resultado_usr = $stmt_buscar->get_result();

    if ($resultado_usr->num_rows > 0) {
        $fila_usr = $resultado_usr->fetch_assoc();
        $id_usuario_actual = $fila_usr['ID_Usuario'];
        $stmt_buscar->close();
        
      
        $sql_update_carrera = "UPDATE corea_Usuarios SET Carrera = ? WHERE ID_Usuario = ?";
        $stmt_update = $conexion->prepare($sql_update_carrera);
        $stmt_update->bind_param("si", $carrera, $id_usuario_actual);
        $stmt_update->execute();
        $stmt_update->close();

    } else {
   
        $stmt_buscar->close();
        $sql_crear_usr = "INSERT INTO corea_Usuarios (NombreUsuario, Carrera) VALUES (?, ?)";
        $stmt_crear = $conexion->prepare($sql_crear_usr);
        $stmt_crear->bind_param("ss", $nombre_usuario, $carrera);
        
        if ($stmt_crear->execute()) {
            $id_usuario_actual = $conexion->insert_id;
        } else {
            die("Error al crear el usuario: " . $conexion->error);
        }
        $stmt_crear->close();
    }


    if ($id_usuario_actual) {
        $sql_insert_com = "INSERT INTO corea_Comentarios (TextoComentario, ID_Usuario, ID_Receta) VALUES (?, ?, ?)";
        $stmt_com = $conexion->prepare($sql_insert_com);
        $stmt_com->bind_param("sii", $texto_comentario, $id_usuario_actual, $id_receta);
        
        if (!$stmt_com->execute()) {
            die("Error al guardar el comentario: " . $conexion->error);
        }
        $stmt_com->close();
    }

    $conexion->close();


    header("Location: receta.php?id=" . $id_receta);
    exit;

} else {
    header('Location: index.php');
    exit;
}
?>