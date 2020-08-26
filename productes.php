<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $pcat = $_POST['cat'];
    $psubcat = $_POST['subcat'];
    $pprov = $_POST['prov'];

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - productos</title>
    </head>


    <body>
    <?php include 'menu.php'; ?>
    <div class="page">
        <div class="container">

            <div class="u-cf">
                <h1 class="pull-left">Productos </h1>

                <div class="pull-right u-mt-1">
                    <button class="button button--white button--animated" onClick="javascript:window.location = 'editprod.php'">Crear
                        producto  <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </button>
                </div>
            </div>



            <form action="productes.php" method="post" name="prod" id="prod">

                <div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cat">Categorias</label>
                                <div>
                                    <SELECT name="cat" id="cat" size="1" maxlength="30" onChange="this.form.submit()">
                                        <option value="">--elegir--</option>

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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="subcat">Subcategorias</label>
                                <div>
                                    <?php
                                    $dis_sc = "disabled";
                                    $opt_sc = '<OPTION value="">--elegir--</option>';
                                    if ($pcat != "") {
                                        $dis_sc = "";
                                        $opt_sc = '<OPTION value="">';
                                    }

                                    ?>

                                        <SELECT name="subcat" id="subcat" size="1" maxlength="30" <?php echo $dis_sc; ?>
                                                onChange="this.form.submit()">

                                            <?php
                                            echo $opt_sc;
                                            if ($pcat != "") {
                                                $select2 = "SELECT subcategoria FROM subcategoria
WHERE categoria='" . $pcat . "' ORDER BY subcategoria";
                                                $query2 = mysqli_query($conn,$select2);
                                                if (!$query2) {
                                                    die('Invalid query2: ' . mysqli_error($conn));
                                                }

                                                while (list($scat) = mysqli_fetch_row($query2)) {
                                                    if ($psubcat == $scat) {
                                                        echo '<option value="' . $scat . '" selected>' . $scat . '</option>';
                                                    } else {
                                                        echo '<option value="' . $scat . '">' . $scat . '</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                            </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="prov">Proveedores</label>
                                <div>
                                    <SELECT name="prov" id="prov" size="1" maxlength="30" onChange="this.form.submit()">
                                        <option value="">--elegir--</option>

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
                    </div>
                </div>

            </form>

            <div class="box">

                <div >


                    <?php
                    if ($pcat != "" OR $pprov != "") {
                        if ($pcat == "") {
                            $where = "proveidora='" . $pprov . "'";
                            $title = "Recerca per proveïdora " . $pprov;
                        } else {
                            if ($psubcat == "" AND $pprov == "") {
                                $where = "categoria='" . $pcat . "'";
                                $title = "Recerca per categoria " . $pcat;
                            } elseif ($psubcat != "" AND $pprov == "") {
                                $where = "categoria='" . $pcat . "' AND subcategoria='" . $psubcat . "'";
                                $title = "Recerca per categoria " . $pcat . " i subcategoria " . $psubcat;
                            } elseif ($psubcat == "" AND $pprov != "") {
                                $where = "categoria='" . $pcat . "' AND proveidora='" . $pprov . "'";
                                $title = "Recerca per categoria " . $pcat . " i proveïdora " . $pprov;
                            } elseif ($psubcat != "" AND $pprov != "") {
                                $where = "categoria='" . $pcat . "' AND subcategoria='" . $psubcat . "' AND proveidora='" . $pprov . "'";
                                $title = "Recerca per categoria " . $pcat . ", subcategoria " . $psubcat . " i proveïdora " . $pprov;
                            }
                        }

                        print ('<p class="alert alert--info">' . $title . '</p>');

                        print('<div class="row">');

                        $sel = "SELECT ref,nom,proveidora FROM productes
	WHERE " . $where . " ORDER BY nom";
                        $result = mysqli_query($conn,$sel);
                        if (!$result) {
                            die('Invalid query: ' . mysqli_error($conn));
                        }

                        $i = 0;
                        $letter = '';
                        $prevletter = '';
                        while (list($ref, $nomprod, $nomprov) = mysqli_fetch_row($result)) {
                            $prod = htmlentities($nomprod, null, 'utf-8');
                            $prodtext = str_replace("&nbsp;", " ", $prod);
                            $prodtext = html_entity_decode($prodtext, null, 'utf-8');
                            $letter = $nomprod[0];
                            if ($letter != $prevletter){
                                print('<div class="col-lg-12 u-mt-1"><h2 class="box-subtitle box-separator u-text-bold">'.$letter.'</h2></div>');

                                $prevletter = $letter;
                            }
                            print('<div class="col-lg-6"><a id="color" class="link"  href="editprod.php?id=' . $ref . '">' . $prodtext . '</a></div>');

                        }
                        print ('</div></div>');

                    } else {
                        print ('<p class="alert alert--info">Ordenación alfabética de productos</p>');

                        print('<div class="row">');

                        $sel = "SELECT ref,nom,proveidora FROM productes ORDER BY nom";
                        $result = mysqli_query($conn,$sel);
                        if (!$result) {
                            die('Invalid query: ' . mysqli_error($conn));
                        }

                        $i = 0;
                        $letter = '';
                        $prevletter = '';
                        while (list($ref, $nomprod, $nomprov) = mysqli_fetch_row($result)) {
                            $prod = htmlentities($nomprod, null, 'utf-8');
                            $prodtext = str_replace("&nbsp;", " ", $prod);
                            $prodtext = html_entity_decode($prodtext, null, 'utf-8');
                            $letter = $nomprod[0];
                            if ($letter != $prevletter){
                                print('<div class="col-lg-12 u-mt-1"><h2 class="box-subtitle box-separator u-text-bold">'.$letter.'</h2></div>');

                                $prevletter = $letter;
                            }
                            print('<div class="col-lg-6"><a id="color" class="link" href="editprod.php?id=' . $ref . '">' . $prodtext . '</a></div>');

                        }
                        print ('</div></div>');
                    }

                    ?>

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
