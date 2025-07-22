<?php
session_start();
include 'koneksi.php';

$login_status = ""; // sukses | password_salah | username_tidak_ditemukan

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM tbl_user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['idUser'] = $user['idUser'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level'] ?? 'user';
            $login_status = "sukses";
        } else {
            $login_status = "password_salah";
        }
    } else {
        $login_status = "username_tidak_ditemukan";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Autentikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .form-icon {
            font-size: 48px;
            color: #0d6efd;
            display: block;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card col-md-6 col-lg-4 mx-auto">
        <div class="form-icon">🔐</div>
        <h3 class="text-center mb-3">Login Akun</h3>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>

        <p class="text-center mt-3">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>
    </div>
</div>

<!-- SweetAlert Feedback -->
<?php if ($login_status === "sukses"): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil Login',
        text: 'Anda akan diarahkan ke dashboard...',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = 'index.php';
    });
</script>
<?php elseif ($login_status === "password_salah"): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Password Salah!',
        text: 'Silakan coba lagi.',
    });
</script>
<?php elseif ($login_status === "username_tidak_ditemukan"): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Username Tidak Ditemukan!',
        text: 'Periksa kembali username Anda.',
    });
</script>
<?php endif; ?>

</body>
</html>
