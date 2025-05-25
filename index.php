<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Cek apakah jawaban sudah dikirim
$skor_terakhir = $_SESSION["skor"] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kuis Online</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Halo, <?php echo htmlspecialchars($_SESSION["user"]); ?>!</h2>
<div class="soal">
        <?php 
        

        if ($skor_terakhir !== null) {
            // Jika jawaban sudah dikirim, tampilkan hasil skor
            echo "<p class='message text-primary'><h2>Terima kasih telah mengerjakan kuis ini, </h2></p>";
            echo "<p class='score'><h2>Skor kamu: <strong>$skor_terakhir</strong></h2></p>";
            unset($_SESSION["skor"]); // Hapus skor agar tidak muncul terus
        } else {
            // Jika belum ada jawaban, tampilkan soal
            include("process.php");
        }
        ?>
        </div>

        <?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kuis_online";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='error'>Koneksi gagal: " . $conn->connect_error . "</div>");
}

// Ambil soal dari database secara acak
$sql = "SELECT * FROM soal ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);

// Proses jawaban setelah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["jawaban"])) {
    $skor = 0;
    foreach ($_POST["jawaban"] as $id_soal => $jawaban_user) {
        $sql = "SELECT jawaban_benar FROM soal WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_soal);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (trim(strtolower($jawaban_user)) == trim(strtolower($row["jawaban_benar"]))) {
                $skor += 1;
            }
        }
    }

    // Simpan skor ke session
    $_SESSION["skor"] = $skor;

    // Redirect kembali ke `index.php` agar hanya skor yang ditampilkan
    header("Location: index.php");
    exit();
}
?>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>