<?php

// Basis-Pfad
include_once "../includes/db.php";
include_once "../anmeldung/login.php";
check_kellner();

/* bild anhand von speisename finden
   wenn nicht vorhanden -> img/default.jpg
*/
function bild_von_speise($name){
    $slug = strtolower($name);
    $slug = str_replace(["ä","ö","ü","ß"], ["ae","oe","ue","ss"], $slug);
    $slug = preg_replace("/[^a-z0-9]+/","_", $slug);
    $slug = trim($slug, "_");

    $pfad = "../assets/img/".$slug.".jpg";
    if(file_exists($pfad)){
        return $pfad;
    }
    return "../assets/img/default.jpg";
}

/* tisch */
$tischid = 0;
// Eingabe prüfen 
if(isset($_GET["tischid"])) $tischid = (int)$_GET["tischid"];

/* speise/getraenk */
$typ = "speise";
// Eingabe prüfen 
if(isset($_GET["typ"])) $typ = $_GET["typ"];
if($typ!="speise" && $typ!="getraenk") $typ="speise";

$typ_text = "Speisen";
if($typ=="getraenk") $typ_text="Getränke";

/* tische laden */
$sql = "SELECT * FROM tisch";
$cmd = $verbindung->prepare($sql);
// SQL ausführen 
$cmd->execute();
$tische = $cmd->fetchAll(PDO::FETCH_ASSOC);

if($tischid==0 && count($tische)>0){
    $tischid = (int)$tische[0]["tischid"];
}

/* speisekarte laden */
$sql = "SELECT * FROM speisekarte ORDER BY speisename";
$cmd = $verbindung->prepare($sql);
// SQL ausführen 
$cmd->execute();
$menu = $cmd->fetchAll(PDO::FETCH_ASSOC);

/* filtern nach typ */
$liste = [];
foreach($menu as $sp){
    $g = strtolower(trim((string)$sp["getraenk"]));
    $ist_getraenk = ($g=="ja" || $g=="getraenk" || $g=="getränk" || $sp["getraenk"]==1 || $sp["getraenk"]=="1");

    if($typ=="getraenk" && $ist_getraenk) $liste[] = $sp;
    if($typ=="speise" && !$ist_getraenk) $liste[] = $sp;
}

/* hinzufügen */
if(isset($_POST["speiseid"]) && isset($_POST["menge"])){
    $speiseid = (int)$_POST["speiseid"];
    $menge = (int)$_POST["menge"];
    if($menge < 1) $menge = 1;

    $user_id = (int)$_SESSION["uid"];

    /* speise holen */
    $sql = "SELECT * FROM speisekarte WHERE speiseid=?";
    $cmd = $verbindung->prepare($sql);
    // SQL ausführen 
    $cmd->execute([$speiseid]);
    $sp = $cmd->fetch(PDO::FETCH_ASSOC);

    if($sp){
        $preis = (float)$sp["preis"];

        /* schon vorhanden? -> menge hoch */
        $sql = "SELECT * FROM bestellung
                WHERE tischid=? AND speisekarteid=? AND storniert=0";
        // Prepared Statement 
        $cmd = $verbindung->prepare($sql);
        // SQL ausführen 
        $cmd->execute([$tischid, $speiseid]);
        $alt = $cmd->fetch(PDO::FETCH_ASSOC);

        if($alt){
            $neu_menge = (int)$alt["menge"] + $menge;
            $neu_gesamt = $preis * $neu_menge;

            // SQL-Statement vorbereiten 
            $sql = "UPDATE bestellung SET menge=?, gesamtpreis=? WHERE bid=?";
            // Prepared Statement 
            $cmd = $verbindung->prepare($sql);
            // SQL ausführen 
            $cmd->execute([$neu_menge, $neu_gesamt, $alt["bid"]]);
        }else{
            $gesamt = $preis * $menge;

            // SQL-Statement vorbereiten 
            $sql = "INSERT INTO bestellung (tischid,user_id,speisekarteid,storniert,menge,gesamtpreis)
                    VALUES (?,?,?,?,?,?)";
            // Prepared Statement 
            $cmd = $verbindung->prepare($sql);
            // SQL ausführen 
            $cmd->execute([$tischid, $user_id, $speiseid, 0, $menge, $gesamt]);
        }
    }

    // Weiterleitung nach Aktion 
    header("Location: speisekarte.php?typ=".$typ."&tischid=".$tischid);
    // Skript beenden 
    exit;
}
?>

<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Speisekarte</title>
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

    <div class="navbtn">
      <a href="bestellung.php">Bestellung</a>
      <a href="../anmeldung/login.php?action=logout">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card cardx p-3 mb-3">
    <div class="row g-2 align-items-end">
      <div class="col-md-6">
        <h3 class="mb-1">Speisekarte</h3>
        <div class="smallmuted">Wähle Tisch, dann Speisen oder Getränke.</div>
      </div>
      <div class="col-md-6">
        <!-- Formular -->
        <form method="get" class="d-flex gap-2">
          <input type="hidden" name="typ" value="<?php echo $typ; ?>">
          <select name="tischid" class="form-select">
            <?php foreach($tische as $t){ ?>
              <option value="<?php echo $t["tischid"]; ?>" <?php if($t["tischid"]==$tischid){echo "selected";} ?>>
                <?php echo $t["name"]; ?>
              </option>
            <?php } ?>
          </select>
          <button class="btn btn-dark">Tisch Auswahl Bestätigen</button>
        </form>
      </div>
    </div>

    <div class="mt-3">
      <a class="btn btn-sm <?php echo ($typ=="speise")?"btn-dark":"btn-outline-dark"; ?>"
         href="speisekarte.php?typ=speise&tischid=<?php echo $tischid; ?>">Speisen</a>

      <a class="btn btn-sm <?php echo ($typ=="getraenk")?"btn-dark":"btn-outline-dark"; ?>"
         href="speisekarte.php?typ=getraenk&tischid=<?php echo $tischid; ?>">Getränke</a>
    </div>
  </div>

  <div class="row g-3">
    <?php foreach($liste as $sp){
        $bild = bild_von_speise($sp["speisename"]);
    ?>
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="food-card">
          <img class="food-img" src="<?php echo $bild; ?>" alt="bild">
          <div class="p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="badge2"><?php echo $typ_text; ?></div>
                <h5 class="mt-2 mb-1"><?php echo $sp["speisename"]; ?></h5>
                <div class="smallmuted">Tisch: <?php echo $tischid; ?></div>
              </div>
              <div class="price"><?php echo number_format((float)$sp["preis"],2,",","."); ?> €</div>
            </div>

            <!-- Formular -->
            <form method="post" class="mt-3 d-flex gap-2">
              <input type="hidden" name="speiseid" value="<?php echo $sp["speiseid"]; ?>">
              <input type="number" name="menge" value="1" min="1" class="form-control" style="max-width:110px;">
              <button class="btn btn-success w-100">Hinzufügen</button>
            </form>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

 

</div>
</body>
</html>
