<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    date_default_timezone_set('Europe/Madrid');
    $session = date("Y-m-d H:i:s", $_SESSION['timeinitse']);

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>crear-editar nuevo producto ::: la coope</title>

        <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-win2k-1.css" title="win2k-1"/>

        <!-- main calendar program -->
        <script type="text/javascript" src="calendar/calendar.js"></script>

        <!-- language for the calendar -->
        <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

        <!-- the following script defines the Calendar.setup helper function, which makes
             adding a calendar a matter of 1 or 2 lines of code. -->
        <script type="text/javascript" src="calendar/calendar-setup.js"></script>

        <script language="javascript" type="text/javascript">

            function Validate() {

                var data = document.getElementById("f_date_a").value;
                var fam = document.getElementById("fam").value;
                var concepte = document.getElementById("concepte").value;
                var valor = document.getElementById("valor").value;
                var valor2 = document.getElementById("valor2").value;

                if (data == "") {
                    alert("T'has deixat la data en blanc");
                    document.getElementById("f_date_a").focus();
                    return false;
                }

                if (fam == "") {
                    alert("No has elegit cap família");
                    document.getElementById("fam").focus();
                    return false;
                }

                if (concepte == "") {
                    alert("No has elegit cap concepte");
                    document.getElementById("concepte").focus();
                    return false;
                }

                if (valor == "" && valor2 == "") {
                    alert("No has introduït cap valor, ni per restar ni per sumar");
                    document.getElementById("valor").focus();
                    return false;
                }

                if (valor != "" && valor2 != "") {
                    alert("Només pots introduïr un valor, o bé per restar o bé per sumar");
                    document.getElementById("valor").focus();
                    return false;
                }

                if (isNaN(valor)) {
                    alert('A sumar: només s/accepten numeros i el punt decimal');
                    document.getElementById("valor").focus();
                    return false;
                }

                if (valor < 0) {
                    alert('A sumar: el numero ha de ser superior que 0');
                    document.getElementById("valor").focus();
                    return false;
                }

                if (valor.indexOf('.') == -1) valor += ".";
                dectext = valor.substring(valor.indexOf('.') + 1, valor.length);

                if (dectext.length > 2) {
                    alert('A sumar: el numero de decimals no pot ser superior a 2');
                    document.getElementById("valor").focus();
                    return false;
                }

                if (isNaN(valor2)) {
                    alert('A restar: només s/accepten numeros i el punt decimal');
                    document.getElementById("valor2").focus();
                    return false;
                }

                if (valor2 < 0) {
                    alert('A restar: el numero ha de ser superior que 0');
                    document.getElementById("valor2").focus();
                    return false;
                }

                if (valor2.indexOf('.') == -1) valor2 += ".";
                dectext = valor2.substring(valor2.indexOf('.') + 1, valor2.length);

                if (dectext.length > 2) {
                    alert('A restar: el numero de decimals no pot ser superior a 2');
                    document.getElementById("valor2").focus();
                    return false;
                }

                return true;
            }


        </script>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid grey;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='moneder_linia.php'>introduir dades moneder</a>
            </p>
            <p class="h1" style="background: grey; text-align: left; padding-left: 20px;">
                Introduir dades moneder
            </p>

            <?php
            if ($_POST['data'] != "") {
                $pdata = $_POST['data'];
                $pdata2 = explode("/", $pdata);
                $pdata_bd = $pdata2[2] . "-" . $pdata2[1] . "-" . $pdata2[0];
                $pfam = $_POST['fam'];
                $pconcepte = $_POST['concepte'];
                $pvalor_mes = $_POST['valor'];
                $pvalor_menys = $_POST['valor2'];
                if ($pvalor_mes != "") {
                    $pvalor = $pvalor_mes;
                    $verb = 'abonat';
                    $signe = "+";
                }
                if ($pvalor_menys != "") {
                    $pvalor = -$pvalor_menys;
                    $verb = 'carregat';
                    $signe = "-";
                }
                $absvalor = ABS($pvalor);
                $pnotes = $_POST['notes'];

                if ($pfam == 'tots') {
                    $select = "SELECT nom FROM usuaris WHERE tipus2='actiu' ORDER BY nom";
                    $query = mysqli_query($conn,$select);
                    if (!$query) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }
                    while (list($tfam) = mysqli_fetch_row($query)) {
                        $query2 = "INSERT INTO moneder
 			VALUES ('" . $session . "','" . $user . "','" . $pdata_bd . "','" . $tfam . "','" . $pconcepte . "','" . $pvalor . "','" . $pnotes . "')";
                        mysqli_query($conn,$query2) or die('Error, insert query2 failed');
                        $query4 = "UPDATE usuaris SET moneder=moneder" . $signe . $absvalor . " WHERE nom = '" . $tfam . "'";
                        mysqli_query($conn,$query4) or die('Error, insert query4 failed');
                        print ("<p class='comment'>S'han " . $verb . " " . ABS($pvalor) . " euros a " . $tfam . " pel concepte " . $pconcepte . " </p>");
                    }
                    die ("<p class='comment'><a href='moneder_linia.php'>nova entrada</a> - <a href='admint.php'>sortir</a> </p>");
                }

                $query2 = "INSERT INTO moneder
 			VALUES ('" . $session . "','" . $user . "','" . $pdata_bd . "','" . $pfam . "','" . $pconcepte . "','" . $pvalor . "','" . $pnotes . "')";
                mysqli_query($conn,$query2) or die('Error, insert query2 failed');

                $query4 = "UPDATE usuaris SET moneder=moneder" . $signe . $absvalor . " WHERE nom = '" . $pfam . "'";
                mysqli_query($conn,$query4) or die('Error, insert query4 failed');

                die ("<p class='comment'>S'han " . $verb . " " . ABS($pvalor) . " euros a " . $pfam . " pel concepte " . $pconcepte . " </p>
			<p class='comment'><a href='moneder_linia.php'>nova entrada</a> - <a href='admint.php'>sortir</a> </p>");
            }

            ?>

            <div class="contenidor_fac" style="border: 1px solid grey; width: 900px; margin-bottom: 20px;">

                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <form action="moneder_linia.php" method="post" name="nouprod" id="nouprod"
                          onSubmit="return Validate();">
                        <tr class="cos_majus">
                            <td width="20%">Data</td>
                            <td width="20%">Familia</td>
                            <td width="40%">Concepte</td>
                            <td width="10%">Suma</td>
                            <td width="10%">Resta</td>
                        </tr>
                        <tr class="cos">
                            <td><input type="text" value="" name="data" id="f_date_a" size="8" maxlength="10" readonly/>
                                <button type="text" name="budi" id="f_trigger_a">...</button>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "f_date_a",     // id of the input field
                                        ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                                        button: "f_trigger_a",  // trigger for the calendar (button ID)
                                        singleClick: true
                                    });
                                </script>
                            </td>
                            <td>
                                <SELECT name="fam" id="fam" size="1" maxlength="30">
                                    <option value="">elegeix una familia</option>
                                    <option value="tots">totes les families actives</option>
                                    <?php
                                    $select3 = "SELECT nom FROM usuaris WHERE tipus2='actiu' ORDER BY nom";
                                    $query3 = mysqli_query($conn,$select3);
                                    if (!$query3) {
                                        die('Invalid query3: ' . mysqli_error($conn));
                                    }
                                    while (list($sfam) = mysqli_fetch_row($query3)) {
                                        echo '<option value="' . $sfam . '">' . $sfam . '</option>';
                                    }
                                    ?>
                            </td>
                            <td><input align="right" name="concepte" id="concepte" type="TEXT" maxlength="50" size="35"
                                       value=""></td>
                            <td><input align="right" name="valor" id="valor" type="TEXT" maxlength="7" size="5"
                                       value=""></td>
                            <td><input align="right" name="valor2" id="valor2" type="TEXT" maxlength="7" size="5"
                                       value=""></td>
                        </tr>
                </table>

                <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                    <tr>
                        <td class="cos_majus">Comentaris:</td>
                        <td><input align="right" name="notes" id="notes" type="TEXT" maxlength="255" size="60"
                                   value="<?php echo $notes; ?>">
                    </tr>
                </table>
                <p class="linia_button2" style="background: grey; text-align: center; vertical-align: middle;">
                    <input class="button2" type="submit" value="GUARDAR">
                    <input class="button2" type="button" value="SORTIR"
                           onClick="javascript: window.location='admint.php';">
                </p>
            </div>
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
