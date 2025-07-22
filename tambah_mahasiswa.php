<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npm = trim($_POST['npm']);
    $nama = trim($_POST['nama']);
    $prodi = trim($_POST['prodi']);
    $email = trim($_POST['email']);
    $alamat = trim($_POST['alamat']);

    $fotoName = $_FILES['foto']['name'];
    $fotoTmp = $_FILES['foto']['tmp_name'];
    $fotoPath = "uploads/" . basename($fotoName);

    if (!empty($npm) && !empty($nama) && !empty($prodi)) {
        if (!empty($fotoName)) {
            if (move_uploaded_file($fotoTmp, $fotoPath)) {
                $foto = $fotoName;
            } else {
                $error = "Upload foto gagal.";
            }
        } else {
            $foto = null;
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO tbl_mahasiswa (npm, nama, prodi, email, alamat, foto) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $npm, $nama, $prodi, $email, $alamat, $foto);

            if ($stmt->execute()) {
                // Ambil idMhs yang baru saja dibuat
                $idMhsBaru = $conn->insert_id;

                // Buat akun login default
                $username = $npm;
                $passwordDefault = password_hash($npm, PASSWORD_DEFAULT);
                $namaLengkap = $nama; // atau bisa ditambahkan form nama lengkap khusus
                $emailUser = $email;

                $stmtUser = $conn->prepare("INSERT INTO tbl_user (namaLengkap, email, username, password, idMhs) VALUES (?, ?, ?, ?, ?)");
                $stmtUser->bind_param("ssssi", $namaLengkap, $emailUser, $username, $passwordDefault, $idMhsBaru);

                if ($stmtUser->execute()) {
                    $success = "Data mahasiswa dan akun login berhasil disimpan.";
                } else {
                    $error = "Mahasiswa berhasil ditambahkan, tetapi akun gagal dibuat: " . $stmtUser->error;
                }
            } else {
                $error = "Gagal menyimpan data: " . $stmt->error;
            }
        }
    } else {
        $error = "Field bertanda * wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            min-height: 100vh;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-success {
            background-color: #1976d2;
            border-color: #1976d2;
        }

        .btn-success:hover {
            background-color: #0d47a1;
            border-color: #0d47a1;
        }

        .btn-secondary:hover {
            background-color: #607d8b;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card mx-auto p-4" style="max-width: 700px;">
            <div class="card-body">
                <h3 class="mb-4 text-primary"><i class="bi bi-person-plus-fill"></i> Tambah Mahasiswa</h3>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">NPM *</label>
                        <input type="text" name="npm" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama *</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prodi *</label>
                        <input type="text" name="prodi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success px-4">Simpan</button>
                        <a href="mahasiswa.php" class="btn btn-secondary px-4">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= $success ?>',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "mahasiswa.php";
            });
        </script>
    <?php elseif ($error): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $error ?>'
            });
        </script>
    <?php endif; ?>
</body>

</html>