<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $elim = $_GET['id'];

    include 'config/configuracio.php';

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>llista de grups ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid red;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='editgrups.php'>crear, editar i eliminar grups</a>
            </p>

            <p class="h1" style="background: red; text-align: left; padding-left: 20px;">
                Crear, editar i eliminar grups
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width:150px;" type="button" value="CREAR GRUP NOU"
       onClick="javascript:window.location = 'creategrups.php';">
</span>
            </p>


            <?php
            if ($elim != "") {
                $select = "SELECT grup FROM proces_linia
	WHERE grup='" . $elim . "' ";
                $result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));
                if (mysqli_num_rows($result) >= 1) {
                    die
                    ("	<p class='comment'>El grup " . $elim . " està associat a processos i categories.<br/>
   				Hauries de borrar les associacions en primer terme.<br/>
   				Si no poguessis borrar-lo, pensa que pots desactivar-lo.</p>");
                } else {
                    $query4 = "DELETE FROM grups
					WHERE nom='" . $elim . "' ";
                    mysqli_query($conn,$query4) or die('Error, insert query4 failed');
                    echo "<p class='comment'>El grup " . $elim . " s'ha eliminat</p>";
                }
            }

            ?>

            <div class="contenidor_fac" style="border: 1px solid red; width: 600px; margin-bottom: 20px;">
                <table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr class="cos_majus">
                        <td width="30%" align="center">nom</td>
                        <td width="20%" align="center">actiu</td>
                        <td width="35%" align="center">comentaris</td>
                        <td width="15%" align="center">eliminar</td>
                    </tr>
                    <?php

                    $taula = "SELECT nom, actiu, notes FROM grups
		ORDER BY actiu,nom";

                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }

                    while (list($nom, $actiu, $notes) = mysqli_fetch_row($result))
                    {

                    ?>

                    <tr class="cos">
                        <td align='center'><a href='editgrups2.php?id=<?php echo $nom; ?>'><?php echo $nom; ?></a></td>
                        <td align='center'><?php echo $actiu; ?></td>
                        <td align='center'><?php echo $notes; ?></td>
                        <td align='center'>
                            <a href='editgrups.php?id=<?php echo $nom; ?>'
                               onClick='if(confirm("Estas segur que vols eliminar el grup <?php echo $nom; ?>?") == false){return false;}'>X</a>
                        </td>

                        <?php
                        }

                        echo "</tr></table></div>";

                        ?>


                        <p class="cos2" style="clear: both; text-align: center;">
                            Per editar les dades d'un grup clica sobre el seu nom. Per eliminar un grup clica sobre la X
                            a la columna ELIMINAR
                        </p>

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
