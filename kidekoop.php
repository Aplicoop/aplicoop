<!--
Apartado economia Kidekoop. Este script esta diseniado para la gestion de economia de Kidekoop
kidekoop.org
En concreto, muestra los consumos y cuotas de los socios de cada mes.
La gestion de los monederos que se puede hacer mediante el boton "Actualizar Monederos" no se recomienda utilizar
fuera del contexto del "cierre del mes". En futuras versiones se intentara capar esa opcion para evitar "falsos cierres de mes"
Autor: Leonidas Ioannidis
Contacto: info@kidekooo.org
-->

<?php
session_start();

if ($_SESSION['image_is_logged_in'] == 'true') {

  $user = $_SESSION['user'];
  $pyear = $_POST['year'];
  $pmes = $_POST['mes'];
  if ($pmes == 12) {
    $nextmonth = 1;
    $pnextyear = $pyear +1;
  }
  else{
    $nextmonth = $pmes+1;
    $pnextyear = $pyear;
  }
    // $nextmonth = $pmes+1;
  $fecha1 = $pnextyear . "-" . $nextmonth . "-01";
  include 'config/configuracio.php';
  $sel = "SELECT tipus FROM usuaris WHERE nom='$user'";
  $query = mysqli_query($conn,$sel) or die ('query failed: ' . mysqli_error($conn));
  list($priv) = mysqli_fetch_row($query);

///sólo entramos si somos "super"////

  if ($priv == 'super') {
    ?>
    <html lang="es">
    <head>
      <?php include 'head.php'; ?>
      <title>Econom&iacute;a - Kidekoop</title>
    </head>

    <body>
      <?php include 'menu.php'; ?>
      <div class="page">
       <div class="container">
        <h1>Econom&iacute;a Kidekoop</h1>
        <form action="kidekoop.php" method="post" name="prod" id="prod">
         <div class="row">
          <div class="col-md-3">
           <div class="form-group">
            <label for="year">Año</label>
            <select name="year" id="year" size="1" maxlength="30">
              <option value="">-- Seleccionar --</option>
              <?php
              $select2 = "SELECT DISTINCT YEAR(data) FROM comanda";
              $query2 = mysqli_query($conn,$select2);
              if (!$query2) {
                die('Invalid query2: ' . mysqli_error($conn));
              }
              while (list($years) = mysqli_fetch_row($query2)) {
                if ($pyear == $years) {
                  echo '<option value="' . $years . '" selected>' . $years . '</option>';
                } else {
                  echo '<option value="' . $years . '">' . $years . '</option>';
                }
              }
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-3">
         <div class="form-group">
          <label for="mes">Mes</label>
          <select name="mes" id="mes" size="1" maxlength="30">
            <option value="">-- Seleccionar --</option>
            <?php
            $select2 = "SELECT DISTINCT MONTH(data) mes FROM comanda ORDER BY mes ASC ";
            $query2 = mysqli_query($conn,$select2);
            if (!$query2) {
              die('Invalid query2: ' . mysqli_error($conn));
            }
            while (list($meses) = mysqli_fetch_row($query2)) {
              if ($pmes == $meses) {
                echo '<option value="' . $meses . '" selected>' . $meses . '</option>';
              } else {
                echo '<option value="' . $meses . '">' . $meses . '</option>';
              }
            }
            ?>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group"><br>
          <input type="submit" name="submit" value="ENVIAR">
        </div>
      </div>
    </div>
  </form>

  <?php
            //When form is send
  if (isset($_POST['year'])) {
    echo "<div><a class='button button--animated button--save u-mt-2 u-mb-1' href='actualizar_monederos.php?year=".$pyear."&month=".$pmes."' title='Ordainketak'>Ordainketak Eguneratu <i class='fa fa-plus-circle' aria-hidden='true'></i></a>
    <a class='button button--animated button--save u-mt-2 u-mb-1' href='actualizar_kuotas.php?year=".$pyear."&month=".$pmes."' title='Kuotak'>Kuotak Kobratu <i class='fa fa-plus-circle' aria-hidden='true'></i></a></div>";
    print ('<p class="alert alert--info"> Consumo mensual de Soci@s con domiciliacion</p>');
    print('<div class="table-responsive">
      <table class="table table-condensed table-striped" >
      <tr >
      <td width="5%" class="u-text-semibold">No</td>
      <td width="10%" class="u-text-semibold">Soci@</td>
      <td width="40%" class="u-text-semibold">Nombre</td>
      <td width="15%" class="u-text-semibold u-text-right">Consumo</td>
      <td width="15%" class="u-text-semibold u-text-right">Cuota</td>
      <td width="20%" class="u-text-semibold u-text-right">TOTAL</td>
      </tr>') ;

    $sel = "(SELECT familia,usuaris.components, ROUND(SUM(valor),2), IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota), ROUND(IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) + ABS(SUM(valor)),2) as tot
    FROM `moneder`
    JOIN usuaris ON moneder.familia = usuaris.nom
    WHERE year(data) = " . $pyear . " and month(data) = " . $pmes . " and (concepte LIKE 'Factura%' OR concepte LIKE 'Anulacio%') AND usuaris.domiciliacion=1
    GROUP BY familia)
    UNION
    (SELECT usuaris.nom, usuaris.components, '0', IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota), IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) AS tot
    FROM usuaris
    WHERE nom NOT IN (
    SELECT DISTINCT us.nom
    FROM usuaris AS us
    JOIN comanda ON us.nom=comanda.usuari
    WHERE year(comanda.data2) = " . $pyear . " AND MONTH(comanda.data2) = " . $pmes . "
  )  AND usuaris.tipus2 = 'actiu' AND usuaris.domiciliacion = 1 AND usuaris.fechaalta <='" . $fecha1 . "')";
  $result = mysqli_query($conn,$sel);
  if (!$result) {
    die('Invalid query: ' . mysqli_error($conn));
  }
  $k = 0;
  while (list($socio, $nomsocio, $consumo, $cuota, $total) = mysqli_fetch_row($result)) {
   ?>
   <tr>
    <td><?php echo $k + 1; ?></td>
    <td><?php echo $socio; ?></td>
    <td><?php echo $nomsocio; ?></td>
    <td class="u-text-right"><?php echo sprintf("%01.2f", $consumo); ?></td>
    <td class="u-text-right"><?php echo $cuota; ?></td>
    <td class="u-text-right"><?php echo sprintf("%01.2f", $total); ?></td>
  </tr>

  <?php
  $k++;
}
print ('</table></div>');

$tot = "SELECT ABS(SUM(consumo)) as consumo, SUM(kuota) as kuota,SUM(total) as total
FROM ((SELECT familia,usuaris.components,
ROUND(SUM(valor),2) as consumo, IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) as kuota,
ROUND(IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) + ABS(SUM(valor)),2) as total
FROM `moneder`
JOIN usuaris ON moneder.familia = usuaris.nom
WHERE year(data) = " . $pyear . " and month(data) = " . $pmes . " and (concepte LIKE 'Factura%' OR concepte LIKE 'Anulacio%') AND usuaris.domiciliacion=1
GROUP BY familia)
UNION
(SELECT usuaris.nom, usuaris.components, '0', IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota), IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) AS total
FROM usuaris
WHERE nom NOT IN (
SELECT DISTINCT us.nom
FROM usuaris AS us
JOIN comanda ON us.nom=comanda.usuari
WHERE year(comanda.data2) = " . $pyear . " AND MONTH(comanda.data2) = " . $pmes . "
) AND usuaris.tipus2 = 'actiu' AND usuaris.domiciliacion = 1 AND usuaris.fechaalta <='" . $fecha1 . "'))as sub";
$result = mysqli_query($conn,$tot);
if (!$result) {
  die('Invalid query: ' . mysqli_error($conn));
}
list($consumo_of_1, $kuota_of_1, $totalof1) = mysqli_fetch_row($result);
?>
<tr>
  <td>TOTALES : </td>
  <td> <?php echo sprintf("Consumo: %01.2f, Cuotas: %01.2f, Total: %01.2f",$consumo_of_1, $kuota_of_1, $totalof1); ?>€</td>
</tr>

<?php
print ('<p class="alert alert--info"> Consumo mensual de Soci@s con monedero</p>');
print('<div class="table-responsive">
  <table class="table table-condensed table-striped" >
  <tr>
  <td width="5%" class="u-text-semibold">No</td>
  <td width="15%" class="u-text-semibold">Soci@</td>
  <td width="45%" class="u-text-semibold">Nombre</td>
  <td width="10%" class="u-text-semibold u-text-right">Consumo</td>
  <td width="10%" class="u-text-semibold u-text-right">Cuota</td>
  <td width="15%" class="u-text-semibold u-text-right">TOTAL</td>
  </tr>') ;

$sel="(SELECT comanda.usuari, usuaris.components, SUM(comanda_linia.cistella * comanda_linia.preu), IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota), SUM(comanda_linia.cistella * comanda_linia.preu) + IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) as total
FROM comanda
JOIN comanda_linia ON comanda.numero=comanda_linia.numero
JOIN usuaris on comanda.usuari=usuaris.nom
WHERE YEAR(comanda.data) = ".$pyear."  AND MONTH(comanda.data) = ".$pmes." AND usuaris.domiciliacion = 0
GROUP BY comanda.usuari)
UNION
(SELECT usuaris.nom, usuaris.components, '0', IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota),IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota)
FROM usuaris
WHERE nom NOT IN (
SELECT DISTINCT us.nom
FROM usuaris AS us
JOIN comanda ON us.nom=comanda.usuari
WHERE year(comanda.data2) = ".$pyear." AND MONTH(comanda.data2) = ".$pmes."
)  AND usuaris.tipus2 = 'actiu' AND usuaris.domiciliacion = 0 AND usuaris.kuota != 0 AND usuaris.fechaalta <'".$fecha1."')";

$result = mysqli_query($conn,$sel);
if (!$result) {
  die('Invalid query: ' . mysqli_error($conn));
}
$k = 0;
while (list($socio, $nomsocio, $consumo, $cuota, $total) = mysqli_fetch_row($result)) {
  ?>
  <tr>
    <td><?php echo $k +1; ?></td>
    <td><?php echo $socio; ?></td>
    <td><?php echo $nomsocio; ?></td>

    <td class="u-text-right"><?php echo sprintf("%01.2f", $consumo); ?></td>
    <td class="u-text-right"><?php echo $cuota; ?></td>
    <td class="u-text-right"><?php echo sprintf("%01.2f", $total); ?></td>
  </tr>
  <?php
  $k++;
}
print ('</table></div>');
$tot = "SELECT ABS(SUM(consumo)) as consumo, SUM(kuota) as kuota, SUM(total) FROM (
(SELECT comanda.usuari, usuaris.components, SUM(comanda_linia.cistella * comanda_linia.preu) as consumo, IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) as kuota, SUM(comanda_linia.cistella * comanda_linia.preu) + IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) as total
FROM comanda
JOIN comanda_linia ON comanda.numero=comanda_linia.numero
JOIN usuaris on comanda.usuari=usuaris.nom
WHERE YEAR(comanda.data) = ".$pyear."  AND MONTH(comanda.data) = ".$pmes." AND usuaris.domiciliacion = 0
GROUP BY comanda.usuari)
UNION
(SELECT usuaris.nom, usuaris.components, '0', IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota),IF(year(usuaris.fechaalta)=" . $pyear . " AND month((usuaris.fechaalta))=" . $pmes . ",'20.00',usuaris.kuota) as total
FROM usuaris
WHERE nom NOT IN (
SELECT DISTINCT us.nom
FROM usuaris AS us
JOIN comanda ON us.nom=comanda.usuari
WHERE year(comanda.data2) = ".$pyear." AND MONTH(comanda.data2) = ".$pmes."
)  AND usuaris.tipus2 = 'actiu' AND usuaris.domiciliacion = 0 AND usuaris.kuota != 0 AND usuaris.fechaalta <'".$fecha1."')) as sub";
$result = mysqli_query($conn,$tot);
if (!$result) {
  die('Invalid query: ' . mysqli_error($conn));
}
list($consumo2, $kuota2, $totalof3) = mysqli_fetch_row($result);
?>
<tr>
  <td>TOTAL : </td>
  <td> <?php echo sprintf("Consumo: %01.2f, Cuotas: %01.2f, Total: %01.2f",$consumo2, $kuota2, $totalof3); ?>€</td>
</tr>
<?php
}
echo "<p>TOTAL de facturas, cuotas y nuevas altas : ". sprintf("%01.2f", $totalof1 + $totalof3) . "€</p>";
?>
</div></div></div>
</body>
</html>
<?php
include 'config/disconect.php';
} else {
  header("Location: escriptori2.php");
}
} else {
  header("Location: index.php");
}
?>
