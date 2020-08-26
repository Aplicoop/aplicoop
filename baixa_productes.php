<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {
    $user = $_SESSION['user'];

    $pcat = $_POST['cat'];
    $psubcat = $_POST['subcat'];
    $pprov = $_POST['prov'];

    $gref = $_GET['id'];
    $gactiu = $_GET['id3'];
    $gpcat = $_GET['id4'];
    $gpsubcat = $_GET['id5'];
    $gpprov = $_GET['id6'];

    include 'config/configuracio.php';
    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - baja productos</title>

    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">
        <div class="container">

            <h1 >Activar y dar de baja productos</h1>


            <?php
            if ($gactiu != "") {
                $query3 = "UPDATE productes
			SET actiu='" . $gactiu . "'
			WHERE ref='" . $gref . "'";
                mysqli_query($conn,$query3) or die('Error, insert query3 failed');

                $pcat = $gpcat;
                $psubcat = $gpsubcat;
                $pprov = $gpprov;
            }
            ?>

        <form action="baixa_productes.php" method="post" name="prod" id="prod">

            <div class="row">
			    <div class="col-md-4">
				    <div class="form-group">
                        <label for="cat">Categorias</label>
                        <SELECT name="cat" id="cat" size="1" maxlength="30" onChange="this.form.submit()">
                            <option value="">--</option>

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
                <div class="col-md-4">
				<div class="form-group">
					<label for="subcat">Subcategorias</label>
					  <?php
				$dis_sc = "disabled";
				$opt_sc = '<OPTION value="">--</option>';
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="prov">Proveedores</label>
                        <SELECT name="prov" id="prov" size="1" maxlength="30" onChange="this.form.submit()">
                            <option value="">--</option>

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

		</form>

            <div class="box" >

                <?php
                if ($pcat != "" OR $pprov != "") {
                    if ($pcat == "") {
                        $where = "WHERE proveidora='" . $pprov . "'";
                        $title = "Filtrado por proveedor " . $pprov;
                    } else {
                        if ($psubcat == "" AND $pprov == "") {
                            $where = "WHERE categoria='" . $pcat . "'";
                            $title = "Filtrado por categoria " . $pcat;
                        } elseif ($psubcat != "" AND $pprov == "") {
                            $where = "WHERE categoria='" . $pcat . "' AND subcategoria='" . $psubcat . "'";
                            $title = "Filtrado por categoria " . $pcat . " y subcategoria " . $psubcat;
                        } elseif ($psubcat == "" AND $pprov != "") {
                            $where = "WHERE categoria='" . $pcat . "' AND proveidora='" . $pprov . "'";
                            $title = "Filtrado por categoria " . $pcat . " y proveedor " . $pprov;
                        } elseif ($psubcat != "" AND $pprov != "") {
                            $where = "WHERE categoria='" . $pcat . "' AND subcategoria='" . $psubcat . "' AND proveidora='" . $pprov . "'";
                            $title = "Filtrado por categoria " . $pcat . ", subcategoria " . $psubcat . " y proveedor " . $pprov;
                        }
                    }
                } else {
                    $where = "";
                    $title = "Ordenación alfabética de productos";
                }

                print ('<p class="alert alert--info">' . $title . '</p>');

                print('<div style="overflow: auto; height: 40vh;">');
                print('<table class="table table-striped table-bordered" >');

                print('<tr class="cos_majus">
			    <td width="80%" class="u-text-semibold">Producto</td>
                <td width="202%" class="u-text-semibold">Activo</td>

			    </tr>');

                $sel = "SELECT ref,nom,proveidora,actiu FROM productes " . $where . " ORDER BY nom";
                $result = mysqli_query($conn,$sel);
                if (!$result) {
                    die('Invalid query: ' . mysqli_error($conn));
                }

                $i = 0;
                $k = 0;
                while (list($ref, $nomprod, $nomprov, $actiu) = mysqli_fetch_row($result)) {
                    $checked1 = "";
                    $checked2 = "";
                    if ($actiu == "actiu") {
                        $checked1 = "checked";
                    } else {
                        $checked2 = "checked";
                    }

                    ?>
                    <tr>
                        <td class="cos"><?php echo $nomprod; ?></td>
                        <td class="u-text-center">
                            <label>
                                si&nbsp;<input type="radio" name="actiu<?php echo $k; ?>" value="actiu"
                                    id="actiu<?php echo $k; ?>" <?php echo $checked1; ?>
                                    onClick="javascript:window.location = 'baixa_productes.php?id=<?php echo $ref; ?>&id3=actiu&id4=<?php echo $pcat; ?>&id5=<?php echo $psubcat; ?>&id6=<?php echo $pprov; ?>';">
                            </label>
                            <label class="u-ml-1">
                                no&nbsp;<input type="radio" name="actiu<?php echo $k; ?>" value="baixa"
                                    id="actiu<?php echo $k; ?>" <?php echo $checked2; ?>
                                    onClick="javascript:window.location = 'baixa_productes.php?id=<?php echo $ref; ?>&id3=baixa&id4=<?php echo $pcat; ?>&id5=<?php echo $psubcat; ?>&id6=<?php echo $pprov; ?>';">
                                </label>
                        </td>
                    </tr>
                    <?php


                    $k++;

                }
                print ('</table></div>');

                ?>
                <p class="alert alert--info" >
                    Para activar o desactivar productos pulsa el botón correspondiente y se aplicará automáticamente.
                    Puedes buscar productos por categoría y / o por proveedor.
                    Por defecto aparecen todos los productos ordenados por orden alfabético.
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
