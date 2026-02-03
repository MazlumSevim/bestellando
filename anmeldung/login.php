<?php

include_once "../includes/db.php";
session_start();

// Basis-Pfad 
// Logout: zurück zum Login
if(isset($_GET["action"]) && $_GET["action"]=="logout"){
    session_destroy();
    // Weiterleitung nach Aktion 
    header("Location: ../anmeldung/login.php");
    // Skript beenden 
    exit;
}

// Zugriff: ohne Login geht’s zurück zur Anmeldung
function check_login(){
    if(!isset($_SESSION["uid"])){
        // Weiterleitung nach Aktion 
        header("Location: anmeldung/login.php");
        // Skript beenden 
        exit;
    }
}
function check_kellner(){
    check_login();
    if($_SESSION["rolle"]!="kellner"){
        // Weiterleitung nach Aktion 
        header("Location: ../index.php");
        // Skript beenden 
        exit;
    }
}
function check_kueche(){
    check_login();
    if($_SESSION["rolle"]!="kueche"){
        // Weiterleitung nach Aktion 
        header("Location: ../index.php");
        // Skript beenden 
        exit;
    }
}

// nur wenn login.php direkt geöffnet wird
$direkt = (realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]));
if(!$direkt){
    return;
}

// schon eingeloggt 
if(isset($_SESSION["uid"])){
    // Weiterleitung nach Aktion 
    header("Location: ../index.php");
    // Skript beenden 
    exit;
}

// login 
$fehler="";
// Eingabe prüfen 
if(isset($_POST["username"]) && isset($_POST["passwort"])){
    $username=trim($_POST["username"]);
    $passwort=$_POST["passwort"];

    // SQL-Statement vorbereiten 
    $sql="SELECT * FROM users WHERE username=? AND passwort=SHA2(?,256)";
    // Prepared Statement 
    $cmd=$verbindung->prepare($sql);
    // SQL ausführen 
    $cmd->execute([$username,$passwort]);
    $u=$cmd->fetch(PDO::FETCH_ASSOC);

    if($u){
        $_SESSION["uid"]=$u["uid"];
        $_SESSION["username"]=$u["username"];
        $_SESSION["rolle"]=$u["rolle"];
        

        // Weiterleitung nach Aktion 
        header("Location: ../index.php");
        // Skript beenden 
        exit;
    }else{
        $fehler="Falscher Login.";
    }
}
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Bestellando - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Kopfbereich/Navi -->
<div class="topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
  <img src="../assets/img/logo.png" class="logo-top" alt="Bestellando Logo">
  <div class="brand">Bestellando</div>
</div>

    <div class="smallmuted text-white-50">Login</div>
  </div>
</div>

<div class="container" style="max-width:520px;">
  <div class="card cardx">
    <div class="card-body p-4">
      <h1 class="pagetitle mb-1">Einloggen</h1>
      <div class="smallmuted mb-3">Kellner und Küche haben unterschiedliche Ansicht.</div>

      <?php if($fehler!=""){ ?>
        <div class="alert alert-danger"><?php echo $fehler; ?></div>
      <?php } ?>

      <!-- Formular -->
      <form method="post">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required>

        <label class="form-label mt-3">Passwort</label>
        <input class="form-control" type="password" name="passwort" required>

        <button class="btn btn-dark w-100 mt-3">Login</button>
      </form>

      <div class="mt-3 smallmuted">
        Test: <b>ali / 1234</b> (Kellner) — <b>koch / 1234</b> (Küche)
      </div>
    </div>
  </div>
</div>

</body>
</html>
