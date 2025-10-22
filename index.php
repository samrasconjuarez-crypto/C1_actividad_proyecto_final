<?php
include 'db_conexion.php';

$termino_busqueda = $_GET['q'] ?? '';
$param_like = "%" . $termino_busqueda . "%";
$has_search = !empty($termino_busqueda);

$sql_alimentos = "SELECT ID_Receta, NombrePlato, DescripcionCorta FROM corea_Recetas WHERE Categoria = 'alimento'";
if ($has_search) {
    $sql_alimentos .= " AND (NombrePlato LIKE ? OR DescripcionCorta LIKE ? OR Ingredientes LIKE ?)";
}
$sql_alimentos .= " ORDER BY NombrePlato";
$stmt_alimentos = $conexion->prepare($sql_alimentos);
if ($has_search) {
    $stmt_alimentos->bind_param("sss", $param_like, $param_like, $param_like);
}
$stmt_alimentos->execute();
$resultado_alimentos = $stmt_alimentos->get_result();


$sql_postres = "SELECT ID_Receta, NombrePlato, DescripcionCorta FROM corea_Recetas WHERE Categoria = 'postre'";
if ($has_search) {
    $sql_postres .= " AND (NombrePlato LIKE ? OR DescripcionCorta LIKE ? OR Ingredientes LIKE ?)";
}
$sql_postres .= " ORDER BY NombrePlato";
$stmt_postres = $conexion->prepare($sql_postres);
if ($has_search) {
    $stmt_postres->bind_param("sss", $param_like, $param_like, $param_like);
}
$stmt_postres->execute();
$resultado_postres = $stmt_postres->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandu</title>
    <link rel="stylesheet" href="estilos.css">
    
    <style>
        .contenedor-buscador {
            max-width: 1200px; margin: 20px auto; padding: 0 20px; text-align: center;
        }
        .form-buscador {
            display: flex; justify-content: center;
        }
        .form-buscador input[type="text"] {
            font-size: 1.1em; padding: 10px; border: 1px solid #ccc;
            border-radius: 5px 0 0 5px; width: 50%; max-width: 400px;
        }
        .form-buscador button {
            font-size: 1.1em; padding: 10px 20px; border: none;
            background-color: #005A9C; color: white; cursor: pointer;
            border-radius: 0 5px 5px 0;
        }
        .hero-container {
            position: relative; width: 100%; height: 450px; overflow: hidden;
        }
        .hero-container img {
            width: 100%; height: 100%; object-fit: cover; filter: brightness(0.6);
        }
        .hero-texto {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            color: white; text-align: center; text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        .hero-texto h2 {
            font-size: 3em; margin: 0; font-weight: 700;
        }
        .hero-texto p {
            font-size: 1.5em; margin-top: 10px;
        }
        .categoria-seccion {
            max-width: 1200px;
            margin: 40px auto 20px auto;
            padding: 0 20px;
        }
        .categoria-seccion h2 {
            font-size: 2.5em;
            color: #A31E35;
            border-bottom: 3px solid #eee;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="enlace-header">
        <h1>Mandu - Cocina Surcoreana</h1>
        </a>
    </header>

    <?php if (empty($termino_busqueda)): ?>
        <div class="hero-container">
            <img src="img/bienvenida.jpg" alt="Bienvenida a la Cocina Coreana">
            <div class="hero-texto">
                <h2>Descubre los Sabores de Corea</h2>
                <p>Explora recetas auténticas y tradicionales.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="contenedor-buscador">
        <form action="index.php" method="GET" class="form-buscador">
            <input 
                type="text" 
                name="q" 
                placeholder="Buscar en alimentos y postres..." 
                value="<?php echo htmlspecialchars($termino_busqueda); ?>"
            >
            <button type="submit">Buscar</button>
        </form>
    </div>

    <main>
        <section class="categoria-seccion">
            <h2>Platos Principales</h2>
            <div class="grid-recetas">
                <?php
                if ($resultado_alimentos->num_rows > 0) {
                    while($receta = $resultado_alimentos->fetch_assoc()) {
                        $ruta_imagen = "img/receta-" . $receta['ID_Receta'] . ".jpg";
                ?>
                    <article class="card-receta">
                        <img src="<?php echo $ruta_imagen; ?>" alt="<?php echo htmlspecialchars($receta['NombrePlato']); ?>">
                        <div class="card-info">
                            <h2><?php echo htmlspecialchars($receta['NombrePlato']); ?></h2>
                            <p><?php echo htmlspecialchars($receta['DescripcionCorta']); ?></p>
                            <a href="receta.php?id=<?php echo $receta['ID_Receta']; ?>" class="boton">Ver Receta</a>
                        </div>
                    </article>
                <?php
                    } 
                } else if ($has_search) {
                    echo "<p style='grid-column: 1 / -1; text-align: center;'>No se encontraron platos principales con ese término.</p>";
                } else {
                    echo "<p style='grid-column: 1 / -1; text-align: center;'>No hay platos principales para mostrar.</p>";
                }
                ?>
            </div>
        </section>

        <section class="categoria-seccion">
            <h2>Postres</h2>
            <div class="grid-recetas">
                <?php
                if ($resultado_postres->num_rows > 0) {
                    while($receta = $resultado_postres->fetch_assoc()) {
                        $ruta_imagen = "img/receta-" . $receta['ID_Receta'] . ".jpg";
                ?>
                    <article class="card-receta">
                        <img src="<?php echo $ruta_imagen; ?>" alt="<?php echo htmlspecialchars($receta['NombrePlato']); ?>">
                        <div class="card-info">
                            <h2><?php echo htmlspecialchars($receta['NombrePlato']); ?></h2>
                            <p><?php echo htmlspecialchars($receta['DescripcionCorta']); ?></p>
                            <a href="receta.php?id=<?php echo $receta['ID_Receta']; ?>" class="boton">Ver Receta</a>
                        </div>
                    </article>
                <?php
                    } 
                } else if ($has_search) {
                    echo "<p style='grid-column: 1 / -1; text-align: center;'>No se encontraron postres con ese término.</p>";
                } else {
                    echo "<p style='grid-column: 1 / -1; text-align: center;'>No hay postres para mostrar.</p>";
                }
                ?>
            </div>
        </section>

        <?php
        if ($resultado_alimentos->num_rows == 0 && $resultado_postres->num_rows == 0 && $has_search) {
            echo "<p style='text-align: center; font-size: 1.2em; margin: 20px;'>No se encontró ninguna receta que coincida con '<strong>" . htmlspecialchars($termino_busqueda) . "</strong>'.</p>";
        }
        
        $stmt_alimentos->close();
        $stmt_postres->close();
        $conexion->close();
        ?>
    </main>

    <footer>
          <p>Samuel Rascón - 204607</p>
    </footer>

</body>
</html>