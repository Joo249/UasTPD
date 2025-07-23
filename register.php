<?php
require_once 'config/database.php';
require_once 'lib/google-authenticator.php';

$pesan = "";
$qr_url = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = $ga->createSecret();

    $key = base64_decode(getenv("AES_KEY"));
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted_email = base64_encode($iv . openssl_encrypt($email, 'AES-128-CBC', $key, 0, $iv));
    $hash_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $koneksi->prepare("INSERT INTO users (email, password, secret) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $encrypted_email, $hash_password, $secret);

    if ($stmt->execute()) {
        $pesan = "Registrasi berhasil. Scan QR berikut di Google Authenticator.";
        $qr_url = $ga->getQRCodeGoogleUrl('InventoriAman', $secret);
    } else {
        $pesan = "Gagal menyimpan data.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 100%; max-width: 450px;">
        <h3 class="text-center mb-4">Registrasi Pengguna</h3>

        <?php if ($pesan): ?>
            <div class="alert alert-info"><?= $pesan ?></div>
        <?php endif; ?>

        <?php if ($qr_url): ?>
            <div class="text-center mb-3">
                <img src="<?= $qr_url ?>" alt="QR Code">
                <p class="mt-2 small text-muted">Scan QR di aplikasi Google Authenticator.</p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
    </div>
</div>

</body>
</html>