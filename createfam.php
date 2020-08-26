<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $p_nom = $_POST['nom'];
    $p_cdp = $_POST['cdp'];
    $p_cdp2 = $_POST['cdp2'];
    $p_tip = $_POST['tip'];
    $p_tip2 = $_POST['tip2'];
    $p_dia = $_POST['dia'];
    $p_comp = $_POST['components'];
    $p_tlf1 = $_POST['tlf1'];
    $p_tlf2 = $_POST['tlf2'];
    $p_email1 = $_POST['email1'];
    $p_email2 = $_POST['email2'];
    $p_nomf = $_POST['nomf'];
    $p_adressf = $_POST['adressf'];
    $p_niff = $_POST['niff'];
    $p_nota = $_POST['nota'];
    $p_kuota = $_POST['kuota'];
    $p_IBAN = $_POST['IBAN'];
    $p_domiciliacion = $_POST['domiciliacion'];
    $p_fechaalta = $_POST['fechaalta'];

    include('config/configuracio.php');

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>crear dades família ::: la coope</title>
    </head>

    <script>
        function validate_form() {

            var nom = document.getElementById("nom").value;
            var cdp = document.getElementById("cdp").value;
            var cdp2 = document.getElementById("cdp2").value;
            var comp = document.getElementById("components").value;
            var tel1 = document.getElementById("tlf1").value;
            var tel2 = document.getElementById("tlf2").value;
            var email1 = document.getElementById("email1").value;
            var email2 = document.getElementById("email2").value;
            var kuota = document.getElementById("kuota").value;
            //var fechaalta = document.getElementById("fechaalta").value;



            if (nom == "") {
                alert("T'has deixat el nom en blanc");
                document.getElementById("nom").focus();
                return false;
            }

            var illegalChars = /[\<\>\'\;\:\\\/\"\+\!\¡\º\ª\$\|\@\#\%\¬\=\?\¿\{\}\_\[\]\(\)\.\,\-\à\á\é\è\í\ì\ó\ò\ù\ú\ü\ö\ï\ë\ä\ \&\·\*]/
            if (nom.match(illegalChars)) {
                alert('A nom: només s/accepten lletres minúscules (sense accents ni dièresi) i numeros');
                document.getElementById("nom").focus();
                return false;
            }

            if (cdp == "") {
                alert("No has escrit cap clau de pas");
                document.getElementById("cdp").focus();
                return false;
            }

            if (cdp != cdp2) {
                alert("Les claus de pas no coincideixen. \n Torna a intentar-ho");
                document.getElementById("cdp").focus();
                return false;
            }

            if (cdp.match(illegalChars)) {
                alert('A Clau de Pas: només s/accepten lletres minúscules (sense accents ni dièresi) i numeros');
                document.getElementById("cdp").focus();
                return false;
            }

            if (comp == "") {
                alert("Has deixat la casella de components de la família en blanc \n Hauries de ficar el nom de les persones que conformen la família");
                document.getElementById("components").focus();
                return false;
            }

            if (tel1 == "" && tel2 == "") {
                alert("No has escrit cap telèfon de contacte");
                document.getElementById("tlf1").focus();
                return false;
            }

            if (email1 == "" && email2 == "") {
                alert("No has escrit cap correu electrònic");
                document.getElementById("email1").focus();
                return false;
            }

            if (kuota == "") {
                alert("No has elegido la kuota");
                document.getElementById("kuota").focus();
                return false;
            }
            // if (validateDate(fechaalta, 'Y-m-d') == false) {
            //     console.log(validateDate(fechaalta, 'Y-m-d'));
            //     alert("No has respetado el formato de fecha AAAA-MM-DD");
            //     document.getElementById("fechaalta").focus();
            //     return false;
            // }
            return true;
        }
    </script>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid orange;">

            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='editfamilies3.php'>crear i editar famílies</a>
                >>><a href='createfam.php'>crear nova família</a>
            </p>
            <p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
                Crear nova família
            </p>

            <div class="contenidor_fac" style=" width:500px; border: 1px solid orange; margin-bottom:20px;">
                <table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5">
                    <?php

                    if ($p_nom != ""){
                        $md5_cdp = md5($p_cdp);
                        $query2 = "INSERT INTO usuaris
	VALUES ('" . $p_nom . "', '" . $md5_cdp . "', '" . $p_tip . "', '" . $p_tip2 . "', '" . $p_dia . "', '0', '" . $p_comp . "', '" . $p_tlf1 . "', '" . $p_tlf2 . "', '" . $p_email1 . "', '" . $p_email2 . "', '" . $p_nomf . "', '" . $p_adressf . "', '" . $p_niff . "', '" . $p_nota . "','" . $p_kuota . "','" . $p_IBAN . "','" . $p_domiciliacion . "','" . $p_fechaalta . "') ";

                        mysqli_query($conn,$query2) or die('Error, insert query2 failed:' . mysqli_error($conn));

                        echo
                            "<tr><td align='center' class='cos2'>
	<p class='error' style='font-size: 14px;'>Una nueva família se ha introducido correctamente a la base de datos:</p>
	<p>NOM: " . $p_nom . "</p>
	<p>CLAU DE PAS: " . $p_cdp . "</p>
	<p>PERMISOS: " . $p_tip . "</p>
	<p>ACTIU: " . $p_tip2 . "</p>
	<p>GRUP: " . $p_dia . "</p>
	<p>COMPONENTS: " . $p_comp . "</p>
	<p>TELEFON 1: " . $p_tlf1 . "</p>
	<p>TELEFON 2: " . $p_tlf2 . "</p>
	<p>E-MAIL 1: " . $p_email1 . "</p>
	<p>E-MAIL 2: " . $p_email2 . "</p>
	<p>NOM FACTURA: " . $p_nomf . "</p>
	<p>ADREÇA FACTURA: " . $p_adressf . "</p>
	<p>NIF FACTURA: " . $p_niff . "</p>
    <p>KUOTA: " . $p_kuota . "</p>
    <p>IBAN: " . $p_IBAN . "</p>
    <p>Domiciliacion: "; if ($p_domiciliacion == 1){echo "Si";} else {echo "No";} echo "</p>
    <p>Fecha de alta: " . $p_fechaalta . "</p>
	<p>COMENTARIS: " . $p_nota . "</p>
	</td></tr>
	</table>";

                    }
                    else {
                    ?>


                    <form action="createfam.php" method="post" name="frmeditdadesp" id="frmeditdadesp"
                          onSubmit="return validate_form();">

                        <tr class="cos_majus">
                            <td>Nom</td>
                            <td>
                                <input type="text" name="nom" id="nom" size="10" maxlength="10"
                                       onkeyup="frmeditdadesp.nom.value=frmeditdadesp.nom.value.toLowerCase();">
                            </td>
                        </tr>
                        <tr class="cos_majus">
                            <td>Clau de pas</td>
                            <td>
                                <input type="text" name="cdp" id="cdp" size="10" maxlength="10"
                                       onkeyup="frmeditdadesp.cdp.value=frmeditdadesp.cdp.value.toLowerCase();"></td>
                        </tr>
                        <tr>
                            <td><span class="cos_majus">Clau de pas </span><br/><span class="cos">(repeteix per seguretat)</span>
                            </td>
                            <td>
                                <input type="text" name="cdp2" id="cdp2" size="10" maxlength="10"
                                       onkeyup="frmeditdadesp.cdp.value=frmeditdadesp.cdp.value.toLowerCase();"></td>
                        </tr>
                        <tr>
                            <td class="cos_majus">Actiu/Baixa</td>
                            <td class="cos">
                                <SELECT name="tip2" id="tip2" size="1" maxlength="5">
                                    <option value="actiu" checked>actiu</option>
                                    <option value="baixa">baixa</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="cos_majus">Grup</td>
                            <td class="cos">
                                <SELECT name="dia" id="dia" size="1" maxlength="12">

                                    <?php
                                    $select3 = "SELECT nom FROM grups ORDER BY nom";
                                    $query3 = mysqli_query($conn,$select3);
                                    if (!$query3) {
                                        die('Invalid query3: ' . mysqli_error($conn));
                                    }

                                    while (list($sgrup) = mysqli_fetch_row($query3)) {
                                        echo '<option value="' . $sgrup . '">' . $sgrup . '</option>';
                                    }
                                    ?>

                                </select></td>
                        </tr>
                        <tr>
                            <td class="cos_majus">Tipus d'usuari (permisos)</td>
                            <td class="cos">
                                <SELECT name="tip" id="tip" size="1" maxlength="10">
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                    <option value="eco">eco</option>
                                    <option value="prov">prov</option>
                                    <option value="cist">cist</option>
                                    <option value="super">super</option>
                                </select></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>Components de la família</td>
                            <td>
                                <input type="text" name="components" id="components" size="30" maxlength="100"></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>Telèfon 1</td>
                            <td>
                                <input type="text" name="tlf1" id="tlf1" size="9" maxlength="9"></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>Telèfon 2</td>
                            <td>
                                <input type="text" name="tlf2" id="tlf2" size="9" maxlength="9"></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>E-mail 1</td>
                            <td>
                                <input type="text" name="email1" id="email1" size="30" maxlength="50"></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>E-mail 2</td>
                            <td>
                                <input type="text" name="email2" id="email2" size="30" maxlength="50"></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>Nom a efectes de la factura</td>
                            <td>
                                <input type="text" name="nomf" value="<?php echo $nomf; ?>" size="30" maxlength="100">
                            </td>
                        </tr>
                        <tr class="cos_majus">
                            <td>Adreça a efectes de la factura</td>
                            <td>
                                <input type="text" name="adressf" value="<?php echo $adressf; ?>" size="30"
                                       maxlength="200"></td>
                        </tr>
                        <tr class="cos_majus">
                            <td>NIF a efectes de la factura</td>
                            <td>
                                <input type="text" name="niff" value="<?php echo $niff; ?>" size="9" maxlength="9"></td>
                        </tr>
                        <tr>
                            <td class="cos_majus">Kuota de la socia</td>
                            <td class="cos">
                                <input type="number" name="kuota" min="0" max="10" step="0.01">
                            </td>
                        </tr>
                        <tr>
                            <td class="cos_majus">IBAN</td>
                            <td class="cos">
                                <input type="text" name="IBAN" value="<?php echo $IBAN; ?>" size="24" maxlength="24">
                        </tr>
                        <tr>
                            <td class="cos_majus">Domiciliacion</td>
                            <td class="cos">
                                <SELECT name="domiciliacion" id="domiciliacion">
                                    <option value="0"></option>
                                    <option value="1">SI</option>
                                    <option value="0">NO</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="cos_majus">Fecha de alta</td>
                            <td class="cos">
                                <input type="text" name="fechaalta" placeholder="AAAA-MM-DD">
                            </td>
                        </tr>
                        <tr class="cos_majus">
                            <td>comentaris</td>
                            <td>
                                <textarea name="nota" cols="35" rows="4" id="nota"></textarea></td>
                        </tr>
                </table>


                <p class="linia_button2" style="background: orange; text-align: center; vertical-align: middle;">
                    <input class="button2" type="submit" value="GUARDAR">
                    <input class="button2" type="button" value="SORTIR" onClick="javascript:history.go(-1);">
                </p>
            </div>

            <?php
            }
            ?>

            <p class="cos2" style="clear: both; text-align: center;">
                Para guardar los datos clica á GUARDAR
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
