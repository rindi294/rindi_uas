<?php
session_start();
include 'koneksi.php';

$error = "";
$success = "";
$showSuccessAlert = false;
$showErrorAlert = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_plain = trim($_POST['password']);

    // Validasi
    if (empty($username) || empty($password_plain)) {
        $error = "Username dan password wajib diisi.";
        $showErrorAlert = true;
    } else {
        $stmt = $conn->prepare("SELECT idUser FROM tbl_user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan.";
            $showErrorAlert = true;
        } else {
            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO tbl_user (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password_hashed);

            if ($stmt->execute()) {
                $success = "Registrasi berhasil!";
                $showSuccessAlert = true;
            } else {
                $error = "Gagal menyimpan data user: " . $stmt->error;
                $showErrorAlert = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            font-weight: bold;
            color: #0d47a1;
            text-shadow: 1px 1px 1px #bbdefb;
        }
        .btn-primary {
            background-color: #0d47a1;
            border: none;
            transition: 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #1565c0;
        }
        .form-label {
            color: #0d47a1;
            font-weight: 500;
        }
        a {
            color: #0d47a1;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <h3 class="form-title text-center mb-4">Form Registrasi</h3>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>

                <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert -->
<?php if ($showSuccessAlert): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Registrasi Berhasil!',
        text: 'Akun berhasil dibuat. Silakan login.',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'login.php';
    });
</script>
<?php elseif ($showErrorAlert): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal Registrasi',
        text: '<?= $error ?>',
        confirmButtonText: 'Coba Lagi'
    });
</script>
<?php endif; ?>
</body>
</html>
