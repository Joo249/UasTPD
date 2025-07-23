<?php
session_start();
require_once 'config/database.php';

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT id, email, password, secret FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $key = base64_decode(getenv("AES_KEY"));

    while ($row = $result->fetch_assoc()) {
        $raw = base64_decode($row['email']);
        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);
        $decrypted_email = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, 0, $iv);

        if ($email === $decrypted_email && password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['secret'] = $row['secret'];
            header("Location: verify_otp.php");
            exit;
        }
    }
    $pesan = "Login gagal. Email atau password salah.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Aman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">Login Inventori Aman</h3>

        <?php if ($pesan): ?>
            <div class="alert alert-danger"><?= $pesan ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

</body>
</html>