<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $pactiu = $_POST['actiu'];
    $pgrup = $_POST['grup'];
    $ptipus = $_POST['tipus'];

    $gnom = $_GET['id'];
    $gactiu = $_GET['id2'];
    $ggrup = $_GET['id3'];
    $gtipus = $_GET['id4'];

    $gpactiu = $_GET['id5'];
    $gpgrup = $_GET['id6'];
    $gptipus = $_GET['id7'];

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>crear/editar families ::: la coope</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid orange;">

            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='editfamilies3.php'>crear i editar famílies</a> </td></tr>
            </p>
            <p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
                Crear i editar famílies
<span style="display: inline; float: right; text-align: center; vertical-align: middle;
padding: 2px 50px 2px 0px;">
<input class="button2" style="width: 150px;" type="button" value="CREAR FAMILIA NOVA"
       onClick="javascript:window.location = 'createfam.php';">
</span>
            </p>


            <?php
            if ($gactiu != "") {
                $query2 = "UPDATE usuaris
			SET tipus2='" . $gactiu . "'
			WHERE nom='" . $gnom . "' ";
                mysqli_query($conn,$query2) or die('Error, insert query2 failed');
                $pactiu = $gpactiu;
                $pgrup = $gpgrup;
                $ptipus = $gptipus;
            }

            if ($ggrup != "") {
                $query3 = "UPDATE usuaris
			SET dia='" . $ggrup . "'
			WHERE nom='" . $gnom . "' ";
                mysqli_query($conn,$query3) or die('Error, insert query3 failed');
                $pactiu = $gpactiu;
                $pgrup = $gpgrup;
                $ptipus = $gptipus;
            }

            if ($gtipus != "") {
                $query4 = "UPDATE usuaris
			SET tipus='" . $gtipus . "'
			WHERE nom='" . $gnom . "' ";
                mysqli_query($conn,$query4) or die('Error, insert query4 failed');
                $pactiu = $gpactiu;
                $pgrup = $gpgrup;
                $ptipus = $gptipus;
            }
            ?>

            <table width="80%" align="center" style="padding: 10px 0px;">
                <form action="editfamilies3.php" method="post" name="fam" id="fam">
                    <tr>
                        <td width="33%" align="center" class="form">Actiu/Baixa</td>
                        <td width="33%" align="center" class="form">Grup</td>
                        <td width="33%" align="center" class="form">Comissió</td>
                    </tr>

                    <td align="center">
                        <SELECT name="actiu" id="actiu" size="1" maxlength="5" onChange="this.form.submit()">

                            <?php

                            if ($pactiu == 'actiu') {
                                $checked1 = 'selected';
                                $checked2 = "";
                            }
                            if ($pactiu == 'baixa') {
                                $checked2 = 'selected';
                                $checked1 = "";
                            }


                            ?>
                            <option value="">Tots</option>
                            <option value="actiu" <?php echo $checked1; ?>>Actiu</option>
                            <option value="baixa" <?php echo $checked2; ?>>Baixa</option>

                        </select>
                    </td>

                    <td align="center">
                        <SELECT name="grup" id="grup" size="1" maxlength="30" onChange="this.form.submit()">
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
                        <SELECT name="tipus" id="tipus" size="1" maxlength="30" onChange="this.form.submit()">
                            <option value="">Tots</option>
                            <?php
                            if ($ptipus == 'user') {
                                $checked3 = 'selected';
                                $checked4 = "";
                                $checked5 = "";
                                $checked6 = "";
                                $checked7 = "";
                                $checked8 = "";
                            } elseif ($ptipus == 'admin') {
                                $checked3 = '';
                                $checked4 = "selected";
                                $checked5 = "";
                                $checked6 = "";
                                $checked7 = "";
                                $checked8 = "";
                            } elseif ($ptipus == 'eco') {
                                $checked3 = '';
                                $checked4 = "";
                                $checked5 = "selected";
                                $checked6 = "";
                                $checked7 = "";
                                $checked8 = "";
                            } elseif ($ptipus == 'prov') {
                                $checked3 = '';
                                $checked4 = "";
                                $checked5 = "";
                                $checked6 = "selected";
                                $checked7 = "";
                                $checked8 = "";
                            } elseif ($ptipus == 'cist') {
                                $checked3 = '';
                                $checked4 = "";
                                $checked5 = "";
                                $checked6 = "";
                                $checked7 = "selected";
                                $checked8 = "";
                            } elseif ($ptipus == 'super') {
                                $checked3 = '';
                                $checked4 = "";
                                $checked5 = "";
                                $checked6 = "";
                                $checked7 = "";
                                $checked8 = "selected";
                            }
                            echo '
<option value="user" ' . $checked3 . '>user</option>
<option value="admin" ' . $checked4 . '>admin</option>
<option value="eco" ' . $checked5 . '>eco</option>
<option value="prov" ' . $checked6 . '>prov</option>
<option value="cist" ' . $checked7 . '>cist</option>
<option value="super" ' . $checked8 . '>super</option>';
                            ?>

                        </select>
                    </td>
                </form>
                </tr></table>

            <div class="contenidor_fac" style="border: 0px solid orange; padding-bottom: 20px;">

                <?php

                if ($pactiu != "" AND $pgrup == "" AND $ptipus == "") {
                    $where = "WHERE tipus2='" . $pactiu . "'";
                    $title = "Famílies en " . $pactiu;
                } elseif ($pactiu != "" AND $pgrup != "" AND $ptipus == "") {
                    $where = "WHERE tipus2='" . $pactiu . "' AND dia='" . $pgrup . "'";
                    $title = "Famílies en " . $pactiu . " del grup " . $pgrup;
                } elseif ($pactiu != "" AND $pgrup != "" AND $ptipus != "") {
                    $where = "WHERE tipus2='" . $pactiu . "' AND dia='" . $pgrup . "' AND tipus='" . $ptipus . "'";
                    $title = "Famílies en " . $pactiu . " del grup " . $pgrup . " i " . $ptipus;
                } elseif ($pactiu != "" AND $pgrup == "" AND $ptipus != "") {
                    $where = "WHERE tipus2='" . $pactiu . "' AND tipus='" . $ptipus . "'";
                    $title = "Famílies en " . $pactiu . " del grup " . $ptipus;
                } elseif ($pactiu == "" AND $pgrup == "" AND $ptipus != "") {
                    $where = "WHERE tipus='" . $ptipus . "'";
                    $title = "Famílies del grup " . $ptipus;
                } elseif ($pactiu == "" AND $pgrup != "" AND $ptipus != "") {
                    $where = "WHERE dia='" . $pgrup . "' AND tipus='" . $ptipus . "'";
                    $title = "Famílies del grup " . $pgrup . " i " . $ptipus;
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
		<td width="20%" align="center">nom</td>
		<td width="30%" align="center">components</td>
		<td width="15%" align="center">actiu</td>
		<td width="15%" align="center">grup</td>
		<td width="20%" align="center">comissió</td>
		</tr>');

                $taula = "SELECT nom,components,tipus,tipus2,dia
		FROM usuaris " . $where . "
		ORDER BY nom";
                $result = mysqli_query($conn,$taula);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                $k = 0;
                while (list($nom, $components, $tipus, $tipus2, $dia) = mysqli_fetch_row($result))
                {
                if ($tipus2 == "actiu") {
                    $checked8 = "checked";
                    $checked9 = "";
                } else {
                    $checked9 = "checked";
                    $checked8 = "";
                }
                ?>

                <tr class='cos'>
                    <td align='center'><a href='editdadesp.php?id=<?php echo $nom; ?>'><?php echo $nom; ?></a></td>
                    <td align='center'><?php echo $components; ?></td>
                    <td align='center'>
                        si<input type='radio' name='actiu<?php echo $k; ?>' value='actiu'
                                 id='actiu<?php echo $k; ?>' <?php echo $checked8; ?>
                                 onClick="javascript:window.location='editfamilies3.php?id=<?php echo $nom; ?>&id2=actiu&id5=<?php echo $pactiu; ?>&id6=<?php echo $pgrup; ?>&id7=<?php echo $ptipus; ?>';">
                        no<input type='radio' name='actiu<?php echo $k; ?>' value='baixa'
                                 id='actiu<?php echo $k; ?>' <?php echo $checked9; ?>
                                 onClick="javascript:window.location='editfamilies3.php?id=<?php echo $nom; ?>&id2=baixa&id5=<?php echo $pactiu; ?>&id6=<?php echo $pgrup; ?>&id7=<?php echo $ptipus; ?>';">
                    </td>
                    <td align='center'>
                        <SELECT name='grup<?php echo $k; ?>' id='grup<?php echo $k; ?>' size='1' maxlength='30'
                                onchange="window.location='editfamilies3.php?id=<?php echo $nom; ?>&id3='+this.value+'&id5=<?php echo $pactiu; ?>&id6=<?php echo $pgrup; ?>&id7=<?php echo $ptipus; ?>'">

                            <?php
                            $select4 = "SELECT nom FROM grups ORDER BY nom";
                            $query4 = mysqli_query($conn,$select4);
                            if (!$query4) {
                                die('Invalid query4: ' . mysqli_error($conn));
                            }

                            while (list($wgrup) = mysqli_fetch_row($query4)) {
                                if ($wgrup == $dia) {
                                    echo '<option value="' . $wgrup . '" selected>' . $wgrup . '</option>';
                                } else {
                                    echo '<option value="' . $wgrup . '">' . $wgrup . '</option>';
                                }
                            }
                            ?>
                        </select></td>
                    <td align='center'>
                        <SELECT name="tipus<?php echo $k; ?>" id="tipus<?php echo $k; ?>" size="1" maxlength="30"
                                onchange="window.location='editfamilies3.php?id=<?php echo $nom; ?>&id4='+this.value+'&id5=<?php echo $pactiu; ?>&id6=<?php echo $pgrup; ?>&id7=<?php echo $ptipus; ?>'">

                            <?php
                            if ($tipus == user) {
                                $sel3 = 'selected';
                                $sel4 = "";
                                $sel5 = "";
                                $sel6 = "";
                                $sel7 = "";
                                $sel8 = "";
                            }
                            if ($tipus == admin) {
                                $sel4 = 'selected';
                                $sel3 = "";
                                $sel5 = "";
                                $sel6 = "";
                                $sel7 = "";
                                $sel8 = "";
                            }
                            if ($tipus == eco) {
                                $sel5 = 'selected';
                                $sel4 = "";
                                $sel3 = "";
                                $sel6 = "";
                                $sel7 = "";
                                $sel8 = "";
                            }
                            if ($tipus == prov) {
                                $sel6 = 'selected';
                                $sel4 = "";
                                $sel5 = "";
                                $sel3 = "";
                                $sel7 = "";
                                $sel8 = "";
                            }
                            if ($tipus == cist) {
                                $sel7 = 'selected';
                                $sel4 = "";
                                $sel5 = "";
                                $sel6 = "";
                                $sel3 = "";
                                $sel8 = "";
                            }
                            if ($tipus == super) {
                                $sel8 = 'selected';
                                $sel4 = "";
                                $sel5 = "";
                                $sel6 = "";
                                $sel3 = "";
                                $sel7 = "";
                            }
                            echo '<option value="user" ' . $sel3 . '>user</option>
		<option value="admin" ' . $sel4 . '>admin</option>
		<option value="eco" ' . $sel5 . '>eco</option>
		<option value="prov" ' . $sel6 . '>prov</option>
		<option value="cist" ' . $sel7 . '>cist</option>
		<option value="super" ' . $sel8 . '>super</option>
		</select></td></tr>';
                            $k++;
                            }

                            echo "</table></div></div>";

                            ?>

                            <p class="cos2" style="clear: both; text-align: center;">
                                Per editar les dades d'una família clica sobre el seu nom. Per crear una família nova
                                clica el botó superior.
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
