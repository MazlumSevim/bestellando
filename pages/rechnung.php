<?php
// Basis-Pfad
include_once "../includes/db.php";
include_once "../anmeldung/login.php";
check_kellner();

$tischid=0;
// Eingabe prüfen 
if(isset($_GET["tischid"])) $tischid=(int)$_GET["tischid"];

// Positionen holen
$sql="SELECT s.speisename, b.menge, b.gesamtpreis, t.name AS tischname
      FROM bestellung b
      JOIN speisekarte s ON b.speisekarteid=s.speiseid
      JOIN tisch t ON b.tischid=t.tischid
      WHERE b.tischid=? AND b.storniert=0
      ORDER BY b.bid ASC";
// Prepared Statement 
$cmd=$verbindung->prepare($sql);
// SQL ausführen 
$cmd->execute([$tischid]);
$rows=$cmd->fetchAll(PDO::FETCH_ASSOC);

$tischname = (count($rows)>0) ? $rows[0]["tischname"] : ("Tisch ".$tischid);

$sum=0;
foreach($rows as $r){ $sum += (float)$r["gesamtpreis"]; }
?>

<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Rechnung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="noprint" style="padding:14px 0;">
  <div class="container d-flex justify-content-center gap-2">
    <a class="btn btn-outline-dark" href="bestellung.php?tischid=<?php echo $tischid; ?>">Zurück</a>
    <button class="btn btn-dark" onclick="window.print()">Drucken</button>
  </div>
</div>

<div class="bon-wrap">
  <div class="bon zack-top">

    <div class="center">
      <img src="../assets/img/logo.png" alt="logo" style="height:70px;width:auto;">
      <div style="font-weight:900; font-size:16px;">BESTELLANDO</div>
      <div class="tiny">RECHNUNG</div>
    </div>

    <div class="line"></div>

    <div class="row2">
      <div>
        <div class="small"><b><?php echo htmlspecialchars($tischname); ?></b></div>
        <div class="tiny">Tisch-ID: <?php echo (int)$tischid; ?></div>
      </div>
      <div class="tiny" style="text-align:right;">
        <?php echo date("d.m.Y"); ?><br>
        <?php echo date("H:i"); ?>
      </div>
    </div>

    <div class="line"></div>

    <!-- Tabelle zur Übersicht -->
    <table>
      <?php foreach($rows as $r){ ?>
        <tr>
          <td><?php echo htmlspecialchars($r["speisename"]); ?></td>
          <td class="qty"><?php echo (int)$r["menge"]; ?>x</td>
          <td class="sum"><?php echo number_format((float)$r["gesamtpreis"],2,",","."); ?>€</td>
        </tr>
      <?php } ?>
    </table>

    <div class="line"></div>

    <div class="row2 total">
      <div>SUMME</div>
      <div><?php echo number_format($sum,2,",","."); ?>€</div>
    </div>

    <div class="line"></div>

    <div class="tiny center">— Danke & guten Appetit —</div>

  </div>
</div>

</body>
</html>
