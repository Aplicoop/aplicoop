<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $elim = $_GET['id'];
    $elim2 = $_GET['id2'];

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>processos ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #66FF66;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='editprocessos.php'>crear, editar i eliminar processos</a>
            </p>
            <p class="h1" style="background: #66FF66; text-align: left; padding-left: 20px;">
                Crear, editar i eliminar processos
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width: 150px;" type="button" value="CREAR PROCÉS NOU"
       onClick="javascript:window.location = 'createproces.php';">
</span>
            </p>

            <div class="contenidor_fac" style="border: 1px solid #66FF66; width:800px; margin-bottom: 20px;">
                <?php
                if ($elim != "") {
                    $select = "SELECT proces FROM proces_linia
	WHERE proces='" . $elim . "' AND grup='" . $elim2 . "'";
                    $result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));
                    if (mysqli_num_rows($result) >= 1) {
                        die
                        ("<p class='comment'>El procés " . $elim . " amb grup " . $elim2 . " està associat a categories.</p>
   				<p class='comment'>Hauries de borrar les associacions en primer terme.</p>
   				<p class='comment' style='margin-bottom: 20px;'>
   				Si no poguessis borrar-lo, pensa que pots desactivar-lo.</p>");
                    } else {
                        $query4 = "DELETE FROM processos
					WHERE nom='" . $elim . "' AND grup='" . $elim2 . "' ";
                        mysqli_query($conn,$query4) or die('Error, insert query4 failed');
                        echo "<p class='comment' style='margin-bottom: 20px;'>
					El procés " . $elim . " amb grup " . $elim2 . " s'ha eliminat.</p>";
                    }
                }

                ?>


                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr class="cos_majus">
                        <td align="center">nombre</td>
                        <td align="center">grupo</td>
                        <td align="center">tipo</td>
                        <td align="center">fecha inicial</td>
                        <td align="center">fecha final</td>
                        <td align="center">periodo</td>
                        <td align="center">dia recogida</td>
                        <td align="center">dia tall</td>
                        <td align="center">activo</td>
                        <td align="center">eliminar</td>
                    </tr>

                    <?php

                    $taula = "SELECT nom, grup, tipus, data_inici, data_fi, dia_recollida, periode, dia_tall, hora_tall, actiu
			FROM processos
			ORDER BY actiu,data_inici ASC";

                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }

                    while (list($nom, $grup, $tipus, $di, $df, $diare, $periode, $diat, $horat, $actiu) = mysqli_fetch_row($result)) {
                        $di2 = explode("-", $di);
                        $dini = $di2[2] . "/" . $di2[1] . "/" . $di2[0];
                        if ($dini == "00/00/0000") {
                            $dini = "";
                        }
                        $df2 = explode("-", $df);
                        $dfin = $df2[2] . "/" . $df2[1] . "/" . $df2[0];
                        if ($dfin == "00/00/0000") {
                            $dfin = "";
                        }
                        if ($horat != "") {
                            $horat2 = substr($horat, 0, 2);
                            $mint = substr($horat, 2, 2);
                            $vhorat = $horat2 . ":" . $mint;
                        } else {
                            $vhorat = "";
                        }
                        echo "<tr class='cos'>
<td align='center'><a href='editprocessos2.php?id=" . $nom . "&id2=" . $grup . "&id3=" . $tipus . "'>" . $nom . " </a></td>
<td align='center'><a href='editprocessos2.php?id=" . $nom . "&id2=" . $grup . "&id3=" . $tipus . "'>" . $grup . "</a></td>
<td align='center'>" . $tipus . "</td>
<td align='center'>" . $dini . "</td>
<td align='center'>" . $dfin . "</td>
<td align='center'>" . $periode . "</td>
<td align='center'>" . $diare . "</td>
<td align='center'>" . $diat . " - " . $vhorat . "</td>
<td align='center'>" . $actiu . "</td>";

                        ?>
                        <td align='center'>
                            <a href='editprocessos.php?id=<?php echo $nom; ?>&id2=<?php echo $grup; ?>'
                               onClick='if(confirm("Estas segur que vols eliminar el procés <?php echo $nom; ?> del grup <?php echo $grup; ?>?") == false){return false;}'>X</a>
                        </td></tr>

                        <?php
                    }
                    ?>

                </table>
            </div>
            <p class="cos2" style="clear: both; text-align: center;">
                Para editar los datos de'n proceso clicka sobre su nombre.
                Para eliminar un proceso clicka sobre la X de la columna ELIMINAR.
            </p>
        </div>
    </div>
    </table>
    </body>
    </html>


    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
