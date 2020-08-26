<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {
    $user = $_SESSION['user'];
    $pfamilia = $_POST['familia'];
    $pdatas = $_POST['datas'];
    $pdatai = $_POST['datai'];
    $superpfam = strtoupper($pfamilia);

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <link rel="stylesheet" type="text/css" href="coope.css"/>
        <title>Estadística de consumo de productos ::: kidekoop</title>
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
       <body>
        <?php include 'menu.php'; ?>
        <div class="pagina" style="margin-top: 10px;">
            <div class="contenidor_1" style="border: 1px solid black;">
                <p class='path'>
                    ><a href='admint.php'>Administración</a>
                    >><a href='estat_iva_prov.php'>calculo del IVA</a>
                </p>

                <p class="h1" style="background: black; text-align: left; padding-left: 20px;">
                Estadística de consumo</p>

                <table width="95%" align="center">
                    <form action="estat_iva_prov.php" method="post" name="prod" id="prod">
                        <tr class="cos_majus" style="padding-top: 10px;">
                            <td width="20%" align="center" class="form">Proveedor</td>
                            <td width="20%" align="center" class="form">Superior a la fecha</td>
                            <td width="20%" align="center" class="form">Inferior a la fecha</td>
                        </tr>
                        <tr class="cos">
                            <td align="center">
                                <SELECT name="familia" id="familia" size="1" maxlength="30" onChange="document.prod.submit()">
                                    <option value="">Elige un proveedor</option>
                                    <?php
                                    $select3 = "SELECT nom FROM proveidores ORDER BY nom";
                                    $query3 = mysqli_query($conn,$select3);
                                    if (!$query3) {
                                        die('Invalid query3: ' . mysqli_error($conn));
                                    }
                                    while (list($sfam) = mysqli_fetch_row($query3)) {
                                        if ($pfamilia == $sfam) {
                                            echo '<option value="' . $sfam . '" selected>' . $sfam . '</option>';
                                        } else {
                                            echo '<option value="' . $sfam . '">' . $sfam . '</option>';
                                        }
                                    }
                                    ?>
                                </SELECT>
                            </td>

                            <td align="center">
                                <input type="text" value="<?php echo $pdatas; ?>" name="datas" id="f_date_a" size="8" maxlength="10"
                                readonly/>
                                <button type="text" name="budi" id="f_trigger_a">...</button>
                                <button type="submit" name="okds" id="okds">ok</button>
                                <script type="text/javascript">
                                    Calendar.setup({
                            inputField: "f_date_a",     // id of the input field
                            ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                            button: "f_trigger_a",  // trigger for the calendar (button ID)
                            singleClick: true
                        });
                    </script>
                </td>

                <td align="center">
                    <input type="text" value="<?php echo $pdatai; ?>" name="datai" id="f_date_b" size="8" maxlength="10"
                    readonly/>
                    <button type="text" name="budf" id="f_trigger_b">...</button>
                    <button type="submit" name="okdi" id="okdi">ok</button>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "f_date_b",     // id of the input field
                            ifFormat: "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
                            button: "f_trigger_b",  // trigger for the calendar (button ID)
                            singleClick: true
                        });
                    </script>
                </td>
            </form>
        </tr></table>
        <div class="contenidor_fac" style="border: 0px solid black; width: 900px;">

            <?php
            if ($pfamilia != "" OR $pdatas != "" OR $pdatai != "") {
                $datas2 = explode("/", $pdatas);
                $datai2 = explode("/", $pdatai);
                $datasup = $datas2[2] . "-" . $datas2[1] . "-" . $datas2[0];
                $datainf = $datai2[2] . "-" . $datai2[1] . "-" . $datai2[0];
                $where = "";
                $title = "";
                if ($pfamilia != "") {
                    $where .= " AND pr.proveidora='" . $pfamilia . "'";
                    $title .= "Búsqueda por proveedor " . $superpfam;
                }
                if ($pdatas != "") {
                    $where .= " AND c.data>='" . $datasup . "'";
                    $title .= " fecha superior a " . $pdatas;
                }
                if ($pdatai != "") {
                    $where .= "AND c.data<='" . $datainf . "'";
                    $title .= " fecha inferior a " . $pdatai;
                }
            }

            print ('<p class="h1"
              style="background: black; font-size:14px; text-align: left;
              height: 20px; padding-left: 20px;">' . $title . '</p>');

            print('<table width="80%" align="center" cellspading="5" cellspacing="5" >
              <tr class="cos_majus">
              <td width="20%" align="center">Base Imponible</td>
              <td width="10%" align="center">Iva</td>
              <td width="20%" align="center">Total</td>
              <td width="15%" align="center">Fecha inf</td>
              <td width="15%" align="center">Fecha sup</td>
              <td width="20%" align="center">Total iva</td>
              </tr>');

            $sel = "SELECT cl.iva, SUM(cl.preu*cl.cistella*(1-cl.descompte)) AS base,
            SUM(cl.preu*cl.cistella*(1-cl.descompte)*cl.iva) AS ivat,
            SUM(cl.preu*cl.cistella*(1-cl.descompte)*(1+cl.iva)) AS total,
            MIN(c.data), MAX(c.data)
            FROM comanda AS c, comanda_linia AS cl, productes as pr
            WHERE c.numero=cl.numero AND cl.ref=pr.ref " . $where . "
            GROUP BY cl.iva";
            $result = mysqli_query($conn,$sel);
            if (!$result) {
                die('Invalid query: ' . mysqli_error($conn));
            }

            $k = 0;
            $total = 0;
            while (list($iva, $base, $ivatotal, $total, $datamin, $datamax) = mysqli_fetch_row($result)) {
                $datas3 = explode("-", $datamax);
                $datai3 = explode("-", $datamin);
                $datamaxvis = $datas3[2] . "-" . $datas3[1] . "-" . $datas3[0];
                $dataminvis = $datai3[2] . "-" . $datai3[1] . "-" . $datai3[0];
                $vis_iva = $iva * 100;
                $t_total = $t_total + $ivatotal;
                $t_total = sprintf("%01.2f", $t_total);
                $base = sprintf("%01.2f", $base);
                $ivatotal = sprintf("%01.2f", $ivatotal);
                $total = sprintf("%01.2f", $total);

                ?>
                <tr class='cos'>
                    <td align="center"><?php echo $base; ?>€</td>
                    <td align="center"><?php echo $vis_iva; ?>%</td>
                    <td align="center"><?php echo $total; ?>€</td>
                    <td align="center"><?php echo $dataminvis; ?></td>
                    <td align="center"><?php echo $datamaxvis; ?></td>
                    <td align="center"><?php echo $ivatotal; ?>€</td>
                </tr>

                <?php
                $k++;
            }
            print('<tr class="cos2"><td></td><td></td><td></td><td></td>
              <td align="center">Total</td><td align="center">' . $t_total . '€</td></tr>');
            print ('</table></div></div>');

            ?>
        </div>

        <?php
        include 'config/disconect.php';
    } else {
        header("Location: index.php");
    }
    ?>
