<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "sig_lahan_pertanian";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi Base URL
// Ganti nilai ini dengan URL ngrok Anda saat aplikasi di-online-kan (misal: https://1234-abcd.ngrok.io)
 //$config_base_url = "http://localhost/SISTEMSIGNATIVE";
//$config_base_url = "https://rachelle-starrier-thoughtfully.ngrok-free.dev/SISTEMSIGNATIVE";

 $config_base_url = "http://darkgoldenrod-mink-191162.hostingersite.com/SISTEMSIGNATIVE";



function base_url($url = '') {
    global $config_base_url;
    return $config_base_url . '/' . ltrim($url, '/');
}
?>
