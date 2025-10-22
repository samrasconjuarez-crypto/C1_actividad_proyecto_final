<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_receta = (int)$_GET['id'];
include 'db_conexion.php';


$sql_receta = "SELECT NombrePlato, DescripcionCorta, Ingredientes, Instrucciones FROM corea_Recetas WHERE ID_Receta = ?";
$stmt_receta = $conexion->prepare($sql_receta);
$stmt_receta->bind_param("i", $id_receta);
$stmt_receta->execute();
$resultado_receta = $stmt_receta->get_result();

if ($resultado_receta->num_rows === 0) {
    echo "Receta no encontrada.";
    $conexion->close();
    exit;
}
$receta = $resultado_receta->fetch_assoc();


$sql_comentarios = "
    SELECT 
        Com.TextoComentario, 
        Com.Fecha, 
        Usr.NombreUsuario, 
        Usr.Carrera
    FROM corea_Comentarios AS Com
    JOIN corea_Usuarios AS Usr ON Com.ID_Usuario = Usr.ID_Usuario
    WHERE Com.ID_Receta = ?
    ORDER BY Com.Fecha DESC
";
$stmt_comentarios = $conexion->prepare($sql_comentarios);
$stmt_comentarios->bind_param("i", $id_receta);
$stmt_comentarios->execute();
$resultado_comentarios = $stmt_comentarios->get_result();

$ruta_imagen_receta = "img/receta-" . $id_receta . ".jpg";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($receta['NombrePlato']); ?></title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <a href="index.php" class="enlace-header">
            <h1><?php echo htmlspecialchars($receta['NombrePlato']); ?></h1>
        </a>
    </header>

    <main class="contenedor-receta">
        
        <a href="index.php" class="boton-volver" style="margin-bottom: 20px;">&larr; Volver a Recetas</a>
        
        <img src="<?php echo $ruta_imagen_receta; ?>" alt="<?php echo htmlspecialchars($receta['NombrePlato']); ?>" class="img-receta">
        
        <h3>Ingredientes</h3>
        <p><?php echo nl2br(htmlspecialchars($receta['Ingredientes'])); ?></p> 

        <h3>Instrucciones</h3>
        <p><?php echo nl2br(htmlspecialchars($receta['Instrucciones'])); ?></p>

        <section class="seccion-comentarios">
            <h2>Comentarios de Estudiantes</h2>
            
            <form action="guardar_comentario.php" method="POST" class="form-comentario">
                <input type="hidden" name="id_receta" value="<?php echo $id_receta; ?>">
                <div class="form-grupo">
                    <label for="usuario">Tu Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                <div class="form-grupo">
                    <label for="carrera">Tu Carrera:</label>
                    <input type="text" id="carrera" name="carrera" required>
                </div>
                <div class="form-grupo">
                    <label for="comentario">Tu Comentario:</label>
                    <textarea id="comentario" name="comentario" rows="4" required></textarea>
                </div>
                <button type="submit" class="boton">Enviar Comentario</button>
            </form>

            <div class="lista-comentarios">
                <?php
                if ($resultado_comentarios->num_rows > 0) {
                    while($com = $resultado_comentarios->fetch_assoc()) {
                ?>
                    <div class="comentario">
                        <p class="comentario-texto">"<?php echo htmlspecialchars($com['TextoComentario']); ?>"</p>
                        <small class="comentario-autor">
                            — <strong><?php echo htmlspecialchars($com['NombreUsuario']); ?></strong> 
                            (Estudiante de: <em><?php echo htmlspecialchars($com['Carrera']); ?></em>)
                        </small>
                        <small class="comentario-fecha"><?php echo date('d/m/Y H:i', strtotime($com['Fecha'])); ?>h</small>
                    </div>
                <?php
                    }
                } else {
                    echo "<p>No hay comentarios. ¡Sé el primero en opinar!</p>";
                }
                
                $stmt_receta->close();
                $stmt_comentarios->close();
                $conexion->close();
                ?>
            </div>
        </section>
    </main>
</body>
</html>