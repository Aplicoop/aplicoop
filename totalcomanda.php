<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

    $user = $_SESSION['user'];

    $data = $_GET['id'];
    $proces = $_GET['id2'];
    $grup = $_GET['id3'];
    $estoc = $_GET['id4'];

    $bd_data = date("Y-m-d", strtotime($data));

    if ($estoc == 1) {
        $link = "&id4=1";
        $title = "con stock";
        $where = "";
    } else {
        $link = "&id4=0";
        $title = "sin stock";
        $where = "AND cat.estoc = 'no' ";
    }

    ?>

    <html lang="es">
    <head>
        <?php include 'head.php'; ?>
        <title>aplicoop - totales pedido</title>
    </head>

    <body>
    <?php include 'menu.php'; ?>
    <div class="page">

        <div class="container">


            <div class="u-cf">
                <h1 class="pull-left">Pedido total <?php echo $title . " " . $proces . "-" . $grup . "-" . $data; ?></h1>

                <div class="pull-right u-mt-1 u-mb-1">
                    <button class="button button--white button--animated" type="button"
                            onClick="javascript:window.location = 'createcsv.php?id=<?php echo $bd_data . "&id2=" . $proces . "&id3=" . $grup . $link; ?>'">
                        CSV <i class="fa fa-table" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="box">
                <div >


                    <?php

                    include 'config/configuracio.php';
                    $color = array("#1abc9c", "#e74c3c", "#34495e", "#b20000", "#9b59b6", "#f1c40f", "#f39c12", "#c0392b", "#2980b9");
                    $cc = 0;
                    $select3 = "SELECT nom FROM proveidores";
                    $resultat3 = mysqli_query($conn,$select3);
                    if (!$resultat3) {
                        die("Query to show fields from table select3 failed");
                    }
                    $numrowsat3 = mysqli_num_rows($resultat3);
                    while (list($prove) = mysqli_fetch_row($resultat3)) {
                        $select2 = "SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND pr.proveidora='$prove'
	AND c.proces='$proces' AND pr.categoria=cat.tipus AND c.grup='$grup'
	AND c.data='$bd_data' " . $where . "
	ORDER BY pr.proveidora, pr.nom";
                        $resultat2 = mysqli_query($conn,$select2);
                        if (!$resultat2) {
                            die("Query to show fields from table select2 failed");
                        }
                        $numrowsat2 = mysqli_num_rows($resultat2);

                        if ($numrowsat2 != 0) {
                            print ('<a href="#' . $prove . '" id="color" class="link u-text-semibold" style="display:inline-block; margin-right: 1rem; border-bottom: 1px solid transparent; color: ' . $color[$cc] . ';">
				<span>' . $prove . '</span></a>');
                            $cc++;
                            if ($cc == 9) {
                                $cc = 0;
                            }
                        }
                        mysqli_free_result($resultat2);
                    }


                    echo '<hr class="box-separator"/><div class="u-mt-2" style="overflow: auto; height: 60vh;">';

                    $cc = 0;
                    $select3 = "SELECT nom FROM proveidores";
                    $resultat3 = mysqli_query($conn,$select3);
                    if (!$resultat3) {
                        die("Query to show fields from table select3 failed");
                    }
                    $numrowsat3 = mysqli_num_rows($resultat3);
                    while (list($prove) = mysqli_fetch_row($resultat3))
                    {
                    $select2 = "SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces' AND pr.categoria=cat.tipus
	AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove' " . $where . "
	ORDER BY pr.proveidora, pr.nom";
                    $resultat2 = mysqli_query($conn,$select2);
                    if (!$resultat2) {
                        die("Query to show fields from table select2 failed");
                    }
                    $numrowsat2 = mysqli_num_rows($resultat2);

                    if ($numrowsat2 != 0)
                    {
                    echo '<a name="' . $prove . '"></a>
                    <h2 style="color: ' . $color[$cc] . '; display:inline-block; text-align: left;">' . $prove . '</h2>';
                    $cc++;
                    if ($cc == 9) {
                        $cc = 0;
                    }
                    echo '<table class="table table-striped table-bordered">';

                    $query = "SELECT numero,usuari FROM comanda
		WHERE proces='$proces' AND grup='$grup' AND data='$bd_data'
		ORDER BY usuari";
                    $result = mysqli_query($conn,$query);
                    if (!$result) {
                        die("Query to show fields from table failed");
                    }
                    $numrows1 = mysqli_num_rows($result);

                    echo "<thead><tr class='u-text-semibold'><td>Producto</td>";
                    echo "<td  class='u-text-semibold u-text-center'>Totales</td>";

                    // printing table headers
                    $i = 0;
                    while (list($numero, $familia) = mysqli_fetch_row($result)) {
                        $fila[] = $numero;
                        echo "<td  class='u-text-semibold u-text-center'>" . ($i + 1) ."<br>" . $familia . " (" . $fila[$i] . ")</td>";
                        $i++;
                    }
                    echo "</tr></thead>";
                    $total = $i;
                    $taula = "SELECT cl.ref, pr.nom, pr.unitat, SUM(cl.quantitat) AS sum
		FROM comanda AS c, comanda_linia AS cl, productes AS pr
		WHERE c.numero=cl.numero AND pr.ref=cl.ref AND c.proces='$proces'
		AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove'
		GROUP BY cl.ref
		ORDER BY pr.nom";

                    $result = mysqli_query($conn,$taula);
                    if (!$result) {
                        die('Invalid query taula: ' . mysqli_error($conn));
                    }
                    while (list($ref, $nomprod, $uni, $sum) = mysqli_fetch_row($result))
                    {

                    ?>

                    <tr class='u-text-center'>
                        <td class="u-text-left"><?php echo $nomprod; ?></td>
                        <td><?php echo ($sum+0) ?> (<?php echo $uni; ?>)</td>

                        <?php

                        $taula2 = "SELECT c.numero, c.usuari, cl.ref, cl.quantitat
			FROM comanda AS c
			LEFT JOIN comanda_linia AS cl ON c.numero=cl.numero
			WHERE c.proces='$proces' AND c.grup='$grup' AND c.data='$bd_data'
			AND cl.ref='$ref'
			ORDER BY c.usuari";
                        $result2 = mysqli_query($conn,$taula2);
                        if (!$result2) {
                            die('Invalid query: ' . mysqli_error($conn));
                        }

                        $j = 0;

                        while (list($numcmda, $familia, $nomprod2, $quant) = mysqli_fetch_row($result2)) {
                            $numrows2 = mysqli_num_rows($result2);

                            for ($i = $j; $i <= $numrows1; $i++) {
                                $numfila = $fila[$i];
                                if ($numcmda == $numfila) {
                                    echo "<td>" . ($quant  + 0) . "</td>";
                                    $j++;
                                    break;
                                } else {
                                    echo "<td>&nbsp;</td>";
                                    $j++;
                                }
                            }


                        }
                        for ($k =  $j; $k < $total; $k++) {
                            echo "<td>&nbsp;</td>";
                        }
                        echo "</tr>";
                        }
                        }
                        echo "</table>";
                        }
                        ?>
                    </div>
                </div>

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
