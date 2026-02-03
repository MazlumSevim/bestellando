<?php
// Datenbankverbindung (PDO)
// Wird von allen Seiten über include eingebunden.
?>
<?php

$host="localhost";
$benutzer="root";
$db="bestellando";
$pass="";

try{
    $verbindung = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $benutzer, $pass);
}catch(PDOException $e){
    die("Verbindung fehler:".$e->getMessage());
}
?>
