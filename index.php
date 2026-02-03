<?php

// Basis-Pfad
include_once "anmeldung/login.php";
check_login();


// die Rolle entscheidet, zur welcher Seite es geht Kellner oder Koch
if($_SESSION["rolle"]=="kellner"){
    // Weiterleitung nach Aktion 
    header("Location: pages/speisekarte.php");
    // Skript beenden 
    exit;
}

if($_SESSION["rolle"]=="kueche"){
    // Weiterleitung nach Aktion 
    header("Location: pages/kueche.php");
    // Skript beenden 
    exit;
}

die("Rolle unbekannt.");
?>
