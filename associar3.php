<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $pd = $_POST['pd'];
    $pd2 = explode("-", $pd);
    $proc = $pd2[0];
    $grup = $pd2[1];
    $cat = $_POST['cat'];

    include('config/configuracio.php');

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>crear nova associació ::: la coope</title>
    </head>

    <script languaje="javascript">

        function validate_form(form) {
            var nom = document.getElementById("nom").value;

            if (nom == "") {
                alert("T'has deixat el nom en blanc");
                document.getElementById("nom").focus();
                return false;
            }
            return true;
        }

    </script>


    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #66FF66;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a class='Estilo2' href='associar.php'>crear i editar associacions</a>
                >>><a class='Estilo2' href='associar3.php'>crear nova associació</a>
            </p>

            <p class="h1" style="background: #66FF66; text-align: left; padding-left: 20px;">
                Crear nova associació
            </p>

            <?php

            if ($proc != "") {
                $select = "SELECT proces,grup,categoria FROM proces_linia
	WHERE proces='" . $proc . "' AND grup='" . $grup . "' AND categoria='" . $cat . "' ";
                $result = mysqli_query($conn,$select) or die('Query failed. ' . mysqli_error($conn));
                if (mysqli_num_rows($result) == 1) {
                    echo
                        "<p class='comment'>L'associació de " . $proc . ", " . $grup . " i " . $cat . " no es pot crear de nou perquè ja existeix.
	</p>";
                } else {
                    $select8 = "SELECT MAX(ordre) FROM proces_linia
		WHERE proces='" . $proc . "' AND grup='" . $grup . "' ";
                    $query8 = mysqli_query($conn,$select8);
                    if (!$query8) {
                        die('Invalid query8: ' . mysqli_error($conn));
                    }

                    list($max) = mysqli_fetch_row($query8);
                    $ordre = $max + 1;
                    $actiu = 'activat';

                    $query2 = "INSERT INTO proces_linia
		VALUES ('" . $proc . "', '" . $grup . "', '" . $cat . "', '" . $ordre . "', '" . $actiu . "') ";

                    mysqli_query($conn,$query2) or die('Error, insert query2 failed');

                    echo
                        "<p class='comment'>Una nova associació s'ha introduit a la base de dades:</p>
		<p class='cos2'>PROCÉS: " . $proc . "<br/>
		GRUP: " . $grup . "<br/>
		CATEGORIA: " . $cat . "<br/>
		ORDRE: " . $ordre . "</p>";
                }
            } else {
                ?>

                <div class="contenidor_fac" style="border: 1px solid #66FF66; width: 600px; margin-bottom: 20px;">
                    <form action="associar3.php" method="post" name="frmeditdadesp" id="frmeditdadesp"
                          onSubmit="return validate_form();">
                        <table width="70%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                            <tr>
                                <td class="cos_majus">Procés - Grup</td>
                                <td>
                                    <SELECT name="pd" id="pd" size="1" maxlength="30">

                                        <?php
                                        $select2 = "SELECT nom, grup FROM processos WHERE actiu='actiu' ORDER BY nom ASC";
                                        $query2 = mysqli_query($conn,$select2);
                                        if (!$query2) {
                                            die('Invalid query2: ' . mysqli_error($conn));
                                        }

                                        while (list($sproc, $sgrup) = mysqli_fetch_row($query2)) {
                                            echo '<OPTION value="' . $sproc . '-' . $sgrup . '">' . $sproc . '-' . $sgrup . '</OPTION>';
                                        }
                                        ?>
                                    </SELECT></td>
                            </tr>

                            <tr>
                                <td class="cos_majus">Categoria</td>
                                <td>
                                    <SELECT name="cat" id="cat" size="1" maxlength="30">

                                        <?php

                                        $select4 = "SELECT tipus FROM categoria WHERE actiu=1 ORDER BY tipus ASC";
                                        $query4 = mysqli_query($conn,$select4);
                                        if (!$query4) {
                                            die('Invalid query4: ' . mysqli_error($conn));
                                        }

                                        while (list($scat) = mysqli_fetch_row($query4)) {
                                            echo '<OPTION value="' . $scat . '">' . $scat;
                                        }
                                        ?>
                                    </SELECT></td>
                            </tr>

                        </table>
                </div>

                <p class="linia_button2" style="background: #66FF66; text-align: center; vertical-align: middle;">
                    <input class="button2" type="submit" value="GUARDAR">
                    <input class="button2" type="button" value="SORTIR"
                           onClick="javascript:window.location = 'associar.php';">
                </p>

                <p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
                    Per crear una associació elegeix les opcions i clica el botó GUARDAR</p>
                <?php
            }
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
