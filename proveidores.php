<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>proveidores ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #990000;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='proveidores.php'>editar, crear, eliminar proveïdores</a>
            </p>
            <p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
                Proveïdores
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width: 180px;" type="button" value="CREAR NOVA PROVEIDORA"
       onClick="javascript:window.location = 'createprov.php'">
</span>
            </p>

            <div class="contenidor_fac" style="border: 1px solid #990000; width: 600px;">
                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr class='cos_majus'>
                        <?php

                        include 'config/configuracio.php';

                        $taula = "SELECT nom FROM proveidores ORDER BY nom";

                        $result = mysqli_query($conn,$taula);
                        if (!$result) {
                            die('Invalid query: ' . mysqli_error($conn));
                        }

                        $i = 0;
                        while (list($nomprov) = mysqli_fetch_row($result)) {

                            echo "<td width='33%' align='center'><a href='editprov.php?id=" . $nomprov . "'>" . $nomprov . " </a></td>";
                            $i++;

                            if ($i == 3) {
                                echo "</tr><tr class='cos_majus'>";
                                $i = 0;
                            }
                        }

                        echo "</tr></table></div>";

                        ?>
                        <p class="cos2" style="clear: both; text-align: center;">
                            Per editar o eliminar una proveïdora clicka sobre el seu nom i t'apareixerà la seva
                            fitxa</p>

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
