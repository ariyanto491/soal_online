<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kuis_online";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika form dikirim, simpan soal ke database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pertanyaan"], $_POST["jawaban1"], $_POST["jawaban2"], $_POST["jawaban3"], $_POST["jawaban_benar"])) {
    $pertanyaan = $_POST["pertanyaan"];
    $jawaban1 = $_POST["jawaban1"];
    $jawaban2 = $_POST["jawaban2"];
    $jawaban3 = $_POST["jawaban3"];
    $jawaban_benar = $_POST["jawaban_benar"];

    // Gunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("INSERT INTO soal (pertanyaan, jawaban1, jawaban2, jawaban3, jawaban_benar) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $pertanyaan, $jawaban1, $jawaban2, $jawaban3, $jawaban_benar);

    if ($stmt->execute()) {
        echo "<h2>Soal berhasil ditambahkan!</h2>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Soal Kuis</h2>
        <form action="input_soal.php" method="POST">
            <label>Pertanyaan:</label>
            <textarea name="pertanyaan" required></textarea><br><br>

            <label>Opsi A:</label>
            <input type="text" name="jawaban1" required><br><br>

            <label>Opsi B:</label>
            <input type="text" name="jawaban2" required><br><br>

            <label>Opsi C:</label>
            <input type="text" name="jawaban3" required><br><br>

            <label>Jawaban Benar:</label>
            <select name="jawaban_benar" required>
                <option value="">Pilih Jawaban Benar</option>
                <option value="jawaban1">Opsi A</option>
                <option value="jawaban2">Opsi B</option>
                <option value="jawaban3">Opsi C</option>
            </select><br><br>

            <input type="submit" value="Tambahkan Soal">
        </form>
    </div>
</body>
</html>