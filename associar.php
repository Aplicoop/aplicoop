<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>associar categories, processos i grups ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #66FF66;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='associar.php'>associar categories, processos i grups</a>
            </p>
            <p class="h1" style="background: #66FF66; text-align: left; padding-left: 20px;">
                Associar categories, processos i grups
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width:200px;" type="button" value="CREAR NOVA ASSOCIACIÓ"
       onClick="javascript:window.location = 'associar3.php';">
</span>
            </p>

            <div class="contenidor_fac" style="border: 1px solid #66FF66; width:800px; margin-bottom: 20px;">
                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr class="cos_majus">
                        <td width="25%" align="center">procés</td>
                        <td width="25%" align="center">grup</td>
                        <td width="25%" align="center">categories</td>
                    </tr>
                    <?php

                    include 'config/configuracio.php';

                    $taula = "SELECT nom, grup FROM processos
		GROUP BY nom, grup";

                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }

                    while (list($proc, $grup) = mysqli_fetch_row($result)) {

                        echo "<tr class='cos'><td align='center'>" . $proc . " </td>
<td align='center'>" . $grup . "</td>
<td align='center'><a href='associar2.php?id=" . $proc . "&id2=" . $grup . "'>E</a></td>
</tr>
";

                    }

                    echo "</table></div>";

                    ?>

                    <p class="cos2" style="clear: both; text-align: center;">
                        Per editar les dades d'una associació o per eliminar-la clica sobre la E a la columna de
                        categories
                    </p>

                </table>
    </body>
    </html>


    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
