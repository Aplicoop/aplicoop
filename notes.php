<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>notes ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #a74fd7;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='notes.php'>introduir notes a l'escriptori</a>
            </p>
            <p class="h1" style="background: #a74fd7; text-align: left; padding-left: 20px;">
                Introduir notes a l'escriptori
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width:150px;" type="button" value="CREAR NOTA NOVA"
       onClick="javascript:window.location = 'editnota.php'">
</span>
            </p>

            <div class="contenidor_fac" style="border: 1px solid #a74fd7; width: 600px;">
                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr class="cos_majus">
                        <td width="10%" align="center">numero</td>
                        <td width="50%" align="center">nom</td>
                        <td width="20%" align="center">tauler</td>
                        <td width="20%" align="center">caducitat</td>
                    </tr>
                    <?php

                    include 'config/configuracio.php';

                    $taula = "SELECT numero,nom,tipus,caducitat FROM notescrip";

                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }

                    while (list($num, $nom, $tipus, $caduc) = mysqli_fetch_row($result)) {

                        list($any, $mes, $dia) = explode("-", $caduc);
                        $caduc2 = $dia . "-" . $mes . '-' . $any;

                        echo "<tr class='cos'>
<td align='center'><a href='editnota.php?id=" . $num . "'>" . $num . " </a></td>
<td align='center'><a href='editnota.php?id=" . $num . "'>" . $nom . "</td>
<td align='center'>" . $tipus . "</td>
<td align='center'>" . $caduc2 . "</td></tr>";

                    }

                    echo "</table>";

                    ?>

            </div>

            <p class="cos2" style="clear: both; text-align: center;">
                Per editar o eliminar una nota clicka sobre el seu numero o el nom i t'apareixerà la seva fitxa.
                <br/>
                Per crear una nova nota clica al botó superior.
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
