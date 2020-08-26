<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true')
{

$superuser = strtoupper($_SESSION['user']);
$user = $_SESSION['user'];
$sessionid = $_SESSION['sessionid'];

$num = $_POST["num"];
$unitat = $_POST["uni"];
$pref = $_POST["ref"];

$files = count($num);

$ptipus = $_POST['tipus'];
$paddfam = $_POST['nouf'];

$pnumcmda = $_POST['numcmda'];

$disabled = "disabled";
$disabled2 = "disabled";

if ($ptipus != "") {
    $disabled = "";
    if ($paddfam != "") {
        $disabled2 = "";
    }
}

include 'config/configuracio.php';

function deleteDades($numcmda)
{
    // borra les dades introduides inicialment
    // abans de borrar restitueix l'estoc
    $taula33 = "SELECT cl.ref, p.nom, p.proveidora, p.categoria, cat.estoc, cl.cistella
	FROM comanda_linia AS cl, productes AS p, categoria AS cat
	WHERE cl.ref=p.ref AND p.categoria=cat.tipus AND cl.numero=" . $numcmda;
    $result33 = mysqli_query($conn,$taula33);
    if (!$result33) {
        die('Invalid query33: ' . mysqli_error($conn));
    }
    while (list($ref, $prod, $prov, $cat, $estoc, $cistella) = mysqli_fetch_row($result33)) {
        if ($estoc == 'si') {
            $query34 = "UPDATE productes
			SET estoc=estoc+'$cistella'
			WHERE ref='$ref'";
            mysqli_query($conn,$query34) or die('Error, insert query34 failed');
        }
    }
    $querydel = "DELETE FROM comanda WHERE numero='$numcmda'";
    mysqli_query($conn,$querydel) or die('Error, delete querydel failed');
    $querydel2 = "DELETE FROM comanda_linia WHERE numero='$numcmda'";
    mysqli_query($conn,$querydel2) or die('Error, delete querydel2 failed');
}

function selectEstoc($se_ref)
{
    // busquem si el producte és d'estoc o no
    $selectse = "SELECT cat.estoc FROM productes AS pr, categoria AS cat
	WHERE pr.categoria=cat.tipus AND pr.ref='$se_ref'";
    $resultse = mysqli_query($conn,$selectse);
    if (!$resultse) {
        die('Invalid query selectse: ' . mysqli_error($conn));
    }
    $estocse = mysqli_fetch_row($resultse);
    return $estocse;
}

function laCuenta($numero)
{
    /// calcula el valor total de la factura ///
    $selectCuenta = "SELECT SUM(cistella*preu*(1+iva)) FROM comanda_linia WHERE numero='" . $numero . "'";
    $resultCuenta = mysqli_query($conn,$selectCuenta);
    if (!$resultCuenta) {
        die('Invalid queryCuenta: ' . mysqli_error($conn));
    }
    $cuenta = mysqli_fetch_row($resultCuenta);
    return $cuenta;
}

function selectNumFact($numero)
{
    /// Busquem el numero de factura ///
    $selectnf = "SELECT numfact FROM comanda WHERE numero='" . $numero . "'";
    $resultnf = mysqli_query($conn,$selectnf);
    if (!$resultnf) {
        die('Invalid querynf: ' . mysqli_error($conn));
    }
    $numfact = mysqli_fetch_row($resultnf);
    return $numfact;

}

?>

<html lang="es">
<head>
    <?php include 'head.php'; ?>
    <title>aplicoop - devoluciones y facturas fuera de proceso </title>
</head>

<script language="javascript" type="text/javascript">

    function validate_form() {
        var x = new Array();
        var nom = new Array();

        for (i = 0; i < this.document.frmdev.elements['num[]'].length; i++) {
            x[i] = document.getElementById("num" + i).value;
            nom[i] = document.getElementById("nom" + i).value;

            if (isNaN(x[i])) {
                alert('A ' + nom[i] + ': només s/accepten numeros i el punt decimal');
                document.getElementById("num" + i).focus();
                return false;
                break;
            }

            if (x[i] >= 100 || x[i] < 0) {
                alert('A ' + nom[i] + ': el numero ha de ser superior que 0 i inferior a 100');
                document.getElementById("num" + i).focus();
                return false;
                break;
            }

        }
        return true;
    }

</script>

<body>
<?php include 'menu.php'; ?>
<div class="page">
    <div class="container">


        <?php

        /// En aquest punt es bifurca en dos visualitzacions:
        /// la primera son els resultat a partir del submit definitiu
        /// la segona el formulari

        //////////////////////////////////////////////////////////////////////////////////////
        ///Quan li donem a acceptar fem el submit definitiu, guardem les dades de la taula///
        /// i visualitzem el resultat                                                     ///
        //////////////////////////////////////////////////////////////////////////////////////
        if (isset($_POST['acceptar']))
        {
/// Primer hem d'estar segurs que hi ha dades num ///
            $count_files = 0;
            for ($i = 0; $i < $files; $i++) {
                if ($num[$i] != "") {
                    $count_files++;
                }
            }

            if ($count_files == 0) {
                // Si no hi ha cap quantitat elegida no continua endavant //

                echo '<p class="alert alert--error">No has introducido ninguna cantidad a ningún producto</p>';

                die ('<a class="button" href="devolucions.php">Volver</a>');
            }
            //////////////////
            /// Si hi ha dades num ///
            /// creem el numero de factura o de devolució ///
            /// anotem si es factura fora de procés o devolució ///
            /// inserim les dades a la taula comandes ///
            else {

                $data_avui = date("Y-m-d");

                /// creem numero factura o devolució ///
                // trobem la darrera factura de l'any vigent
                // i creem el numero de factura següent
                // o si no existeix cap factura de l'any li donem valor 1 ///

                $currentyear = date("Y");
                $taulanf2 = "SELECT numfact
				FROM comanda
				WHERE YEAR(data2)=" . $currentyear . "
				ORDER BY numfact DESC
				LIMIT 1";
                $resultnf2 = mysqli_query($conn,$taulanf2);
                if (!$resultnf2) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                list($lastnumfact) = mysqli_fetch_row($resultnf2);

                if ($lastnumfact != "") {
                    $numfact = $lastnumfact + 1;
                } else {
                    $numfact = 1;
                }

                /// anotem si es factura fora de procés o devolució ///
                if ($ptipus == "fac") {
                    $notes = "factura fora de procés";
                }
                if ($ptipus == "dev") {
                    $notes = "devolució";
                }

                /// inserim les dades a la taula comanda ///
                $query2 = "INSERT INTO `comanda` ( `usuari`,`sessionid`,`data`,`check0`,`data2`,`check1`,`check2`,`numfact`,`notes`)
					VALUES ('$paddfam', '$sessionid', '$data_avui', '1', '$data_avui', '1', '1','$numfact','$notes')";
                mysqli_query($conn,$query2) or die('Error, insert query2 failed');
                $numcmda = mysqli_insert_id();

                /// visualitzem les dades ///
                /// l'usuari pot elegir entre
                /////// acceptar-> carrega un nou formulari en blanc
                /////// editar -> carrega el formulari amb les dades introduides i es poden canviar
                /////// eliminar -> borra tot i carrega un nou formulari en blanc
                ?>
                <div class="box">
                    <form action="devolucions.php?id=1" method="post" name="frmdev2" id="frmdev2" >
                        <input type=hidden name="numcmda" value="<?php echo $numcmda; ?>">
                        <input type=hidden name="tipus" value="<?php echo $ptipus; ?>">
                        <input type=hidden name="nouf" value="<?php echo $paddfam; ?>">

                        <div class="hidden-print  u-text-right u-mb-2">

                                <button class="button button--animated" type="submit" name="acord" id="acord">Confirmar <i class="fa fa-check" aria-hidden="true"></i></button>
                                <button class="button button--animated" type="submit" name="edit" id="edit" >Editar <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                <button class="button button--animated" type="submit" name="del" id="del"
                                       onClick="if (confirm('¿Estás seguro que quieres borrar esta factura o devolución?')) document.frmdev2.submit();">Eliminar <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                <button class="button button--animated" type="button" name="imprimir"
                                       onclick="window.print();">Imprimir <i class="fa fa-print" aria-hidden="true"></i></button>

                        </div>
                    </form>


                    <?php
                    /// Aconseguim les dades personals per a la factura ///
                    $sel = "SELECT u.nomf, u.adressf, u.niff
				FROM comanda AS c, usuaris AS u
				WHERE c.numero='$numcmda' AND c.usuari=u.nom";
                    $query = mysqli_query($conn,$sel) or die('query:' . mysqli_error($conn));
                    list($nomf, $adressf, $niff) = mysqli_fetch_row($query);

                    /// Aconseguim la data d'avui per veure ///
                    $ver_avui = date("d-m-Y");
                    $year_avui = date("Y");
                    ?>

                    <div class="row">
                        <div class="col-md-4 u-text-center u-mb-1">
                            <img id="fig" class="img--responsive" style="height:85px;" src="<?php echo $logo_factura; ?>">
                        </div>

                        <div class="col-md-8 u-text-right u-mb-1">
                            <span style="color: grey;">Factura nº </span><span class="u-text-semibold"><?php echo $numfact . "/" . $year_avui; ?></span>
                            <br/>
                            <span style="color: grey;">Fecha: </span><span class="u-text-semibold"><?php echo $ver_avui; ?></span>
                            <br/>
                            <span style="color: grey;">Familia: </span><span class="u-text-semibold"><?php echo $nomf; ?></span>
                            <br/>
                            <span style="color: grey;">Dirección: </span><span class="u-text-semibold"><?php echo $adressf; ?></span>
                            <br/>
                            <span style="color: grey;">NIF: </span><span class="u-text-semibold"><?php echo $niff; ?></span>
                        </div>
                    </div>


                    <div class="cf u-mt-2 table-responsive u-width-100">
                        <table width="100%" class="table table-striped">
                            <thead>
                            <tr  style="font-size:18px;" valign="baseline">
                                <td width="50%" align="left" class="u-text-semibold">Producto</td>
                                <td width="15%" align="center" class="u-text-semibold">Cantidad</td>
                                <td width="10%" align="center" class="u-text-semibold">PVP</td>
                                <td width="10%" align="center" class="u-text-semibold">Descuento</td>
                                <td width="10%" align="center" class="u-text-semibold">IVA</td>
                                <td width="10%" align="right" class="u-text-semibold">Total</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            /// entrem les quantitats a comanda_linia ///
                            for ($i = 0; $i < $files; $i++) {
                                if ($num[$i] != "" AND $num[$i] != 0) {
                                    /// Busquem ref,preu, iva, marge i descompte a partir de nomprod i nomprov ////
                                    $query0 = "SELECT nom, proveidora, preusi, iva, marge, descompte FROM productes
						WHERE ref='$pref[$i]'";
                                    $result0 = mysqli_query($conn,$query0);
                                    if (!$result0) {
                                        die("Query0 to show fields from table failed");
                                    }
                                    list($nomprod, $nomprov, $spreusi, $siva, $smarge, $sdescompte) = mysqli_fetch_row($result0);

                                    $pvp = $spreusi * (1 + $smarge);
                                    $pvp = sprintf("%01.2f", $pvp);

                                    /// Si es una devolució la quantitat demanada és negativa ///
                                    if ($ptipus == "dev") {
                                        $num[$i] = -$num[$i];
                                    }

                                    /// entrem les quantitats a comanda_linia ///
                                    $query4 = "INSERT INTO `comanda_linia` ( `numero`, `ref`, `cistella`, `preu`, `iva`, `descompte`)
						VALUES ('$numcmda', '$pref[$i]', '$num[$i]', '$pvp', '$siva', '$sdescompte')";
                                    mysqli_query($conn,$query4) or die('Error, insert query failed');

                                    /// restem les quantitats de l'estoc en el cas que la categoria del producte sigui d'estoc///
                                    $estocse = selectEstoc($pref[$i]);
                                    $estocse = $estocse[0];
                                    if ($estocse == 'si') {
                                        $query7 = "UPDATE productes
							SET estoc=estoc-'$num[$i]'
							WHERE ref='$pref[$i]'";
                                        mysqli_query($conn,$query7) or die('Error, update query7 failed');
                                    }
                                }
                            }
                            ///////////////////////
                            /// Les visualitzem ///
                            //////////////////////
                            $sel5 = "SELECT cl.ref, prod.nom, prod.proveidora, prod.unitat, cl.cistella, cl.preu, cl.descompte, cl.iva
				FROM comanda_linia AS cl, productes AS prod
				WHERE cl.numero='$numcmda' AND cl.ref=prod.ref
				ORDER BY prod.categoria, prod.proveidora, prod.nom";
                            $result5 = mysqli_query($conn,$sel5) or die(mysqli_error($conn));

                            $total = 0;
                            $total_import_brut = 0;
                            $totaliva = 0;
                            while (list ($ref, $nomprod, $nomprod2, $unitat, $cistella, $preu, $descompte, $iva) = mysqli_fetch_row($result5)) {
                                /// agafem la primera lletra de la unitat ///
                                $unitat1 = substr($unitat, 0, 1);

                                //calculem import brut, iva línia, subtotal linia,
                                ///i totals import brut, iva i factura
                                $importbrut = $cistella * $preu * (1 - $descompte);
                                $total_import_brut = $total_import_brut + $importbrut;
                                $subtotal = $cistella * $preu * (1 - $descompte) * (1 + $iva);
                                $subtotal = sprintf("%01.2f", $subtotal);
                                $total = $total + $subtotal;
                                $iva_linia = $cistella * $preu * (1 - $descompte) * $iva;
                                $iva_linia = sprintf("%01.2f", $iva_linia);
                                $totaliva = $totaliva + $iva_linia;

                                //iva i descompte si =0 apreixen en blanc//
                                $v_descompte = $descompte * 100;
                                $v_iva = $iva * 100;
                                if ($iva == 0) {
                                    $v_iva = "";
                                } else {
                                    $v_iva = $v_iva . " %";
                                }
                                if ($descompte == 0) {
                                    $v_descompte = "";
                                } else {
                                    $v_descompte = $v_descompte . " %";
                                }
                                ?>

                                <tr class="cos">
                                    <td><?php echo $ref . ' - ' . $nomprod; ?></td>
                                    <td align="center"><?php echo $cistella . ' - ' . $unitat1; ?>.</td>
                                    <td align="center"><?php echo $preu; ?>&#8364;</td>
                                    <td align="center"><?php echo $v_descompte; ?></td>
                                    <td align="center"><?php echo $v_iva; ?></td>
                                    <td align="right"><?php echo $subtotal; ?>&#8364;</td>
                                </tr>

                                <?php
                            }
                            $total = sprintf("%01.2f", $total);
                            $totaliva = sprintf("%01.2f", $totaliva);
                            $total_import_brut = sprintf("%01.2f", $total_import_brut);

                            ?>

                        </table>
                        <table width="100%" align="center">
                            <tr class="u-text-semibold"  style="font-size:18px;">
                                <td width="33%" align="center">Imp. Brut</td>
                                <td width="33%" align="center" >IVA</td>
                                <td width="33%" align="center">TOTAL</td>
                            </tr>
                            <tr>
                                <td align="center"><?php echo $total_import_brut; ?>&#8364;</td>
                                <td align="center"><?php echo $totaliva; ?>&#8364;</td>
                                <td align="center"><?php echo $total; ?>&#8364;</td>
                            </tr>
                        </table>
                    </div>
                    <p class="alert alert--info">
                        Tus datos proceden de un fichero del que es propietaria y responsable esta entidad, ante la cual pueden ejercitar los derechos de acceso, modificación, cancelación y oposición reconocidos por la LO 15/1999, de 13 de septiembre, de protección de datos de carácter personal.
                    </p>
                </div>

                <?php

            }
        }
        else
        {

        /////////////////////////
        /// A partir d'aquí vé el formulari  ///////
        //////////////////////////////////
        ?>

        <h1>Crear devoluciones o facturas fuera de proceso</h1>

        <div class="box">

            <?php
            ////////////////////////////
            /// Si apretem el botó D'ACORD des de la visualització de la factura ///
            /// es carrega al moneder el valor total de la factura o de la devolució ///
            /// Sino conitnua endavant ///
            ///////////////////////////////
            if (isset($_POST['acord'])) {
                $cuenta = laCuenta($pnumcmda);
                ///El valor total de la factura ha de canviar de signe
                /// ja que es resta del moneder
                $cuenta = -$cuenta[0];
                $cuenta = sprintf("%01.2f", $cuenta);
                $numfact = selectNumFact($pnumcmda);
                $numfact = $numfact[0];
                $session = date("Y-m-d H:i:s");
                $selectMoneder2 = "INSERT INTO moneder(sessio, user, data, familia, concepte, valor)
		VALUES ('" . $session . "','" . $user . "','" . date('Y-m-d') . "','" . $paddfam . "','Factura num. " . $numfact . "','" . $cuenta . "')";
                $resultMoneder2 = mysqli_query($conn,$selectMoneder2);
                if (!$resultMoneder2) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                if ($ptipus == "dev") {
                    $text = array("devuelto", "devolución");
                }
                if ($ptipus == "fac") {
                    $text = array("cargado", "factura");
                }
                $yearfact = date('Y');
                die ('<div class="alert alert--info">
		Se ha ' . $text[0] . ' ' . $cuenta . '€ al monedero de la familia ' . $paddfam . ' correspondiente a la ' . $text[1] . ' número ' . $numfact . '/' . $yearfact . '</div>
		<div class="u-text-center u-mt-2">
		<a href="devolucions.php" class="button">Volver</a></div>');
            }
            /////////////////////
            /// Si apretem el boto ELIMINAR des de la visualització de la factura///
            /// s'eliminen les dades i apareix un comentari al respecte ///
            /// Abans de borrar les dades restituim la quantitat a estoc
            /// Si no continua endavant ///
            ////////////////////
            if (isset($_POST['del'])) {
                deleteDades($pnumcmda);
                die ('<p class="error" style="font-size: 14px; padding-bottom: 50px;">
		Les dades introduides de la factura o devolució s\'han borrat correctament</p>');
            }
            ///////////////////////////////////////////////////////////////////////////
            ?>

            <form action="" method="post" name="frmdev" id="frmdev" onSubmit="return validate_form()">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipus">Tipo</label>
                            <div>
                                <SELECT class="button2" name="tipus" id="tipus" size="1" maxlength="30"
                                        onchange="document.frmdev.submit()">
                                    <option value="">-- elegir --</option>
                                    <?php
                                    $selected = "";
                                    $selected1 = "";
                                    if ($ptipus == "dev") {
                                        $selected = "selected";
                                    }
                                    if ($ptipus == "fac") {
                                        $selected1 = "selected";
                                    }
                                    ?>
                                    <option value="dev" <?php echo $selected; ?>>Devolucion</option>
                                    <option value="fac" <?php echo $selected1; ?>>Factura</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nouf">Familia</label>
                            <div>
                                <SELECT class="button2" name="nouf" id="nouf" size="1"
                                        maxlength="30" <?php echo $disabled; ?> onchange="document.frmdev.submit()">
                                    <option value="">-- elegir -- </option>

                                    <?php
                                    // Es pot elegir entre totes les famílies actives o anònim//
                                    $selected2 = "";
                                    if ($paddfam == "anom") {
                                        $selected2 = "selected";
                                    }
                                    echo '<option value="anom" ' . $selected2 . '>Anònim</option>';

                                    $taula7 = "SELECT nom FROM usuaris WHERE tipus2='actiu' ORDER BY nom";
                                    $result7 = mysqli_query($conn,$taula7);
                                    if (!$result7) {
                                        die('Invalid query7: ' . mysqli_error($conn));
                                    }

                                    while (list($sfam) = mysqli_fetch_row($result7)) {
                                        $selected3 = "";
                                        if ($paddfam == $sfam) {
                                            $selected3 = "selected";
                                        }
                                        echo '<option value="' . $sfam . '" ' . $selected3 . '>' . $sfam . '</option>';

                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="cat">Añadir a Categoria</label>
                        <div>
                            <SELECT class="button2" name="cat" id="cat" size="1" maxlength="30" <?php echo $disabled2; ?>
                                    onChange="location=this.form.cat.value">
                                <option value="">-- elegir --</option>

                                <?php

                                $sel = "SELECT tipus FROM categoria WHERE actiu='activat' ORDER BY tipus ASC";
                                $result = mysqli_query($conn,$sel);
                                if (!$result) {
                                    die('Invalid query: ' . mysqli_error($conn));
                                }

                                while (list($scat) = mysqli_fetch_row($result)) {
                                    if ($pcat == $scat) {
                                        echo '<option value="#' . $scat . '" selected>' . $scat . '</option>';
                                    } else {
                                        echo '<option value="#' . $scat . '">' . $scat . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                </div>





            <?php

            $sel = "SELECT tipus FROM categoria WHERE actiu='activat' ORDER BY tipus ASC";
            $result = mysqli_query($conn,$sel);
            if (!$result) {
                die('Invalid query: ' . mysqli_error($conn));
            }

            $color = array("#C0C000", "#00b2ff", "orange", "#b20000", "#14e500", "red", "#8524ba", "green");
            $id = 0;
            $cc = 0;
            while (list($sscat) = mysqli_fetch_row($result)) {

                $cc++;
                if ($cc == 7) {
                    $cc = 0;
                }

                $sel2 = "SELECT pr.ref,pr.nom,pr.unitat,pr.proveidora,ctg.tipus,ctg.estoc,pr.subcategoria,pr.preusi,pr.iva,
			pr.marge, pr.descompte,pr.estoc FROM productes AS pr, categoria AS ctg
			WHERE pr.categoria=ctg.tipus AND pr.categoria='$sscat' AND pr.actiu='actiu'
			ORDER BY pr.categoria, pr.nom ";

                $result2 = mysqli_query($conn,$sel2);
                if (!$result2) {
                    die('Invalid query2: ' . mysqli_error($conn));
                }

                print ('

                    <ul class="accordion">
                        <hr class="box-separator"/>
                        <li class="accordion-item">
                            <input type="checkbox"  class="accordion-check" checked>
                            <i class="accordion-icon"></i>
                            <h2 class="accordion-title box-subtitle">' . $sscat . '</h2>
                            <ul class="accordion-section row">

                ');

                $contador = 0;
                while (list($ref, $nomprod, $unitat, $prov, $categ, $ctg_estoc, $subcat, $preu, $iva, $marge, $descompte, $pr_estoc) = mysqli_fetch_row($result2)) {
                    //// Si estem editant un formulari ja fet -existeix $pnumcmda-
                    /// Hem apretat el boto EDITAR a la visualització de la factura
                    /// han d'apareixer inicialment les quantitats elegides
                    //// en un principi $num, sino num="" ////////////
                    if (isset($_POST['edit'])) {
                        $sel3 = "SELECT cistella FROM comanda_linia
					WHERE numero='$pnumcmda' AND ref='$ref'";

                        $result3 = mysqli_query($conn,$sel3);
                        if (!$result3) {
                            die('Invalid query3: ' . mysqli_error($conn));
                        }
                        list ($quantitat) = mysqli_fetch_row($result3);

                        if ($quantitat != "") {
                            /// per veure la quantitat amb els decimals imprescindibles /////
                            $r2 = round($quantitat, 2) * 1000;
                            $r1 = round($quantitat, 1) * 1000;
                            $r0 = round($quantitat) * 1000;
                            $rb = $quantitat * 1000;
                            if ($rb == $r0) {
                                $nd = 0;
                            } else {
                                if ($rb == $r1) {
                                    $nd = 1;
                                } else {
                                    if ($rb == $r2) {
                                        $nd = 2;
                                    } else {
                                        $nd = 3;
                                    }
                                }
                            }
                            $num[$id] = round($quantitat, $nd);
                            if ($ptipus == "dev") {
                                $num[$id] = -$num[$id];
                            }

                            /// recarreguem l'estoc ///
                            if ($ctg_estoc == 'si') {
                                $query34 = "UPDATE productes
							SET estoc=estoc+'$quantitat'
							WHERE ref='$ref'";
                                mysqli_query($conn,$query34) or die('Error, insert query34 failed');

                                $sel35 = "SELECT estoc FROM productes WHERE ref='$ref'";
                                $result35 = mysqli_query($conn,$sel35);
                                if (!$result35) {
                                    die('Invalid query35: ' . mysqli_error($conn));
                                }
                                $pr_estoc = mysqli_fetch_row($result35);
                                $pr_estoc = $pr_estoc[0];
                            }
                        }
                        //////////////////////////////////////
                    }

                    //// En els productes d'estoc, apareix l'estoc ////
                    //// Si l'estoc es negatiu apareix en gris ////
                    if ($ctg_estoc == 'si') {
                        $rpr_estoc = round($pr_estoc, 1);
                        $w_estoc = "[" . $rpr_estoc . "]";
                        if ($pr_estoc <= 0) {
                            $color_cos = "color: grey;";
                        } else {
                            $color_cos = "";
                        }
                    } else {
                        $w_estoc = "";
                    }

                    //// càlcul del pvp ///
                    /// inclou iva i marge, però no descompte ////
                    $pvp = $preu * (1 + $iva) * (1 + $marge);
                    $pvp = sprintf("%01.2f", $pvp);

                    //// si existeix un descompte apareix en vermell ////
                    $w_desc = "";
                    if ($descompte != 0) {
                        $descompte = $descompte * 100;
                        $w_desc = "<span class='u-text-bold'> descuento: " . $descompte . "%</span>";
                    }

                    print('
                        <li class="col-lg-6">
                            <div class="form-group product">
                                <label for="num' . $id . '">
                                    ' . $nomprod . ' (' . $pvp . ' &#8364;/' . $unitat . ') ' . $w_estoc . ' ' . $w_desc . '
                                </label>


                                <input  class="form-control" name="num[]" id="num' . $id . '" type="number" value="' . $num[$id] . '" maxlength="5" size="3" ' . $disabled2 . '  step="0.01">

                                <input type=hidden name="ref[]" value="' . $ref . '">
                                <input type=hidden name="uni[]" value="' . $unitat . '">
                                <input type=hidden name="nomp[]" id="nom' . $id . '" value="' . $nomprod . '">
                            </div>
                        </li>
                    ');

                    $id++;

                }

                print ('
                            </ul>

                        </li>
                    </ul>
                ');

            }

            /// Si hem apretat el botó EDITAR de la visualització de la factura
            /// existeix $pnumcmda vol dir que es una segona o posterior edició
            /// Fem un input invisible amb $pnumcmda
            /// borrem les dades existents amb el numero de comanda conegut ($pnumcmda)///
            if ($_POST['edit']) {
                echo '<input type="hidden" name="numcmda" id="numcmda" value="' . $pnumcmda . '">';

                $querydel = "DELETE FROM comanda WHERE numero='$pnumcmda'";
                mysqli_query($conn,$querydel) or die('Error, delete querydel failed');
                $querydel2 = "DELETE FROM comanda_linia WHERE numero='$pnumcmda'";
                mysqli_query($conn,$querydel2) or die('Error, delete querydel2 failed');
            }
            ?>


        <div class="u-text-center">
            <button class="button button--save button--animated" name="acceptar" type="submit" id="acceptar"
                    <?php echo $disabled2; ?>>Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
        </div>
        </form>
        <div class="alert alert--info">
            Omple les quantitats dels productes que vulguis i clica ACCEPTAR.
            Les devolucions s'introdueixen en positiu encara que sigui un retorn.
            Els productes estan ordenats per categoria. Només apareixen els productes actius i les categories actives.
            Per buscar un producte concret pots utilitzar l'opció de recerca del teu navegador (usualment al menu
            Editar>opció Buscar)
        </div>
    </div>
</div>

<?php
}
include 'config/disconect.php';
}
else {
    header("Location: index.php");
}
?>
