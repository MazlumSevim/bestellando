<?php

// Basis-Pfad 
include_once "../includes/db.php";
include_once "../anmeldung/login.php";
check_kellner();

$tischid=0;
// Eingabe prüfen 
if(isset($_GET["tischid"])) $tischid=(int)$_GET["tischid"];

// tische 
$sql="SELECT * FROM tisch";
$cmd=$verbindung->prepare($sql);
// SQL ausführen 
$cmd->execute();
$tische=$cmd->fetchAll(PDO::FETCH_ASSOC);
if($tischid==0 && count($tische)>0) $tischid=(int)$tische[0]["tischid"];

/* update */
if(isset($_POST["action"]) && $_POST["action"]=="update"){
    $bid=(int)$_POST["bid"];
    $menge=(int)$_POST["menge"];
    if($menge<1) $menge=1;

    // SQL-Statement vorbereiten 
    $sql="SELECT * FROM bestellung b
          JOIN speisekarte s ON b.speisekarteid=s.speiseid
          WHERE b.bid=?";
    // Prepared Statement 
    $cmd=$verbindung->prepare($sql);
    // SQL ausführen 
    $cmd->execute([$bid]);
    $b=$cmd->fetch(PDO::FETCH_ASSOC);

    if($b){
        $preis=(float)$b["preis"];
        $gesamt=$preis*$menge;

        // SQL-Statement vorbereiten 
        $sql="UPDATE bestellung SET menge=?, gesamtpreis=? WHERE bid=?";
        // Prepared Statement 
        $cmd=$verbindung->prepare($sql);
        // SQL ausführen 
        $cmd->execute([$menge,$gesamt,$bid]);
    }

    // Nach dem Speichern einmal neu laden (damit kein doppeltes POST passiert)
    header("Location: bestellung.php?tischid=".$tischid);
    // Skript beenden 
    exit;
}

/* delete */
if(isset($_POST["action"]) && $_POST["action"]=="delete"){
    $bid=(int)$_POST["bid"];
    // SQL-Statement vorbereiten 
    $sql="DELETE FROM bestellung WHERE bid=?";
    // Prepared Statement 
    $cmd=$verbindung->prepare($sql);
    // SQL ausführen 
    $cmd->execute([$bid]);

    // Weiterleitung nach Aktion 
    header("Location: bestellung.php?tischid=".$tischid);
    // Skript beenden 
    exit;
}

// bestellungen 
$sql="SELECT b.*, s.speisename, s.preis
      FROM bestellung b
      JOIN speisekarte s ON b.speisekarteid=s.speiseid
      WHERE b.tischid=? AND b.storniert=0
      ORDER BY b.bid DESC";
// Prepared Statement 
$cmd=$verbindung->prepare($sql);
// SQL ausführen 
$cmd->execute([$tischid]);
$bestellungen=$cmd->fetchAll(PDO::FETCH_ASSOC);

// summe 
$summe=0;
foreach($bestellungen as $b){
    $summe += (float)$b["gesamtpreis"];
}
?>

<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Bestellung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Kopfbereich/Navi -->
<div class="topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <img src="../assets/img/logo.png" class="logo-top" alt="Logo">
      <div class="brand">Bestellando</div>
    </div>
    <div class="navbtn">
      <a href="speisekarte.php">Speisekarte</a>
      <a href="../anmeldung/login.php?action=logout">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card cardx p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h1 class="pagetitle mb-0">Bestellung</h1>
        <div class="smallmuted">Menge ändern, löschen, Rechnung drucken.</div>
      </div>

      <!-- Formular -->
      <form method="get" class="d-flex gap-2" style="min-width:320px;">
        <select name="tischid" class="form-select">
          <?php foreach($tische as $t){ ?>
            <option value="<?php echo (int)$t["tischid"]; ?>" <?php if((int)$t["tischid"]==$tischid){echo "selected";} ?>>
              <?php echo htmlspecialchars($t["name"]); ?>
            </option>
          <?php } ?>
        </select>
        <button class="btn btn-dark">Anzeigen</button>
        <a class="btn btn-success" href="rechnung.php?tischid=<?php echo $tischid; ?>">Rechnung drucken</a>
      </form>
    </div>
  </div>

  <div class="card cardx p-3">
    <!-- Tabelle zur Übersicht -->
    <table class="table table-bordered table-hover mb-3">
      <thead>
        <tr>
          <th>Artikel</th>
          <th style="width:150px;">Menge</th>
          <th style="width:130px;">Summe</th>
          <th style="width:120px;">Update</th>
          <th style="width:120px;">Löschen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($bestellungen as $b){ ?>
          <tr>
            <td>
              <?php echo htmlspecialchars($b["speisename"]); ?>
              <?php if(isset($b["fertig"]) && (int)$b["fertig"]==1){ ?>
                <span class="badge bg-success ms-2">fertig</span>
              <?php } else { ?>
                <span class="badge bg-warning text-dark ms-2">offen</span>
              <?php } ?>
            </td>

            <td>
              <!-- Formular -->
              <form method="post" class="d-flex gap-2">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="bid" value="<?php echo (int)$b["bid"]; ?>">
                <input type="number" name="menge" value="<?php echo (int)$b["menge"]; ?>" min="1" class="form-control">
            </td>

            <td><?php echo number_format((float)$b["gesamtpreis"],2,",","."); ?> €</td>

            <td>
                <button class="btn btn-warning w-100">Update</button>
              </form>
            </td>

            <td>
              <!-- Formular -->
              <form method="post" onsubmit="return confirm('Wirklich löschen?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="bid" value="<?php echo (int)$b["bid"]; ?>">
                <button class="btn btn-danger w-100">Löschen</button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="smallmuted">Tisch: <b><?php echo $tischid; ?></b></div>
      <div class="fs-5"><b>Rechnung: <?php echo number_format($summe,2,",","."); ?> €</b></div>
    </div>
  </div>

</div>
</body>
</html>
