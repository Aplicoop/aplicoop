<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $gtipus = $_GET['id'];
    $gactiu = $_GET['id2'];
    $gestoc = $_GET['id4'];
    $elim = $_GET['id3'];

    include 'config/configuracio.php';

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - editar categorias</title>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">
        <div class="container">
           <div class="u-cf">
             <h1 class="pull-left">Crear, editar y eliminar categorias</h1>
            <div class="pull-right u-mt-1">
                <button class="button button--white button--animated" onClick="javascript:window.location = 'createcat.php';">Crear categoría<i class="fa fa-plus-circle" aria-hidden="true"></i></button>

            </div>
        </div>



            <?php
            if ($gestoc != "") {
                $query4 = "UPDATE categoria
				SET estoc='" . $gestoc . "'
				WHERE tipus='" . $gtipus . "' ";
                mysqli_query($conn,$query4) or die('Error, insert query4 failed');
            }

            if ($gactiu != "") {
                $query3 = "UPDATE categoria
				SET actiu='" . $gactiu . "'
				WHERE tipus='" . $gtipus . "' ";
                mysqli_query($conn,$query3) or die('Error, insert query3 failed');
            }

            if ($elim != "") {
                $select = "SELECT subcategoria FROM subcategoria
				WHERE categoria='" . $gtipus . "' ";
                $result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));

                if (mysqli_num_rows($result) >= 1) {
                    die
                    ("<p class='comment'>La categoria " . $gtipus . " posseeix subcategories.</p>
   				<p class='comment'>Hauries de borrar-les en primer terme.</p>");
                } else {
                    $query4 = "DELETE FROM categoria
				WHERE tipus='" . $gtipus . "' ";
                    mysqli_query($conn,$query4) or die('Error, insert query4 failed');

                    echo "<p class='comment'>La categoria " . $gtipus . " s'ha eliminat correctament</p>";
                }
            }

            ?>

            <div class="box">
               <div class="table-responsive">
                 <table class="table table-striped table-fixed">
                    <tr class="cos_majus">
                        <td width="40%" align="center" class="u-text-bold">CATEGORIA</td>
                        <td width="15%" align="center" class="u-text-bold">ACTIVO</td>
                        <td width="15%" align="center" class="u-text-bold">STOCK</td>
                        <td width="15%" align="center" class="u-text-bold">SUBCATEGORIAS</td>
                        <td width="15%" align="center" class="u-text-bold">ELIMINAR</td>
                    </tr>
                    <?php

                    $taula = "SELECT tipus, actiu, estoc FROM categoria
		ORDER BY actiu, tipus";
                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }

                    $k = 0;
                    while (list($tipus, $actiu, $estoc) = mysqli_fetch_row($result))
                    {
                    ?>
                    <tr class="cos">
                        <td align="center">
                            <?php echo $tipus; ?>
                        </td>
                        <td align="center">
                            si<input type="radio" name="actiu<?php echo $k; ?>" value="activat"
                                     id="actiu<?php echo $k; ?>" <?php if ($actiu == "activat") {echo "checked";} ?>
                                     onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id2=activat';">
                            no<input type="radio" name="actiu<?php echo $k; ?>" value="desactivat"
                                     id="actiu<?php echo $k; ?>" <?php if ($actiu == "desactivat") {echo "checked";} ?>
                                     onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id2=desactivat';">
                        </td>
                        <td align="center">
                            si<input type="radio" name="estoc<?php echo $k; ?>" value="si"
                                     id="estoc<?php echo $k; ?>" <?php if ($estoc == "si") {echo "checked";} ?>
                                     onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id4=si';">
                            no<input type="radio" name="estoc<?php echo $k; ?>" value="no"
                                     id="estoc<?php echo $k; ?>" <?php if ($estoc == "no") {echo "checked";} ?>
                                     onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id4=no';">
                        </td>
                        <td align="center"><a href='subcategories.php?id=<?php echo $tipus; ?>'>S</a></td>
                        <td align="center">
                            <a href='categories.php?id=<?php echo $tipus; ?>&id3=borrar'
                               onClick='if(confirm("Estas segur que vols eliminar aquesta categoria <?php echo $tipus; ?>?") == false){return false;}'>X</a>
                        </td>

                        <?php
                        $k++;
                        }
                        echo "</tr></table></div>";

                           echo "<p class='alert alert--info'>

                            Per activar o desactivar o per permetre que una categoria tingui estoc o no clica el botó
                            desitjat.
                            Per editar subcategories clica la S en la categoria que et convingui.
                            Per borrar clica sobre la X de la columna ELIMINAR</p>";

                        echo "</div>";

                        ?>




            </div>
        </div>
    </body>
    </html>


    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
