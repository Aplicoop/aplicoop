<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {
    $user = $_SESSION['user'];

    $pcat = $_POST['cat'];
    $psubcat = $_POST['subcat'];
    $pprov = $_POST['prov'];
    $pdatas = $_POST['datas'];
    $pdatai = $_POST['datai'];

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - estadísticas de consumo</title>
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
<div class="page">
    <div class="container">

    <h1>Estadísticas de consumo</h1>

        <form action="estat_consum.php" method="post" name="prod" id="prod">

             <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cat">Categoría</label>
                        <div>
                            <SELECT name="cat" id="cat" size="1" maxlength="30" onChange="this.form.submit()">
                                <option value="">-- Seleccionar --</option>

                                <?php
                                $select2 = "SELECT tipus FROM categoria ORDER BY tipus";
                                $query2 = mysqli_query($conn,$select2);
                                if (!$query2) {
                                    die('Invalid query2: ' . mysqli_error($conn));
                                }
                                while (list($scat) = mysqli_fetch_row($query2)) {
                                    if ($pcat == $scat) {
                                        echo '<option value="' . $scat . '" selected>' . $scat . '</option>';
                                    } else {
                                        echo '<option value="' . $scat . '">' . $scat . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="prov">Proveedor</label>
                        <div>
                            <SELECT name="prov" id="prov" size="1" maxlength="30" onChange="this.form.submit()">
                                <option value="">-- Seleccionar --</option>

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
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="f_date_a">Superior a</label>
                        <input type="date" value="<?php echo $pdatas; ?>" name="datas" id="f_date_a"
                                onChange="this.form.submit()" placeholder="dd/mm/aaaa"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="f_date_b">Inferior a</label>
                        <input type="date" value="<?php echo $pdatai; ?>" name="datai" id="f_date_b"
                                onChange="this.form.submit()" placeholder="dd/mm/aaaa"/>
                    </div>
                </div>
             </div>
        </form>

    <div class="box">

        <?php
        if ($pcat != "" OR $pprov != "" OR $pdatas != "" OR $pdatai != "") {
            $datas2 = explode("/", $pdatas);
            $datai2 = explode("/", $pdatai);
            $datasup = $datas2[2] . "-" . $datas2[1] . "-" . $datas2[0];
            $datainf = $datai2[2] . "-" . $datai2[1] . "-" . $datai2[0];
            if ($pcat != "") {
                $wpcat = "AND pr.categoria='" . $pcat . "'";
                $tpcat = 'la categoría ' . $pcat;
            } else {
                $wpcat = "";
                $tpcat = "";
            }
            if ($psubcat != "") {
                $wpsubcat = "AND pr.subcategoria='" . $psubcat . "'";
                $tpsubcat = 'la subcategoria ' . $psubcat;
            } else {
                $wpsubcat = "";
                $tpsubcat = "";
            }
            if ($pprov != "") {
                $wpprov = "AND pr.proveidora='" . $pprov . "'";
                $tpprov = 'el proveedor ' . $pprov;
            } else {
                $wpprov = "";
                $tpprov = "";
            }
            if ($pdatas != "") {
                $wpdatas = "AND c.data>='" . $pdatas . "'";
                $tpdatas = 'fecha superior a ' . $pdatas;
            } else {
                $wpdatas = "";
                $tpdatas = "";
            }
            if ($pdatai != "") {
                $wpdatai = "AND c.data<='" . $pdatai . "'";
                $tpdatai = 'fecha inferior a ' . $pdatai;
            } else {
                $wpdatai = "";
                $tpdatai = "";
            }
            $where = $wpcat . " " . $wpsubcat . " " . $wpprov . " " . $wpdatas . " " . $wpdatai;
            $title = 'Búsqueda por ' . $tpcat . ' ' . $tpsubcat . ' ' . $tpprov . ' ' . $tpdatas . ' ' . $tpdatai;
        } else {
            $where = "";
            $title = "Ordenación alfabética de productos";
        }

        print ('<p class="alert alert--info">' . $title . '</p>');

        print('<div class="table-responsive">
                    <table class="table table-condensed table-striped" >
                        <tr >
                            <td width="30%" class="u-text-semibold">Producto</td>
                            <td width="10%" class="u-text-semibold">Proveedor</td>
                            <td width="10%" class="u-text-semibold">Categoría</td>
                            <td width="5%" class="u-text-semibold u-text-right">Consumo</td>
                            <td width="5%" class="u-text-semibold u-text-right">Gasto</td>
                            <td width="20%" class="u-text-semibold u-text-right">Inferior a</td>
                            <td width="20%" class="u-text-semibold u-text-right">Superior a</td>
                        </tr>') ;

        $sel = "SELECT cl.ref, pr.nom, pr.proveidora, pr.unitat, pr.categoria, pr.subcategoria,
				SUM(cl.cistella), SUM(cl.preu*cl.cistella), MIN(c.data), MAX(c.data)
			FROM comanda AS c, comanda_linia AS cl, productes AS pr
			WHERE c.numero=cl.numero AND pr.ref=cl.ref " . $where . "
			GROUP BY cl.ref";
        $result = mysqli_query($conn,$sel);
        if (!$result) {
            die('Invalid query: ' . mysqli_error($conn));
        }

        $k = 0;
        while (list($ref, $nomprod, $nomprov, $unitat, $cat, $subcat, $consum, $despesa, $datamin, $datamax) = mysqli_fetch_row($result)) {
            $datas3 = explode("-", $datamax);
            $datai3 = explode("-", $datamin);
            $datamaxvis = $datas3[2] . "-" . $datas3[1] . "-" . $datas3[0];
            $dataminvis = $datai3[2] . "-" . $datai3[1] . "-" . $datai3[0];
            $consum = number_format($consum, 3, ',', '.');
            $despesa = number_format($despesa, 2, ',', '.');
            $prod = htmlentities($nomprod, null, 'utf-8');
            $prodtext = str_replace("&nbsp;", " ", $prod);
            $prodtext = html_entity_decode($prodtext, null, 'utf-8');

            ?>
            <tr>
                <td><?php echo $prodtext; ?></td>
                <td><?php echo $nomprov; ?></td>
                <td><?php echo $cat; ?></td>
                <!--<td><?php echo $subcat; ?></td>-->
                <td class="u-text-right"><?php echo $consum . " " . $unitat; ?></td>
                <td class="u-text-right"><?php echo $despesa; ?> €</td>
                <td class="u-text-right"><?php echo $dataminvis; ?></td>
                <td class="u-text-right"><?php echo $datamaxvis; ?></td>
            </tr>

            <?php
            $k++;
        }
        print ('</table></div></div></div>');

        ?>
    </div>

    <?php
    include 'config/disconect.php';
} else {
    header("Location: index.php");
}
?>
