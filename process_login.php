<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kuis_online";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Periksa apakah form telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["password"])) {
    $user = $_POST["username"];
    $pass = $_POST["password"]; // Tidak dienkripsi di sini, karena `password_hash` digunakan di penyimpanan

    // Gunakan prepared statement untuk mencari username
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Gunakan `password_verify()` untuk mengecek password
        if (password_verify($pass, $row["password"])) {
            $_SESSION["user"] = $user;
            header("Location: index.php"); // Redirect jika berhasil login
            exit();
        } else {
            echo "<h2>Password salah! Silakan coba lagi.</h2>";
        }
    } else {
        echo "<h2>Username tidak ditemukan! Pastikan akun sudah terdaftar.</h2>";
    }

    $stmt->close();
} else {
    echo "<h2>Harap masukkan username dan password.</h2>";
}

$conn->close();
?>