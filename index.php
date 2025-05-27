<?php
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = htmlspecialchars($_SESSION["user"]);
$skorTerakhir = $_SESSION["skor"] ?? null;

// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "kuis_online");
if ($mysqli->connect_error) {
    die("<div class='error'>Koneksi gagal: " . htmlspecialchars($mysqli->connect_error) . "</div>");
}

// Jika form dikirim, proses jawaban
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["jawaban"])) {
    $skor = 0;
    foreach ($_POST["jawaban"] as $idSoal => $jawabanUser) {
        $stmt = $mysqli->prepare("SELECT jawaban_benar FROM soal WHERE id = ?");
        $stmt->bind_param("i", $idSoal);
        $stmt->execute();
        $stmt->bind_result($jawabanBenar);
        if ($stmt->fetch()) {
            if (strcasecmp(trim($jawabanUser), trim($jawabanBenar)) === 0) {
                $skor++;
            }
        }
        $stmt->close();
    }
    $_SESSION["skor"] = $skor;
    header("Location: index.php");
    exit();
}

// Ambil 5 soal acak jika skor belum ada
$soals = [];
if ($skorTerakhir === null) {
    $result = $mysqli->query("SELECT id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d FROM soal ORDER BY RAND() LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        $soals[] = $row;
    }
    $_SESSION["soal_ids"] = array_column($soals, "id");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuis Online</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f6f9ff; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ddd; }
        .soal { margin: 20px 0; }
        .soal h3 { margin-bottom: 8px; }
        .score { color: #2196F3; }
        .logout-button { display: inline-block; margin-top: 20px; color: #fff; background: #f44336; padding: 8px 18px; border-radius: 4px; text-decoration: none; }
        .logout-button:hover { background: #c62828; }
        .pilihan { margin-bottom: 10px; }
        .message { color: #388e3c; }
    </style>
</head>
<body>
<div class="container">
    <h2>Halo, <?= $user ?>!</h2>
    <div class="soal">
        <?php if ($skorTerakhir !== null): ?>
            <div class="message">
                <h2>Terima kasih telah mengerjakan kuis!</h2>
            </div>
            <div class="score">
                <h3>Skor kamu: <strong><?= $skorTerakhir ?></strong> dari 5</h3>
            </div>
            <?php unset($_SESSION["skor"]); unset($_SESSION["soal_ids"]); ?>
        <?php elseif (!empty($soals)): ?>
            <form method="POST" action="index.php">
                <?php foreach ($soals as $index => $soal): ?>
                    <div class="soal-item">
                        <h3><?= ($index + 1) . ". " . htmlspecialchars($soal["pertanyaan"]) ?></h3>
                        <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                            <label class="pilihan">
                                <input type="radio" name="jawaban[<?= $soal["id"] ?>]" value="<?= $soal["pilihan_$opt"] ?>" required>
                                <?= strtoupper($opt) ?>. <?= htmlspecialchars($soal["pilihan_$opt"]) ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                <?php endforeach; ?>
                <button type="submit">Kirim Jawaban</button>
            </form>
        <?php else: ?>
            <p>Tidak ada soal tersedia.</p>
        <?php endif; ?>
    </div>
    <a href="logout.php" class="logout-button">Logout</a>
</div>
</body>
</html>