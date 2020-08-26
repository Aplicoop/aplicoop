<?php
//////////////////////////////////////////////////////////
// Configuració de la connexió a la base de dades mysql //
//////////////////////////////////////////////////////////
$dbhost = 'localhost'; //servidor on hi ha la base de dades coment
$dbuser = 'root'; //nom de l'usuari de la base de dades
$dbpass = ''; //password de la base de dades per l'usuari
$dbname = 'aplicoop_v3'; //nom de la base de dades
//$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
mysqli_query($conn,"SET NAMES 'utf8'"); //Suposo que s'ha de posar aquÃ­
//mysqli_select_db($dbname);
mysqli_select_db($conn, $dbname);
//////////////////////////////////////////////////////////
// Aquí van els elements de configuració de cada coope ///
//////////////////////////////////////////////////////////
// Canviar les imatge actuals per l'enllaç a les vostres imatges
$logo_portada='imatges/logo.jpg'; //Imatge que apreix a la portada
$logo_menu='imatges/logo_menu.jpg'; // Imatge que apareix al menu (una vegada hem iniciat sessió)
$logo_factura='imatges/logo_comanda.jpg'; //Imatge que apareix a les comandes i factures
// email on van les incidencies de l'aplicatiu (p.e. una familia no pot entrar). fiqueu l'email de la
// persona o grup encarregada de la supervisió de l'aplicatiu
$email_incidencies='XXXXXXXXXX@email.com';
// email on van les incidències d'economia, on es respon al correus electrònics que avisen a les famílies de les factures
// (p.e. una factura te algun error). fiqueu l'email de la persona o grup encarregada d'incidències amb les factures
$email_economia='XXXXXXXXXX@email.com';
// Enllaç del Google calendar a l'escriptori
// Podeu introduir l'enllaç d'un calendari o d'altres widgets que volgueu
$gcal="";
?>
