<?php

session_start();

if ($_SESSION['image_is_logged_in']) {

    $user = $_SESSION['user'];

    $gproces = $_GET['id'];
    $ggrup = $_GET['id2'];
    $gbd_data = $_GET['id3'];

    setlocale(LC_ALL, "CA");
    date_default_timezone_set("Europe/Madrid");

    include 'config/configuracio.php';


    // function ae_send_mail
    function ae_send_mail($from, $to, $subject, $text, $headers = "", $reply)
    {
        if (strtolower(substr(PHP_OS, 0, 3)) === 'win')
            $mail_sep = "\r\n";
        else
            $mail_sep = "\n";

        function _rsc($s)
        {
            $s = str_replace("\n", '', $s);
            $s = str_replace("\r", '', $s);
            return $s;
        }

        $h = '';
        if (is_array($headers)) {
            foreach ($headers as $k => $v)
                $h = _rsc($k) . ': ' . _rsc($v) . $mail_sep;
            if ($h != '') {
                $h = substr($h, 0, strlen($h) - strlen($mail_sep));
                $h = $mail_sep . $h;
            }
        }

        $from = _rsc($from);
        $to = _rsc($to);
        $subject = _rsc($subject);
        $head = 'From: ' . $from . $h . "\r\n" . 'Reply-To: ' . $reply;
        mail($to, $subject, $text, $head);
    }


    if (($_SERVER['REQUEST_METHOD'] == 'POST') &&
        isset($_POST['to']) && isset($_POST['subject']) && isset($_POST['text'])
    ) {
        $desti = $_POST['to'];
        $cont = count($desti);
        $noms = array();
        $noms2 = array();
        $noms3 = array();
        $noms4 = array();

        if ($desti[0] == "tothom") {
            $where = "WHERE tipus2='actiu'";
        } else {

            for ($i = 0; $i < $cont; $i++) {
                if ($desti[$i] == "adm") {
                    $taula = "SELECT nom
					FROM usuaris
					WHERE tipus='admin' AND tipus2='actiu'
					ORDER BY nom";
                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query: ' . mysqli_error($conn));
                    }
                    while (list($r) = mysqli_fetch_row($result)) {
                        $noms[] = $r;
                    }
                } elseif ($desti[$i] == "eco") {
                    $taula2 = "SELECT nom
					FROM usuaris
					WHERE tipus='eco' AND tipus2='actiu'
					ORDER BY nom";
                    $result2 = mysqli_query($conn,$taula2);
                    if (!$result2) {
                        die('Invalid query2: ' . mysqli_error($conn));
                    }
                    while (list($r2) = mysqli_fetch_row($result2)) {
                        $noms2[] = $r2;
                    }
                } elseif ($desti[$i] == "prov") {
                    $taula3 = "SELECT nom
					FROM usuaris
					WHERE tipus='prov' AND tipus2='actiu'
					ORDER BY nom";
                    $result3 = mysqli_query($conn,$taula3);
                    if (!$result3) {
                        die('Invalid query3: ' . mysqli_error($conn));
                    }
                    while (list($r3) = mysqli_fetch_row($result3)) {
                        $noms3[] = $r3;
                    }
                } else {
                    $noms4[] = $desti[$i];
                }
            }
            $noms5 = array();
            $noms5 = array_merge($noms, $noms2, $noms3, $noms4);
            $noms5 = array_unique($noms5);
            sort($noms5);
            if (empty($noms5)) {
                die('No s ha especificat cap destinatari');
            }
            $where = "WHERE";
            $conta = count($noms5);
            $k = 1;
            foreach ($noms5 as $a) {

                if ($k < $conta) {
                    $where .= " nom='" . $a . "' OR";
                    $noms_desti .= $a . "-";
                } else {
                    $where .= " nom='" . $a . "' ";
                    $noms_desti .= $a;
                }
                $k++;
            }
        }

        $taula5 = "SELECT nom, email1, email2
		  FROM usuaris " . $where . " ORDER BY nom";
        $result5 = mysqli_query($conn,$taula5);
        if (!$result5) {
            die('Invalid query5: ' . mysqli_error($conn));
        }
        while (list($familia, $email1, $email2) = mysqli_fetch_row($result5)) {
            $destinatari .= $email1 . ',' . $email2 . ',';
        }

        // nice RFC 2822 From field
        $subject = $_POST['subject'];
        $text = $_POST['text'];
        $taula9 = "SELECT email1, email2
		  FROM usuaris WHERE nom='$user' ";
        $result9 = mysqli_query($conn,$taula9);
        if (!$result9) {
            die('Invalid query9: ' . mysqli_error($conn));
        }
        list($email1, $email2) = mysqli_fetch_row($result9);
        $reply = $email1 . "," . $email2;
        ae_send_mail($user, $destinatari, $subject, $text, array('X-Mailer' => 'PHP script at ' . $_SERVER['HTTP_HOST']), $reply);
        $mail_send = true;

        $data_c = date("Y-m-d-H-i-s");
        $taula6 = "INSERT INTO incidencia VALUES('$user', '$noms_desti', '$subject', '$text', '$data_c', '0')";
        $result6 = mysqli_query($conn,$taula6);
        if (!$result6) {
            die('Invalid query6: ' . mysqli_error($conn));
        }
    }
    ?>


    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>correu intern::: la coope</title>
    </head>

    <script>
        function validate_form() {

            var to = document.getElementById("to").value;
            var sub = document.getElementById("subject").value;
            var tex = document.getElementById("text").value;

            if (to == "") {
                alert("T'has deixat el destinatari en blanc");
                document.getElementById("to").focus();
                return false;
            }

            if (sub == "") {
                alert("T'has deixat el titol en blanc");
                document.getElementById("subject").focus();
                return false;
            }

            if (tex == "") {
                alert("T'has deixat el text en blanc");
                document.getElementById("text").focus();
                return false;
            }
            return true;
        }
    </script>


    <?php
    if ($gproces != "" AND $ggrup != "" AND $gbd_data != "") {

        $link = ">><a href='grups_comandes.php'>grups de comandes i cistelles</a>>>><a href='cistella_incidencia.php?id=" . $gproces . "&id2=" . $ggrup . "&id3=" . $gbd_data . "'>comunicar incidència proces " . $gproces . " - " . $ggrup . " - " . $gbd_data . "</a>";
        $titular = "Comunicar incidències proces " . $gproces . " - " . $ggrup . " - " . $gbd_data;
        $color = "green";
        $location = 'cistella_incidencia.php?id=' . $gproces . '&id2=' . $ggrup . '&id3=' . $gbd_data;

    } else {
        $link = ">><a href='cistella_incidencia.php'>comunicar incidència</a>";
        $titular = "Comunicar incidències";
        $color = "#a74fd7";
        $location = 'cistella_incidencia.php';
    }
    ?>

    <body>
    <?php include 'menu.php'; ?>
    <div class="pagina" style="margin-top: 10px;">
        <div class="contenidor_1" style="border: 1px solid <?php echo $color; ?>;">
            <p class='path'>
                ><a href='admint.php'>administració</a>

                <?php echo $link; ?>

            </p>
            <p class="h1" style="background: <?php echo $color; ?>; text-align: left; padding-left: 20px;">
                <?php echo $titular; ?>
            </p>

            <?php
            if (isset($mail_send)) {
                ?>

                <p class="error" style='font-size: 14px; padding: 50px 0px;'>El correu s'ha enviat correctament.</p>

                <?php
            } else {
                ?>

                <form action="<?php echo $location; ?>" method="post" name="frmIncidencies" id="frmIncidencies"
                      onSubmit="return validate_form();">

                    <div class="contenidor_fac" style="border: 1px solid <?php echo $color; ?>; width: 600px;">
                        <table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">
                            <tr class="cos_majus">
                                <td width="20%">Des de</td>
                                <td style="color: red; border: 1px solid <?php echo $color; ?>;"><?php echo $user; ?></td>
                            </tr>
                            <tr>
                                <td class="cos_majus">Destinatari
                                </td>
                                <td class="cos" style="border: 1px solid <?php echo $color; ?>;">
                                    <select style="display: block; float: left;" name="to[]" multiple size=5 id="to">
                                        <option value="tothom">tothom</option>
                                        <?php
                                        if ($gproces != "" AND $ggrup != "" AND $gbd_data != "") {
                                            echo '<option value="tothom_c">tothom cistella</option>';
                                        }
                                        ?>
                                        <option value="adm">administració</option>
                                        <option value="eco">economia</option>
                                        <option value="prov">proveidors</option>

                                        <?php
                                        if ($gproces != "" AND $ggrup != "" AND $gbd_data != "") {
                                            $taula7 = "SELECT usuari FROM comanda
				WHERE proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'
				ORDER BY usuari";
                                        } else {
                                            $taula7 = "SELECT nom FROM usuaris WHERE tipus2='actiu' ORDER BY nom";
                                        }

                                        $result7 = mysqli_query($conn,$taula7);
                                        if (!$result7) {
                                            die('Invalid query7: ' . mysqli_error($conn));
                                        }
                                        while (list($familia) = mysqli_fetch_row($result7)) {
                                            echo '<option value="' . $familia . '">' . $familia . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span style="display: block; float: left; width: 150px; margin-left: 10px;">Multiselecció amb Crtl+click o Majus+click</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="cos_majus">Tema</td>
                                <td style="border: 1px solid <?php echo $color; ?>;"><input
                                        style="border:1px solid red; text-align:left;" size="52" name="subject"
                                        type="text" id="subject"></td>
                            </tr>
                            <tr>
                                <td class="cos_majus">incidència</td>
                                <td style="border: 1px solid <?php echo $color; ?>;"><textarea name="text" cols="60"
                                                                                               rows="8"
                                                                                               id="text"></textarea>
                                </td>
                            </tr>
                        </table>
                        <p class="linia_button2"
                           style="background: <?php echo $color; ?>; padding:4px 0px; height: 20px; text-align: center; vertical-align: middle;">
                            <input class="button2" name="btnIncidencies" type="submit" id="btnIncidencies"
                                   value="Envia">
                        </p>
                    </div>
                </form>

            <?php } ?>

            <p class="linia_button2"
               style="background: <?php echo $color; ?>; text-align: center; vertical-align: middle;">
                <input class="button2" name="sortir" type="button" value="FINALITZAR"
                       onClick="javascript:window.location = 'admint.php';">
                <input class="button2" style="width: 120px;" name="sortir" type="button" value="MÉS INCIDÈNCIES"
                       onClick="javascript:window.location = '<?php echo $location; ?>';">
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
