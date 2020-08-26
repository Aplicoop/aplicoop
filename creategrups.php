<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $nom = $_POST['nom'];
    $actiu = $_POST['actiu'];
    $notes = $_POST['notes'];

    include('config/configuracio.php');

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>crear nou grup ::: la coope</title>
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
        <div class="contenidor_1" style="border: 1px solid red;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='editgrups.php'>crear, editar i eliminar grups</a>
                >>><a href='creategrups.php'>crear nou grup</a>
            </p>
            <p class="h1" style="background: red; text-align: left; padding-left: 20px;">
                Crear nou grup</p>

            <?php

            if ($nom != "") {
                $select = "SELECT nom FROM grups
	WHERE nom='" . $nom . "' ";
                $result = mysqli_query($conn,$select) or die("Query failed. " . mysqli_error($conn));

                if (mysqli_num_rows($result) == 1) {
                    die
                    ("<p class='comment'>No es pot crear de nou el grup " . $nom . " perquè ja existeix. </p>");
                } else {
                    $query2 = "INSERT INTO grups
			VALUES ('" . $nom . "', '" . $actiu . "', '" . $notes . "') ";

                    mysqli_query($conn,$query2) or die('Error, insert query2 failed');

                    echo
                        "<p class='comment'>Un nou procés s'ha introduit a la base de dades:</p>
			<p class='cos2'>NOM: " . $nom . "</p>
			<p class='cos2'>ACTIU: " . $actiu . "</p>
			<p class='cos2'>NOTES: " . $notes . "</p>";
                }
            } else {
                ?>
                <div class="contenidor_fac" style="border: 1px solid red; width: 600px; margin-bottom: 20px;">
                    <form action="creategrups.php" method="post" name="frmeditdadesp" id="frmeditdadesp"
                          onSubmit="return validate_form();">
                        <table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                            <tr>
                                <td class="cos_majus">Nom</td>
                                <td>
                                    <input type="text" name="nom" id="nom" size="10" maxlength="30">
                                </td>
                            </tr>

                            <tr>
                                <td class="cos_majus">Activitat</td>
                                <td>
                                    <SELECT name="actiu" id="actiu" size="1" maxlength="12">
                                        <OPTION value="actiu" selected>Actiu
                                        <OPTION value="no actiu">No actiu
                                    </SELECT></td>
                            </tr>
                            <tr>
                                <td class="cos_majus">Comentaris</td>
                                <td>
                                    <textarea name="notes" cols="65" rows="4" id="notes"></textarea></td>
                            </tr>
                        </table>
                </div>

                <p class="linia_button2" style="background: red; text-align: center; vertical-align: middle;">
                    <input type="submit" value="GUARDAR">
                    <input type="button" value="SORTIR" onClick="javascript:window.location = 'editgrups.php';">
                </p>
                <?php
            }
            ?>
            <p class="cos2" style="clear: both; text-align: center;">
                Per canviar les dades clica el botó GUARDAR</p>
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
