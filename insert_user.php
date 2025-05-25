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

// Proses pendaftaran user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT); // Enkripsi password

    // Cek apakah username sudah ada
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<p class='text-danger'>Username sudah terdaftar! Pilih yang lain.</p>";
    } else {
        // Simpan user baru
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            echo "<p class='text-success'>Registrasi berhasil! Silakan login.</p>";
        } else {
            echo "<p class='text-danger'>Terjadi kesalahan, coba lagi.</p>";
        }
        $stmt->close();
    }
    $stmt_check->close();
}

$conn->close();
?>