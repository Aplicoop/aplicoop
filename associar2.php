<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $gproc = $_GET['id'];
    $ggrup = $_GET['id2'];
    $gcat = $_GET['id3'];
    $gordre = $_GET['id4'];
    $updown = $_GET['id5'];
    $gactiu = $_GET['id6'];
    $elim = $_GET['id7'];


    include('config/configuracio.php');

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>editar associacions ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #66FF66;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='associar.php'>crear i editar associacions</a>
                >>><a href='associar2.php?id=<?php echo $gproc; ?>&id2=<?php echo $ggrup; ?>'>
                    canviar ordre associació <?php echo $gproc . " " . $ggrup . " " . $gcat; ?></a>
            </p>

            <p class="h1" style="background: #66FF66; text-align: left; padding-left: 20px;">
                Canviar ordre associació <?php echo $gproc . " " . $ggrup . " " . $gcat; ?></p>


            <?php
            if ($updown == 'up') {
                $gordreup = $gordre - 1;
                $query = "UPDATE proces_linia
					SET ordre='" . $gordre . "'
					WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND ordre='" . $gordreup . "' ";
                mysqli_query($conn,$query) or die('Error, insert query failed');
                $query2 = "UPDATE proces_linia
					SET ordre='" . $gordreup . "'
					WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND categoria='" . $gcat . "' ";
                mysqli_query($conn,$query2) or die('Error, insert query2 failed');
            }

            if ($updown == 'down') {
                $gordredown = $gordre + 1;
                $query = "UPDATE proces_linia
					SET ordre='" . $gordre . "'
					WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND ordre='" . $gordredown . "' ";
                mysqli_query($conn,$query) or die('Error, insert query failed');

                $query2 = "UPDATE proces_linia
					SET ordre='" . $gordredown . "'
					WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND categoria='" . $gcat . "' ";
                mysqli_query($conn,$query2) or die('Error, insert query2 failed');
            }

            if ($gactiu != "") {
                $query3 = "UPDATE proces_linia
				SET actiu='" . $gactiu . "'
				WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND categoria='" . $gcat . "' ";
                mysqli_query($conn,$query3) or die('Error, insert query3 failed');
            }

            if ($elim != "") {
                $query4 = "DELETE FROM proces_linia
				WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND categoria='" . $gcat . "' ";
                mysqli_query($conn,$query4) or die('Error, insert query4 failed');
            }

            ?>

            <div class="contenidor_fac" style="border: 1px solid #66FF66; margin-bottom: 20px;">

                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr class="cos_majus">
                        <td width="20%" align="center">PROCES</td>
                        <td width="20%" align="center">GRUP</td>
                        <td width="20%" align="center">CATEGORIA</td>
                        <td width="10%" align="center">ORDRE</td>
                        <td width="20%" align="center">ACTIVAT</td>
                        <td width="10%" align="center">ELIMINAR</td>
                    </tr>

                    <?php

                    $select8 = "SELECT MAX(ordre) FROM proces_linia
	WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' ";

                    $query8 = mysqli_query($conn,$select8);

                    if (!$query8) {
                        die('Invalid query8: ' . mysqli_error($conn));
                    }

                    list($max) = mysqli_fetch_row($query8);

                    $select5 = "SELECT categoria,actiu FROM proces_linia
	WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "'
	ORDER BY ordre ";

                    $query5 = mysqli_query($conn,$select5);

                    if (!$query5) {
                        die('Invalid query5: ' . mysqli_error($conn));
                    }

                    $k = 1;
                    while (list($scat, $sactiu) = mysqli_fetch_row($query5)) {
                        $ord = $k;

                        $query7 = "UPDATE proces_linia
			  SET ordre='" . $ord . "'
		  	  WHERE proces='" . $gproc . "' AND grup='" . $ggrup . "' AND categoria='" . $scat . "' ";
                        mysqli_query($conn,$query7) or die('Error, insert query7 failed');

                        $checked1 = "";
                        $checked2 = "";
                        if ($sactiu == "activat") {
                            $checked1 = "checked";
                        } else {
                            $checked2 = "checked";
                        }
                        $ud = "";
                        if ($max == 1) {
                            $ud = "";
                        } else {
                            if ($k == 1) {
                                $ud = "<a href='associar2.php?id=" . $gproc . "&id2=" . $ggrup . "&id3=" . $scat . "&id4=" . $ord . "&id5=down'>baixa</a>";
                            } elseif ($k == $max) {
                                $ud = "<a href='associar2.php?id=" . $gproc . "&id2=" . $ggrup . "&id3=" . $scat . "&id4=" . $ord . "&id5=up'>puja</a>";
                            } else {
                                $ud = "<a href='associar2.php?id=" . $gproc . "&id2=" . $ggrup . "&id3=" . $scat . "&id4=" . $ord . "&id5=up'>puja</a>
	<a href='associar2.php?id=" . $gproc . "&id2=" . $ggrup . "&id3=" . $scat . "&id4=" . $ord . "&id5=down'>baixa</a>";
                            }
                        }
                        ?>

                        <tr class="cos">
                            <td align="center">
                                <input type="text" name="proc" id="proc" value="<?php echo $gproc; ?>" size="15"
                                       maxlength="30" readonly></td>
                            </td>
                            <td align="center">
                                <input type="text" name="grup" id="grup" value="<?php echo $ggrup; ?>" size="15"
                                       maxlength="30" readonly></td>
                            </td>
                            <td align="center">
                                <input type="text" name="cat" id="cat" value="<?php echo $scat; ?>" size="15"
                                       maxlength="30" readonly></td>
                            </td>
                            <td align="center">
                                <?php echo $ud; ?>
                            </td>
                            <td align="center">
                                si<input type="radio" name="actiu<?php echo $k; ?>" value="si" <?php echo $checked1; ?>
                                         id="actiu<?php echo $k; ?>"
                                         onClick="javascript:window.location = 'associar2.php?id=<?php echo $gproc; ?>&id2=<?php echo $ggrup; ?>&id3=<?php echo $scat; ?>&id6=activat';">
                                no<input type="radio" name="actiu<?php echo $k; ?>" value="no"
                                         id="actiu<?php echo $k; ?>" <?php echo $checked2; ?>
                                         onClick="javascript:window.location = 'associar2.php?id=<?php echo $gproc; ?>&id2=<?php echo $ggrup; ?>&id3=<?php echo $scat; ?>&id6=desactivat';">
                            </td>
                            <td align="center">
                                <a href='associar2.php?id=<?php echo $gproc; ?>&id2=<?php echo $ggrup; ?>&id3=<?php echo $scat; ?>&id7=borrar'
                                   onClick='if(confirm("Estas segur que vols eliminar l associació <?php echo $gproc . "-" . $ggrup . "-" . $scat; ?>?") == false){return false;}'>X</a>
                            </td>
                        </tr>

                        <?php
                        $k++;
                    }
                    ?>

                </table>
            </div>
            <p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">Per ordenar les categories fes
                servir els botons de PUJA i BAIXA.
                Per activar o desactivar clica el botó desitjat. Per borrar clica sobre la X de la columna ELIMINAR
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
