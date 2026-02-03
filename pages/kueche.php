<?php

// Basis-Pfad
include_once "../includes/db.php";
include_once "../anmeldung/login.php";
check_kueche();

// Tisch fertig klicken 
if(isset($_POST["action"]) && $_POST["action"]=="fertig" && isset($_POST["tischid"])){
    $tischid = (int)$_POST["tischid"];

    // SQL-Statement vorbereiten 
    $sql="UPDATE bestellung
          SET fertig=1
          WHERE tischid=? AND storniert=0 AND fertig=0";
    // Prepared Statement 
    $cmd=$verbindung->prepare($sql);
    // SQL ausführen 
    $cmd->execute([$tischid]);

    // Seite neu laden, damit die fertigen Bons verschwinden 
    header("Location: kueche.php");
    // Skript beenden 
    exit;
}

// offene Bestellungen 
$sql="SELECT b.bid, b.tischid, b.menge, b.gesamtpreis,
             s.speisename,
             t.name AS tischname,
             u.username AS user_name
      FROM bestellung b
      JOIN speisekarte s ON b.speisekarteid = s.speiseid
      JOIN tisch t ON b.tischid = t.tischid
      LEFT JOIN users u ON b.user_id = u.uid
      WHERE b.storniert=0 AND b.fertig=0
      ORDER BY b.tischid ASC, b.bid ASC";
// Prepared Statement 
$cmd=$verbindung->prepare($sql);
// SQL ausführen 
$cmd->execute();
$rows=$cmd->fetchAll(PDO::FETCH_ASSOC);

// nach Tisch gruppieren 
$gruppen=[];
foreach($rows as $r){
    $tid=(int)$r["tischid"];
    if(!isset($gruppen[$tid])){
        $gruppen[$tid]=[
            "tischname"=>$r["tischname"],
            "user_name"=>$r["user_name"],
            "items"=>[]
        ];
    }
    $gruppen[$tid]["items"][]=$r;
}
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Bestellando - Küche</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <meta http-equiv="refresh" content="8">
</head>
<body>

<!-- Kopfbereich/Navi -->
<div class="topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <img src="../assets/img/logo.png" class="logo-top" alt="Logo">
      <div class="brand">Bestellando - Küche</div>
    </div>
    <div class="navbtn">
      <a href="../anmeldung/login.php?action=logout">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <?php if(count($gruppen)==0){ ?>
    <div class="card cardx p-4">
      <h3 class="mb-1">Keine offenen Bestellungen</h3>
      <div class="smallmuted">Auto-Refresh läuft.</div>
    </div>
  <?php } ?>

  <div class="row g-3">
    <?php foreach($gruppen as $tid=>$g){
        $sum=0;
        foreach($g["items"] as $it){ $sum += (float)$it["gesamtpreis"]; }
        $tischname = $g["tischname"] ? $g["tischname"] : ("Tisch ".$tid);
        $user_name = $g["user_name"] ? $g["user_name"] : "-";
    ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="bon-wrap">
          <div class="bon zack-top">

            <div class="center">
              <div style="font-weight:900; font-size:16px;">BESTELLANDO</div>
              <div class="tiny">KÜCHE</div>
            </div>

            <div class="line"></div>

            <div class="row2">
              <div>
                <div class="small"><b><?php echo htmlspecialchars($tischname); ?></b></div>
                <div class="tiny">Tisch-ID: <?php echo (int)$tid; ?></div>
                <div class="tiny">Kellner: <?php echo htmlspecialchars($user_name); ?></div>
              </div>
              <div class="tiny" style="text-align:right;">
                <?php echo date("d.m.Y"); ?><br>
                <?php echo date("H:i"); ?>
              </div>
            </div>

            <div class="line"></div>

            <!-- Tabelle zur Übersicht -->
            <table>
              <?php foreach($g["items"] as $it){ ?>
                <tr>
                  <td><?php echo htmlspecialchars($it["speisename"]); ?></td>
                  <td class="qty"><?php echo (int)$it["menge"]; ?>x</td>
                  <td class="sum"><?php echo number_format((float)$it["gesamtpreis"],2,",","."); ?>€</td>
                </tr>
              <?php } ?>
            </table>

            <div class="line"></div>

            <div class="row2 total">
              <div>SUMME</div>
              <div><?php echo number_format($sum,2,",","."); ?>€</div>
            </div>

            <div class="line"></div>

            <div class="tiny center">— Guten Appetit —</div>

            <div class="noprint" style="margin-top:10px;">
              <!-- Formular -->
              <form method="post">
                <input type="hidden" name="action" value="fertig">
                <input type="hidden" name="tischid" value="<?php echo (int)$tid; ?>">
                <button class="btn btn-success btn-bon">Fertig</button>
              </form>
            </div>

          </div>
        </div>
      </div>
    <?php } ?>
  </div>

</div>
</body>
</html>
