<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kuis_online";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan pengguna sudah login
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

// Ambil soal dari database
$sql = "SELECT * FROM soal";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuis Online</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>kerjakan</h2>
        <form action="process.php" method="POST">
            <p>Nama Siswa: <strong><?php echo $_SESSION["user"]; ?></strong></p>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='question-box'>";
                    echo "<p class='question'>" . htmlspecialchars($row["pertanyaan"]) . "</p>";
                    echo "<label class='option'><input type='radio' name='jawaban_benar[" . $row["id"] . "]' value='" . htmlspecialchars($row["jawaban1"]) . "'> " . htmlspecialchars($row["jawaban1"]) . "</label>";
                    echo "<label class='option'><input type='radio' name='jawaban_benar[" . $row["id"] . "]' value='" . htmlspecialchars($row["jawaban2"]) . "'> " . htmlspecialchars($row["jawaban2"]) . "</label>";
                    echo "<label class='option'><input type='radio' name='jawaban_benar[" . $row["id"] . "]' value='" . htmlspecialchars($row["jawaban3"]) . "'> " . htmlspecialchars($row["jawaban3"]) . "</label>";
                    echo "</div>";
                }
            } else {
                echo "<p>Tidak ada soal tersedia.</p>";
            }
            ?>

            <input type="submit" value="Kirim jawaban_benar">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>