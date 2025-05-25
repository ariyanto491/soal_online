<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuis Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color:rgb(255, 255, 255);
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            background: rgba(120, 120, 212, 0.3); /* Warna semi transparan */
            backdrop-filter: blur(10px); /* Efek blur */
            background-size: cover;
            background-repeat: no-repeat;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 8px 26px rgba(0,0,0,0.1);
        }
        .transparan {
    opacity: 0.5; /* Nilai antara 0 (transparan penuh) hingga 1 (tidak transparan) */
}
    </style>
</head>
<body>
    <div class="container">
        <h5 class="text-success">Pilihlah Jawaban yang Paling Tepat!</h5>
        <form action="" method="POST">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p>" . htmlspecialchars($row["pertanyaan"]) . "</p>";
                    echo "<input type='radio' name='jawaban[" . $row["id"] . "]' value='" . $row["jawaban1"] . "'> " . htmlspecialchars($row["jawaban1"]) . "<br>";
                    echo "<input type='radio' name='jawaban[" . $row["id"] . "]' value='" . $row["jawaban2"] . "'> " . htmlspecialchars($row["jawaban2"]) . "<br>";
                    echo "<input type='radio' name='jawaban[" . $row["id"] . "]' value='" . $row["jawaban3"] . "'> " . htmlspecialchars($row["jawaban3"]) . "<br><br>";
                }
            } else {
                echo "<p class='text-danger'>Belum ada soal tersedia.</p>";
            }
            ?>
            <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
        </form>
    </div>

    <?php
    // Proses jawaban setelah dikirim
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["jawaban"])) {
        $nama_siswa = $_SESSION["user"] ?? "Anonim";
        $skor = 0;

        foreach ($_POST["jawaban"] as $id_soal => $jawaban_user) {
            $sql = "SELECT jawaban_benar FROM soal WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_soal);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $benar = (trim(strtolower($jawaban_user)) == trim(strtolower($row["jawaban_benar"]))) ? 1 : 0;
                $skor += $benar;

                // Simpan jawaban siswa
                $sql_insert = "INSERT INTO peserta (nama, skor) VALUES (?, ?) ON DUPLICATE KEY UPDATE skor=?";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sii", $nama_siswa, $skor, $skor);
                $stmt_insert->execute();
            }
        }

        echo "<div class='container mt-3'>";
        echo "<p class='score'>Skor kamu: <strong>$skor</strong> dari " . count($_POST["jawaban"]) . " soal.</p>";
        echo "<p class='message text-primary'>Terima kasih telah mengikuti kuis! 🎉</p>";
        echo "</div>";
    }

    $conn->close();
    ?>
</body>
</html>