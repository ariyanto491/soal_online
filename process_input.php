<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kuis_online";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$pertanyaan = $_POST["pertanyaan"];
$jawaban1 = $_POST["jawaban1"];
$jawaban2 = $_POST["jawaban2"];
$jawaban3 = $_POST["jawaban3"];
$jawaban_benar = $_POST["jawaban_benar"];

$jawaban_benar = "";
if ($jawaban_benar == "jawaban1") $jawaban_benar = $jawaban1;
if ($jawaban_benar == "jawaban2") $jawaban_benar = $jawaban2;
if ($jawaban_benar == "jawaban3") $jawaban_benar = $jawaban3;

$sql = "INSERT INTO soal (pertanyaan, jawaban1, jawaban2, jawaban3, jawaban_benar) 
        VALUES ('$pertanyaan', '$jawaban1', '$jawaban2', '$jawaban3', '$jawaban_benar')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Soal berhasil ditambahkan!</h2>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>