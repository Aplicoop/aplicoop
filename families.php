<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $pactiu = isset($_POST['actiu']) ? $_POST['actiu'] : 'actiu';
    $pgrup = $_POST['grup'];
    $ptipus = $_POST['tipus'];
    $pkuota = $_POST['kuota'];

    include 'config/configuracio.php';

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>llistat families ::: la coope</title>

    </head>
    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">

        <div class="contenidor_1" style="border: 1px solid orange;">

            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='families.php'>llistat famílies</a>
            </p>
            <p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
                Llistat famílies
            </p>

            <table width="80%" align="center" style="padding: 10px 0px;">
                <form action="families.php" method="post" name="fam" id="fam">
                    <tr>
                        <td width="33%" align="center" class="form">Actiu/Baixa</td>
                        <td width="33%" align="center" class="form">Grup</td>
                        <td width="33%" align="center" class="form">Comissió</td>
                    </tr>

                    <tr>
                        <td align="center">
                            <select name="actiu" id="actiu" size="1" maxlength="5" onChange="this.form.submit()">

                                <option value="">Tots</option>
                                <option value="actiu" <?php if ($pactiu == 'actiu') {echo 'selected';} ?>>Actiu</option>
                                <option value="baixa" <?php if ($pactiu == 'baixa') {echo 'selected';} ?>>Baixa</option>

                            </select>
                        </td>

                        <td align="center">
                            <select name="grup" id="grup" size="1" maxlength="30" onChange="this.form.submit()">
                                <option value="">Tots</option>

                                <?php
                                $select3 = "SELECT nom FROM grups ORDER BY nom";
                                $query3 = mysqli_query($conn,$select3);
                                if (!$query3) {
                                    die('Invalid query3: ' . mysqli_error($conn));
                                }

                                while (list($sgrup) = mysqli_fetch_row($query3)) {
                                    if ($pgrup == $sgrup) {
                                        echo '<option value="' . $sgrup . '" selected>' . $sgrup . '</option>';
                                    } else {
                                        echo '<option value="' . $sgrup . '">' . $sgrup . '</option>';
                                    }
                                }

                                ?>

                            </select>
                        </td>
                        <td align="center">
                            <select name="tipus" id="tipus" size="1" maxlength="30" onChange="this.form.submit()">
                                <option value="">Tots</option>

<option value="user" <?php if ($ptipus == 'user') {echo "selected";} ?>>user</option>
<option value="admin" <?php if ($ptipus == 'admin') {echo "selected";} ?>>admin</option>
<option value="eco" <?php if ($ptipus == 'eco') {echo "selected";} ?>>eco</option>
<option value="prov" <?php if ($ptipus == 'prov') {echo "selected";} ?>>prov</option>
<option value="cist" <?php if ($ptipus == 'cist') {echo "selected";} ?>>cist</option>
<option value="super" <?php if ($ptipus == 'super') {echo "selected";} ?>>super</option>';


                            </select>
                        </td>
                </form>
                </tr></table>

            <div class="contenidor_fac" style="border: 1px solid orange; max-height: 350px; overflow: scroll; overflow-x: hidden;
margin-bottom: 20px; padding-bottom: 20px;">

                <?php

                if ($pactiu != "" AND $pgrup == "" AND $ptipus == "") {
                    $where = "WHERE tipus2='" . $pactiu . "'";
                    $title = "Famílies en " . $pactiu;
                } elseif ($pactiu != "" AND $pgrup != "" AND $ptipus == "") {
                    $where = "WHERE tipus2='" . $pactiu . "' AND dia='" . $pgrup . "'";
                    $title = "Famílies en " . $pactiu . " del grup " . $pgrup;
                } elseif ($pactiu != "" AND $pgrup != "" AND $ptipus != "") {
                    $where = "WHERE tipus2='" . $pactiu . "' AND dia='" . $pgrup . "' AND tipus='" . $ptipus . "'";
                    $title = "Famílies en " . $pactiu . " del grup " . $pgrup . " i de la comissió " . $ptipus;
                } elseif ($pactiu != "" AND $pgrup == "" AND $ptipus != "") {
                    $where = "WHERE tipus2='" . $pactiu . "' AND tipus='" . $ptipus . "'";
                    $title = "Famílies en " . $pactiu . " de la comissió " . $ptipus;
                } elseif ($pactiu == "" AND $pgrup == "" AND $ptipus != "") {
                    $where = "WHERE tipus='" . $ptipus . "'";
                    $title = "Famílies de la comissió " . $ptipus;
                } elseif ($pactiu == "" AND $pgrup != "" AND $ptipus != "") {
                    $where = "WHERE dia='" . $pgrup . "' AND tipus='" . $ptipus . "'";
                    $title = "Famílies del grup " . $pgrup . " i de la comissió " . $ptipus;
                } elseif ($pactiu == "" AND $pgrup != "" AND $ptipus == "") {
                    $where = "WHERE dia='" . $pgrup . "'";
                    $title = "Famílies del grup " . $pgrup;
                } elseif ($pactiu == "" AND $pgrup == "" AND $ptipus == "") {
                    $where = "";
                    $title = "Totes les famílies";
                }

                print ('<p class="h1"
		style="background: orange; font-size:14px; text-align: left;
		height: 20px; padding-left: 20px;">
		' . $title . '
		</p>');

                print('<table width="100%" align="center" cellspading="5" cellspacing="5" >
		<tr class="cos_majus">
		<td align="center" width="20%">nom</td>
		<td align="center" width="30%">components</td>
		<td align="center" width="15%">telefono</td>
    <td align="center" width="35%">email</td>
    <td align="center" width="35%">email alt</td> </tr>');

                $taula = "SELECT nom,components,tel1,email1,email2 FROM usuaris " . $where . " ORDER BY nom";
                $result = mysqli_query($conn,$taula);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }

                while (list($nom, $components, $tel1, $email1,$email2) = mysqli_fetch_row($result)) {
                    echo "<tr class='cos'>
		<td align='center'><a href='vis_user.php?id=" . $nom . "'>" . $nom . " </a></td>
		<td align='center'>" . $components . "</td>
		<td align='center'>" . $tel1 . "</td>
    <td align='center'>" . $email1 . "</td>
    <td align='center'>" . $email2 . "</td>
		</tr>";
                }

                echo "</table></div></div>";

                ?>

                <p class="cos2" style="clear: both; text-align: center;">
                    Per veure la fitxa completa d'una família clicka sobre el seu nom
                </p>
            </div>
    </body>
    </html>


    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
