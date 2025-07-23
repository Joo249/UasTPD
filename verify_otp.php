<?php
session_start();
require_once 'lib/google-authenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $secret = $_SESSION['secret'];

    if ($ga->verifyCode($secret, $otp, 2)) {
        $_SESSION['login'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $pesan = "Kode OTP salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Verifikasi OTP</h3>

        <?php if ($pesan): ?>
            <div class="alert alert-danger"><?= $pesan ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Masukkan Kode OTP:</label>
                <input type="text" class="form-control" name="otp" required autofocus>
            </div>
            <button type="submit" class="btn btn-success w-100">Verifikasi</button>
        </form>
    </div>
</div>

</body>
</html>