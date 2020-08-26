<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];
    $superuser = strtoupper($_SESSION['user']);

    $nom = $_GET['id'];
    $supernom = strtoupper($nom);
    $grup = $_GET['id2'];
    $supergrup = strtoupper($grup);
    $tipus = $_GET['id3'];
    $datai = $_POST['datai'];
    $datai2 = explode("/", $datai);
    $dataini = $datai2[2] . "/" . $datai2[1] . "/" . $datai2[0];
    $dataf = $_POST['dataf'];
    $dataf2 = explode("/", $dataf);
    $datafin = $dataf2[2] . "/" . $dataf2[1] . "/" . $dataf2[0];
    $periode = $_POST['periode'];
    $diare = $_POST['diare'];
    $diat = $_POST['diat'];
    $horat = $_POST['horat'];
    $actiu = $_POST['actiu'];

    function tradueixData2($d)
    {
        $angles = array("Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday",
            "Mon",
            "Tue",
            "Wed",
            "Thu",
            "Fri",
            "Sat",
            "Sun",
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec");

        $catala = array("/Dilluns/",
            "/Dimarts/",
            "/Dimecres/",
            "/Dijous/",
            "/Divendres/",
            "/Dissabte/",
            "/Diumenge/",
            "/Dll/",
            "/Dmr/",
            "/Dmc/",
            "/Djs/",
            "/Dvd/",
            "/Dss/",
            "/Dmg/",
            "/Gener/",
            "/Febrer/",
            "/Març/",
            "/Abril/",
            "/Maig/",
            "/Juny/",
            "/Juliol/",
            "/Agost/",
            "/Setembre/",
            "/Octubre/",
            "/Novembre/",
            "/Desembre/",
            "/Gen/",
            "/Feb/",
            "/Mar/",
            "/Abr/",
            "/Mai/",
            "/Jun/",
            "/Jul/",
            "/Ago/",
            "/Set/",
            "/Oct/",
            "/Nov/",
            "/Des/");

        $ret2 = preg_replace($catala, $angles, $d);
        return $ret2;
    }

    include('config/configuracio.php');

    ?>

    <html lang="es">
    <head>
        <meta http-equiv="content-type" content="" text
        /htm; charset=UTF-8" >
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>editar dades processos ::: la coope</title>

        <!-- calendar stylesheet -->
        <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-win2k-1.css" title="win2k-1"/>

        <!-- main calendar program -->
        <script type="text/javascript" src="calendar/calendar.js"></script>

        <!-- language for the calendar -->
        <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

        <!-- the following script defines the Calendar.setup helper function, which makes
             adding a calendar a matter of 1 or 2 lines of code. -->
        <script type="text/javascript" src="calendar/calendar-setup.js"></script>

    </head>

    <script languaje="javascript">
        function validar_formulari() {
            var nom = document.getElementById("nom");
            var grup = document.getElementById("grup");
            var tipus = document.getElementById("tipus");

            if (nom.value == "") {
                alert("T'has deixat el nom en blanc");
                document.getElementById("nom").focus();
                return false;
            }
            if (grup.value == "") {
                alert("T'has deixat el grup en blanc");
                document.getElementById("grup").focus();
                return false;
            }
            if (tipus.value == "") {
                alert("Has d'escollir un tipus de procés");
                document.getElementById("tipus").focus();
                return false;
            }
            if (tipus.value == "període concret") {
                var datai = document.getElementById("f_date_a");
                array_datai = datai.value.split("/");
                var dataini = array_datai[2] + array_datai[1] + array_datai[0];
                var dataf = document.getElementById("f_date_b");
                array_dataf = dataf.value.split("/");
                var datafi = array_dataf[2] + array_dataf[1] + array_dataf[0];
                if (datai.value == "") {
                    alert("T'has deixat la data inicial en blanc");
                    document.getElementById("f_date_a").focus();
                    return false;
                }
                if (dataf.value == "") {
                    alert("T'has deixat la data final en blanc");
                    document.getElementById("f_date_b").focus();
                    return false;
                }
                if (parseInt(datafi) <= parseInt(dataini)) {
                    alert("La data final ha de ser igual o superior a la data inicial");
                    document.getElementById("f_date_a").focus();
                    return false;
                }
            }
            if (tipus.value == "continu") {
                var per = document.getElementById("periode");
                var diare = document.getElementById("diare");
                var diat = document.getElementById("diat");
                var horat = document.getElementById("horat");
                if (per.value == "") {
                    alert("T'has deixat el període en blanc");
                    document.getElementById("periode").focus();
                    return false;
                }
                if (diare.value == "") {
                    alert("T'has deixat el dia de recollida en blanc");
                    document.getElementById("diare").focus();
                    return false;
                }
                if (diat.value == "") {
                    alert("T'has deixat el dia de tall en blanc");
                    document.getElementById("diat").focus();
                    return false;
                }
                if (horat.value == "") {
                    alert("T'has deixat hora de tall en blanc");
                    document.getElementById("horat").focus();
                    return false;
                }

                var horat1 = horat.value.substring(0, 2);
                var horat2 = horat.value.substring(2);
                if (isNaN(horat1) || isNaN(horat2) || horat1 == "" || horat2 == "" || horat.value.length < 4) {
                    alert("L'hora de tall ha de ser una xifra de quatre nombres: els dos primers representen l'hora i els dos segons els minuts");
                    document.getElementById("horat").focus();
                    return false;
                }

                if (horat1 < 0 || horat1 > 23) {
                    alert("A horat: els dos primers nombres han d'estar entre l'interval	00 i 23 ja que respresenten les hores del dia");
                    document.getElementById("horat").focus();
                    return false;
                }

                if (horat2 < 0 || horat2 > 60) {
                    alert("A horat: els dos darrers nombres han d'estar entre l'interval	00 i 59 ja que respresenten els minuts");
                    document.getElementById("horat").focus();
                    return false;
                }
            }
            return true;
        }

    </script>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid #66FF66;">
            <p class='path'>
                ><a href='admint.php'>administració</a>
                >><a href='editprocessos.php'>crear, editar i eliminar processos</a>
                >>><a
                    href='editprocessos2.php?id=<?php echo $nom; ?>&id2=<?php echo $grup; ?>&id3=<?php echo $tipus; ?>'>editar
                    procés <?php echo $nom; ?> - grup <?php echo $grup; ?></a>
            </p>

            <p class="h1" style="background: #66FF66; text-align: left; padding-left: 20px;">
                Editar procés <?php echo $supernom; ?> - grup <?php echo $supergrup; ?>
            </p>


            <?php

            if ($actiu != "")
            {

                $query2 = "UPDATE processos
	SET data_inici='" . $dataini . "', data_fi='" . $datafin . "', periode='" . $periode . "',
		dia_recollida='" . $diare . "', dia_tall='" . $diat . "', hora_tall='" . $horat . "',
		actiu='" . $actiu . "'
	WHERE nom='" . $nom . "' AND grup='" . $grup . "' ";

                mysqli_query($conn,$query2) or die('Error, insert query2 failed');

                echo "<p class='comment'>Els canvis en el procés " . $supernom . " - grup " . $supergrup . " s'han guardat correctament
	</p>";

            }
            else
            {
            $select = "SELECT data_inici,data_fi,periode,dia_recollida,dia_tall,hora_tall,actiu
		FROM processos
		WHERE nom='$nom' AND grup='$grup'";
            $query = mysqli_query($conn,$select);
            if (!$query) {
                die('Invalid query: ' . mysqli_error($conn));
            }
            list($predatai, $predataf, $preperiode, $prediare, $prediat, $prehorat, $preactiu) = mysqli_fetch_row($query);

            $selec3 = "";
            $selec4 = "";
            if ($preactiu == "actiu") {
                $selec3 = "selected";
            } else {
                $selec4 = "selected";
            }

            $alt_text = "0";
            if ($tipus == 'període concret') {
                $time_avui = time();
                $time_datai = strtotime($predatai);
                $time_dataf = strtotime($predataf);
                if ($time_avui < $time_dataf AND $time_avui > $time_datai) {
                    $alt_text = '1';
                }
            } else {
                $diat_a = tradueixData2(ucfirst($prediat));
                $diat_0 = strtotime("next " . $diat_a);
                $diare_a = tradueixData2(ucfirst($prediare));
                $time_diare = strtotime("next " . $diare_a, $diat_0);
                $bd_diare = date("Y-m-d", $time_diare);
                $query = "SELECT numero
			FROM comanda
			WHERE proces='$nom' AND grup='$grup' AND data='$bd_diare'";
                $result = mysqli_query($conn,$query);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }
                $rnum = mysqli_num_rows($result);
                if ($rnum != "") {
                    $alt_text = '1';
                }
            }

            if ($alt_text == "1") {
                $mestext = "<p class='comment' style='padding: 0px 100px;'>El procés " . $supernom . " - grup " . $supergrup . " posseeix comandes vigents.
		Alguns canvis no son possibles. Podràs fer els canvis quan el periode arribi a la seva fi.</p>";
                $disabled = "disabled";
            }
            ?>

            <?php echo $mestext; ?>

            <div class="contenidor_fac" style="border: 1px solid #66FF66; width: 500px; margin-bottom: 20px;">

                <form
                    action="editprocessos2.php?id=<?php echo $nom; ?>&id2=<?php echo $grup; ?>&id3=<?php echo $tipus; ?>"
                    method="post" name="frmeditdadesp" id="frmeditdadesp" onSubmit="return validar_formulari();">
                    <table width="60%" align="center" valign="middle" cellpadding="5" cellspacing="5">

                        <tr>
                            <td class="cos_majus">Nom</td>
                            <td>
                                <input type="text" name="nom" id="nom" value="<?php echo $nom; ?>" size="10"
                                       maxlength="30" readonly>
                            </td>
                        </tr>
                        <td class="cos_majus">Grup</td>
                        <td>
                            <input type="text" name="grup" id="grup" value="<?php echo $grup; ?>" size="10"
                                   maxlength="30" readonly>
                        </td>
                        </tr>
                        <tr>
                            <td class="cos_majus">Tipus</td>
                            <td>
                                <input type="text" name="tipus" id="tipus" value="<?php echo $tipus; ?>" size="12"
                                       maxlength="20" readonly>
                            </td>
                        </tr>

                        <?php
                        if ($tipus == "període concret") {
                            $predatai2 = explode("-", $predatai);
                            $predataini = $predatai2[2] . "/" . $predatai2[1] . "/" . $predatai2[0];
                            $predataf2 = explode("-", $predataf);
                            $predatafin = $predataf2[2] . "/" . $predataf2[1] . "/" . $predataf2[0];
                            if ($predataini == "00/00/0000") {
                                $predataini = "";
                            }
                            if ($predatafin == "00/00/0000") {
                                $predatafin = "";
                            }
                            ?>

                            <tr>
                                <td class="cos_majus">Data inicial<sup>(*)</sup></td>
                                <td>

                                    <input type="text" name="datai" id="f_date_a" size="8" maxlength="10" readonly
                                           value="<?php echo $predataini; ?>"/>
                                    <button type="reset" name="budi" id="f_trigger_a">...</button>
                                    </p>
                                    <script type="text/javascript">
                                        Calendar.setup({
                                            inputField: "f_date_a",     // id of the input field
                                            ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                                            button: "f_trigger_a",  // trigger for the calendar (button ID)
                                            singleClick: true
                                        });
                                    </script>

                                </td>
                            </tr>

                            <tr>
                                <td class="cos_majus">Data final <sup>(*)</sup></td>
                                <td>
                                    <input type="text" name="dataf" id="f_date_b" size="8" maxlength="10" readonly
                                           value="<?php echo $predatafin; ?>"/>
                                    <button type="reset" name="budf" id="f_trigger_b" <?php echo $disabled ?> >...
                                    </button>
                                    </p>
                                    <script type="text/javascript">
                                        Calendar.setup({
                                            inputField: "f_date_b",     // id of the input field
                                            ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                                            button: "f_trigger_b",  // trigger for the calendar (button ID)
                                            singleClick: true
                                        });
                                    </script>
                                </td>
                            </tr>

                            <?php
                            $notafinal = "<br/>(*) Si fas servir un procés vàries vegades recorda definir un nou període modificant
tant la data inicial com la data final per a no crear interferències";
                        } else {
                            $sel1 = "";
                            $sel2 = "";
                            $sel3 = "";
                            $sel4 = "";
                            $sel5 = "";
                            $sel6 = "";
                            $sel7 = "";
                            if ($prediat == "dilluns") {
                                $sel1 = "selected";
                            } elseif ($prediat == "dimarts") {
                                $sel2 = "selected";
                            } elseif ($prediat == "dimecres") {
                                $sel3 = "selected";
                            } elseif ($prediat == "dijous") {
                                $sel4 = "selected";
                            } elseif ($prediat == "divendres") {
                                $sel5 = "selected";
                            } elseif ($prediat == "dissabte") {
                                $sel6 = "selected";
                            } elseif ($prediat == "diumenge") {
                                $sel7 = "selected";
                            }
                            $sele1 = "";
                            $sele2 = "";
                            $sele3 = "";
                            $sele4 = "";
                            $sele5 = "";
                            $sele6 = "";
                            $sele7 = "";
                            if ($prediare == "dilluns") {
                                $sele1 = "selected";
                            } elseif ($prediare == "dimarts") {
                                $sele2 = "selected";
                            } elseif ($prediare == "dimecres") {
                                $sele3 = "selected";
                            } elseif ($prediare == "dijous") {
                                $sele4 = "selected";
                            } elseif ($prediare == "divendres") {
                                $sele5 = "selected";
                            } elseif ($prediare == "dissabte") {
                                $sele6 = "selected";
                            } elseif ($prediare == "diumenge") {
                                $sele7 = "selected";
                            }
                            ?>
                            <tr>
                                <td class="cos_majus">Període</td>
                                <td>
                                    <input type="text" name="periode" id="periode" value="<?php echo $preperiode; ?>"
                                           size="10" maxlength="30" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td class="cos_majus">Dia recollida</td>
                                <td>
                                    <SELECT name="diare" id="diare" size="1" maxlength="20" <?php echo $disabled ?>>
                                        <OPTION value="dilluns" <?php echo $sele1; ?>>dilluns</OPTION>
                                        <OPTION value="dimarts" <?php echo $sele2; ?>>dimarts</OPTION>
                                        <OPTION value="dimecres" <?php echo $sele3; ?>>dimecres</OPTION>
                                        <OPTION value="dijous" <?php echo $sele4; ?>>dijous</OPTION>
                                        <OPTION value="divendres" <?php echo $sele5; ?>>divendres</OPTION>
                                        <OPTION value="dissabte" <?php echo $sele6; ?>>dissabte</OPTION>
                                        <OPTION value="diumenge" <?php echo $sele7; ?>>diumenge</OPTION>
                                    </SELECT>
                            </tr>
                            <tr>
                                <td class="cos_majus">Dia tall</td>
                                <td>
                                    <SELECT name="diat" id="diat" size="1" maxlength="20" <?php echo $disabled ?>>
                                        <OPTION value="dilluns" <?php echo $sel1; ?>>dilluns</OPTION>
                                        <OPTION value="dimarts" <?php echo $sel2; ?>>dimarts</OPTION>
                                        <OPTION value="dimecres" <?php echo $sel3; ?>>dimecres</OPTION>
                                        <OPTION value="dijous" <?php echo $sel4; ?>>dijous</OPTION>
                                        <OPTION value="divendres" <?php echo $sel5; ?>>divendres</OPTION>
                                        <OPTION value="dissabte" <?php echo $sel6; ?>>dissabte</OPTION>
                                        <OPTION value="diumenge" <?php echo $sel7; ?>>diumenge</OPTION>
                                    </SELECT></td>
                            </tr>
                            <tr>
                                <td class="cos_majus">Hora tall (HHMM)</td>
                                <td>
                                    <input type="text" name="horat" id="horat" size="4" maxlength="4"
                                           value="<?php echo $prehorat; ?>" <?php echo $disabled ?>>
                                </td>
                            </tr>

                            <?php
                            $notafinal = "";
                        }
                        ?>

                        <tr>
                            <td class="cos_majus">Activitat</td>
                            <td>
                                <SELECT name="actiu" id="actiu" size="1" maxlength="12">
                                    <OPTION value="actiu" <?php echo $selec3; ?> >Actiu</OPTION>
                                    <OPTION value="aturat" <?php echo $selec4; ?> >Aturat</OPTION>
                                </SELECT></td>
                        </tr>

                    </table>
                    <p class="linia_button2" style="background: #66FF66; text-align: center; vertical-align: middle;">
                        <input class="button2" type="submit" value="GUARDAR">
                        <input class="button2" type="button" value="SORTIR"
                               onClick="javascript:window.location = 'editprocessos.php';">
                    </p>
                </form>

                <p class="cos2" style="clear: both; text-align: center;">
                    Per canviar les dades clica el botó GUARDAR.<?php echo $notafinal; ?></p>
                <?php
                }
                ?>

    </body>
    </html>

    <?php

    include 'config/disconect.php';

} else {
    header("Location: index.php");
}
?>
