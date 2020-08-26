<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    /// variables GET només si ve de cistella_prod.php
    /// per canviar preus d'un sol producte

    $gprodref = $_GET['id'];
    $gdata = $_GET['id3'];
    $gcat = $_GET['id4'];
    $gvis = $_GET['id5'];
    $gproces = $_GET['id6'];
    $ggrup = $_GET['id7'];
    $gnumfact = $_GET['id8'];
    $gfam_cist2 = $_GET['id9'];


/// variable pprov per elegir només la llista de productes
/// d'una proveïdora
    $pprov = $_POST['prov'];

/// variables POST per guardar els canvis ///
    $pref = $_POST['ref'];
    $ppreusi = $_POST['preusi'];
    $piva = $_POST['iva'];
    $pmarge = $_POST['marge'];
    $pdescompte = $_POST['descompte'];
    $ppreusiprevi = $_POST['preusiprevi'];
    $pivaprevi = $_POST['ivaprevi'];
    $pmargeprevi = $_POST['margeprevi'];
    $pdescompteprevi = $_POST['descompteprevi'];

    $files = count($ppreusi);

    include 'config/configuracio.php';

/// Busquem nomprod i nomprov a partir de prodref ////
    if ($gprodref) {
        $query0 = "SELECT nom, proveidora FROM productes WHERE ref='$gprodref'";
        $result0 = mysqli_query($conn,$query0);
        if (!$result0) {
            die("Query0 to show fields from table failed");
        }
        list($gnomprod, $gprov) = mysqli_fetch_row($result0);
    }
    ///////////

    ?>

    <html lang="es">
    <head>

        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>canvi preu massiu ::: la coope</title>

        <script language="javascript" type="text/javascript">

            function validar_Form() {

                var preu = new Array();
                var mar = new Array();
                var desc = new Array();
                var i;
                var dectext;
                var dectext2;

                var len = this.document.canvi.elements['preusi[]'].length;
                if (len == undefined) len = 1;

                for (i = 0; i < len; i++) {
                    preu[i] = document.getElementById("preusi" + i).value;
                    mar[i] = document.getElementById("marge" + i).value;
                    desc[i] = document.getElementById("descompte" + i).value;

                    if (isNaN(preu[i])) {
                        alert('A preu sense iva: només s/accepten numeros i el punt decimal');
                        document.getElementById("preusi" + i).focus();
                        return false;
                        break;
                    }

                    if (preu[i] < 0) {
                        alert('A preu sense iva: el numero ha de ser superior que 0');
                        document.getElementById("preusi" + i).focus();
                        return false;
                        break;
                    }

                    if (preu[i].indexOf('.') == -1) preu[i] += ".";
                    dectext = preu[i].substring(preu[i].indexOf('.') + 1, preu[i].length);

                    if (dectext.length > 2) {
                        alert('A preu sense iva: el numero de decimals no pot ser superior a 2');
                        document.getElementById("preusi" + i).focus();
                        return false;
                        break;
                    }

                    if (isNaN(mar[i])) {
                        alert('A marge: només s/accepten numeros i el punt decimal');
                        document.getElementById("marge" + i).focus();
                        return false;
                        break;
                    }

                    if (mar[i].indexOf('.') == -1) mar[i] += ".";
                    dectext2 = mar[i].substring(mar[i].indexOf('.') + 1, mar[i].length);

                    if (dectext2.length > 2) {
                        alert('A marge: el numero de decimals no pot ser superior a 2');
                        document.getElementById("marge" + i).focus();
                        return false;
                        break;
                    }

                    if (mar[i] < 0) {
                        alert('A marge: el numero ha de ser superior que 0');
                        document.getElementById("marge" + i).focus();
                        return false;
                        break;
                    }

                    if (mar[i] >= 100000) {
                        alert('A marge: el numero ha de ser inferior que 1.000 -o bé 100.000%-');
                        document.getElementById("marge" + i).focus();
                        return false;
                        break;
                    }

                    if (mar[i] > 100) {
                        var answer = confirm("Has ficat un marge superior al 100% \nD'acord: Continuar \nCancelar: Tornar a omplir el camp marge");
                        if (answer) {
                            return true;
                        }
                        else {
                            document.getElementById("marge" + i).focus();
                            return false;
                            break;
                        }
                    }

                    if (isNaN(desc[i])) {
                        alert('A descompte: només s/accepten numeros i el punt decimal');
                        document.getElementById("descompte" + i).focus();
                        return false;
                        break;
                    }

                    if (desc[i].indexOf('.') == -1) desc[i] += ".";
                    dectext2 = desc[i].substring(desc[i].indexOf('.') + 1, desc[i].length);

                    if (dectext2.length > 2) {
                        alert('A descompte: el numero de decimals no pot ser superior a 2');
                        document.getElementById("descompte" + i).focus();
                        return false;
                        break;
                    }

                    if (desc[i] < 0) {
                        alert('A descompte: el numero ha de ser superior que 0');
                        document.getElementById("descompte" + i).focus();
                        return false;
                        break;
                    }

                    if (desc[i] >= 100) {
                        alert('A descompte: el numero ha de ser inferior que 100');
                        document.getElementById("descompte" + i).focus();
                        return false;
                        break;
                    }

                }
                return true;
            }

        </script>
    </head>

    <?php
/// si hi ha vartables GET llavors venim de cistelles_prod.php o
/// i només canviem un producte
/// sino continua normal

    if ($gprodref != "") {
        $glink = 'canvi_massiu_productes.php?id=' . $gprodref . '&id3=' . $gdata .
            '&id4=' . $gcat . '&id5=' . $gvis . '&id6=' . $gproces . '&id7=' . $ggrup;
        $where = "WHERE ref='" . $gprodref . "'";
        $title = "Producte " . $gnomprod . " - Proveïdora " . $gprov;
        $gdisplay = 'style="display:none"';
        if ($gfam_cist2 != "") {
            $href2 = "cistella2_fam.php?id=" . $gfam_cist2 . '&id2=' . $gdata . "&id3=" . $gproces . "&id4=" . $ggrup . "&id5=" . $gvis;
        } else {
            $href2 = "cistella_prod.php?id=" . $gprodref . "&id3=" . $gdata . "&id4=" . $gcat . "&id5=" . $gvis . "&id6=" . $gproces . "&id7=" . $ggrup;
        }
        $gbutton = '<br/>
			<p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;">
			<input class="button2" style="width:150px;" type="button" value="Tornar a Cistelles"
			onClick="window.location=\'' . $href2 . '\'"></p>';

    } else {
        $gbutton = "";
        $glink = 'canvi_massiu_productes.php';
        $gdisplay = "";

        /// si pprov es diferent de blanc llavors és que hem elegit
        /// una proveïdora. Només volem llistar els productes
        /// d'aquella proveïdora
        /// sino llistem tots els productes

        if ($pprov != "") {
            $where = "WHERE proveidora='" . $pprov . "'";
            $title = "Productes de la proveïdora " . $pprov;
        } else {
            $where = "";
            $title = "Ordenació alfabètica de productes";
        }
    }

    ?>




<body>
    <?php include 'menu.php'; ?>

<div class="pagina" style="margin-top: 10px;">
    <div class="contenidor_1" style="border: 1px solid #990000;">
    <p class='path'>
        ><a href='admint.php'>administració</a>
        >><a href='<?php echo $glink; ?>'>canviar preus, iva, marge i descompte en llistat</a>
    </p>

    <p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
        Canviar preus, iva, marge i descompte en llistats de productes</p>

    <?php


/// si tenim variables POST guardem les dades diferents ///
/// sino llistem tots els productes ///

    if ($files != "") {

        for ($i = 0; $i < $files; $i++) {
            if ($ppreusiprevi[$i] != $ppreusi[$i]) {
                $query6 = "UPDATE productes SET preusi=" . $ppreusi[$i] . "
				WHERE ref='" . $pref[$i] . "'";
                mysqli_query($conn,$query6) or die('Error, update query6 failed');
                $lp[$i] = "preu sense iva: " . $ppreusi[$i] . "€";
            }

            if ($pivaprevi[$i] != $piva[$i]) {
                $query7 = "UPDATE productes SET iva=" . $piva[$i] . "
				WHERE ref='" . $pref[$i] . "'";
                mysqli_query($conn,$query7) or die('Error, update query6 failed');
                $li[$i] = "iva: " . $piva[$i];
            }

            if ($pmargeprevi[$i] != $pmarge[$i]) {
                $pmarge[$i] = $pmarge[$i] / 100;
                $query8 = "UPDATE productes SET marge=" . $pmarge[$i] . "
				WHERE ref='" . $pref[$i] . "'";
                mysqli_query($conn,$query8) or die('Error, update query8 failed');
                $lm[$i] = "marge: " . $pmarge[$i];
            }

            if ($pdescompteprevi[$i] != $pdescompte[$i]) {
                $pdescompte[$i] = $pdescompte[$i] / 100;
                $query8 = "UPDATE productes SET descompte=" . $pdescompte[$i] . "
				WHERE ref='" . $pref[$i] . "'";
                mysqli_query($conn,$query8) or die('Error, update query8 failed');
                $ld[$i] = "descompte: " . $pdescompte[$i];
            }

            ///Si s'ha produït algun canvi l'imprimim en pantalla///
            if ($lp[$i] != "" OR $li[$i] != "" OR $lm[$i] != "" OR $ld[$i] != "") {
                /// Busquem nomprod i nomprov a partir de ref ////
                $query00 = "SELECT nom, proveidora FROM productes WHERE ref='$pref[$i]'";
                $result00 = mysqli_query($conn,$query00);
                if (!$result00) {
                    die("Query00 to show fields from table failed");
                }
                list($pnomprod, $pprov) = mysqli_fetch_row($result00);
                ///////////

                $linia = $pnomprod . "-" . $pprov . "-" . $lp[$i] . "-" . $li[$i] . "-" . $lm[$i] . "-" . $ld[$i];
                echo "<p>" . $linia . "</p>";
            }
            /////
        }
        echo $gbutton;
    } else {
        ?>

        <form action="" method="post" name="prod" id="prod">
            <table width="80%" align="center" <?php echo $gdisplay; ?>>

                <tr style="padding-top: 10px;" class="cos_majus">
                    <td width="33%" align="center">Proveidores</td>
                </tr>

                <tr style="padding-bottom: 10px;">
                    <td align="center">
                        <SELECT name="prov" id="prov" size="1" maxlength="30"
                                onChange="document.forms['prod'].submit()">
                            <option value="">elegeix una proveïdora</option>


                            <?php
                            $select3 = "SELECT nom FROM proveidores ORDER BY nom";
                            $query3 = mysqli_query($conn,$select3);
                            if (!$query3) {
                                die('Invalid query3: ' . mysqli_error($conn));
                            }
                            while (list($sprov) = mysqli_fetch_row($query3)) {
                                if ($pprov == $sprov) {
                                    echo '<option value="' . $sprov . '" selected>' . $sprov . '</option>';
                                } else {
                                    echo '<option value="' . $sprov . '">' . $sprov . '</option>';
                                }
                            }
                            ?>

                    </td>

                </tr>
            </table>
        </form>

        <FORM action="" method="POST" name="canvi" id="canvi" onSubmit="return validar_Form();">
            <div class="contenidor_fac" style="border: 1px solid #990000; max-height: 350px; overflow: scroll; overflow-x: hidden;
margin-bottom: 20px; padding-bottom: 20px;">

                <?php

                print ('<p class="h1"
		style="background: #990000; font-size:14px; text-align: left;
		height: 20px; padding-left: 20px;">' . $title . '</p>');

                print('<table width="95%" align="center" style="padding:0px;" >');

                $sel = "SELECT ref,nom,proveidora,preusi,iva,marge,descompte FROM productes " . $where . " ORDER BY nom";
                $result = mysqli_query($conn,$sel);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }

                print('<tr class="cos_majus">
		<td width="24%" align="center">nom</td>
		<td width="20%" align="center">proveïdora</td>
		<td width="10%" align="center">preu sense iva</td>
		<td width="10%" align="center">iva</td>
		<td width="18%" align="center">marge</td>
		<td width="18%" align="center">descompte</td>
		</tr>');

                $id = 0;
                $contador = 0;
                while (list($prodref, $nomprod, $nomprov, $preusi, $iva, $marge, $descompte) = mysqli_fetch_row($result)) {
                    $marge = $marge * 100;
                    $descompte = $descompte * 100;
                    /// Si existeix GET i només busquem un sol producte llavors no volem que tingui l'enllaç
                    /// a poder editar el producte perquè volem que pugui tornar a cistella_prod.php sense
                    /// complicacions
                    if ($gprodref != "") {
                        $nomtag = $nomprod;
                    } else {
                        $nomtag = "<a href='editprod.php?id=" . $prodref . "'>" . $nomprod . "</a>";
                    }
                    ?>

                    <tr class='cos'>
                        <td align='center'>
                            <?php echo $nomtag; ?>
                            <input type=hidden name="ref[]" id="ref<?php echo $id; ?>" value="<?php echo $prodref; ?>">
                        </td>

                        <td align='center'><?php echo $nomprov; ?></td>

                        <td align='center'>
                            <input align="center" name="preusi[]" id="preusi<?php echo $id; ?>" type="TEXT"
                                   value="<?php echo $preusi; ?>" maxlength="7" size="5"></td>
                        <input type=hidden name="preusiprevi[]" id="preusiprevi<?php echo $id; ?>"
                               value="<?php echo $preusi; ?>"></td>

                        <td align='center'>
                            <SELECT name="iva[]" id="iva<?php echo $id; ?>" size="1" maxlenght="3">

                                <?php
                                $sele0 = "";
                                $sele1 = "";
                                $sele2 = "";
                                $sele3 = "";
                                if ($iva == 0) $sele0 = "selected";
                                if ($iva == 0.21) $sele1 = "selected";
                                if ($iva == 0.10) $sele2 = "selected";
                                if ($iva == 0.04) $sele3 = "selected";
                                ?>

                                <option value="0" <?php echo $sele0; ?>>sense iva</option>
                                <option value="0.21" <?php echo $sele1; ?>>21%</option>
                                <option value="0.10" <?php echo $sele2; ?>>10%</option>
                                <option value="0.04" <?php echo $sele3; ?>>4%</option>
                            </SELECT>
                            <input type=hidden name="ivaprevi[]" id="ivaprevi<?php echo $id; ?>"
                                   value="<?php echo $iva; ?>">
                        </td>

                        <td align='center'>
                            <input align="center" name="marge[]" id="marge<?php echo $id; ?>" type="TEXT"
                                   value="<?php echo $marge; ?>" maxlength="7" size="5">%
                            <input type=hidden name="margeprevi[]" id="margeprevi<?php echo $id; ?>"
                                   value="<?php echo $marge; ?>">
                        </td>

                        <td align='center'>
                            <input align="center" name="descompte[]" id="descompte<?php echo $id; ?>" type="TEXT"
                                   value="<?php echo $descompte; ?>" maxlength="5" size="3">%
                            <input type=hidden name="descompteprevi[]" id="descompteprevi<?php echo $id; ?>"
                                   value="<?php echo $descompte; ?>">
                        </td>
                    </tr>

                    <?php
                    $contador++;
                    $id++;
                }
                print ('</table></div>');
                ?>

                <p class="linia_button2" style="background: #990000; text-align: center; vertical-align: middle;">
                    <input class="button2" name="btncanvi" type="submit" id="btncanvi" value="Acceptar">
                    <input class="button2" type="button" value="Sortir" onClick="javascript:history.go(-1);">
                </p>
        </form>
        </div>

        <p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
            Per guardar els canvis és indispensable apretar el botó GUARDAR.
            Pots buscar productes per proveïdora. Per defecte apareixen tots els
            productes ordenats per ordre alfabètic.</p>

        </div></body></html>


        <?php
    }
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
