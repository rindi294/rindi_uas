<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'koneksi.php';

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

$idUser = $_SESSION['idUser'];
$success = '';
$error = '';

// Ambil data user dan mahasiswa
$stmt = $conn->prepare("
    SELECT u.*, m.npm, m.nama, m.prodi, m.email AS emailMhs, m.alamat, m.idMhs
    FROM tbl_user u
    LEFT JOIN tbl_mahasiswa m ON u.idMhs = m.idMhs
    WHERE u.idUser = ?
");
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Data user tidak ditemukan.");
}

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaLengkap = trim($_POST['namaLengkap']);
    $emailUser   = trim($_POST['emailUser']);

    if (empty($namaLengkap) || empty($emailUser)) {
        $error = "Nama lengkap dan email user wajib diisi.";
    } else {
        $stmt = $conn->prepare("UPDATE tbl_user SET namaLengkap = ?, email = ? WHERE idUser = ?");
        $stmt->bind_param("ssi", $namaLengkap, $emailUser, $idUser);
        $stmt->execute();
        $stmt->close();

        // Jika mahasiswa terkait
        if (!empty($user['idMhs'])) {
            $npm      = trim($_POST['npm']);
            $nama     = trim($_POST['nama']);
            $prodi    = trim($_POST['prodi']);
            $emailMhs = trim($_POST['emailMhs']);
            $alamat   = trim($_POST['alamat']);

            $stmt = $conn->prepare("UPDATE tbl_mahasiswa SET npm = ?, nama = ?, prodi = ?, email = ?, alamat = ? WHERE idMhs = ?");
            $stmt->bind_param("sssssi", $npm, $nama, $prodi, $emailMhs, $alamat, $user['idMhs']);
            $stmt->execute();
            $stmt->close();

            // Update variabel tampilan
            $user['npm'] = $npm;
            $user['nama'] = $nama;
            $user['prodi'] = $prodi;
            $user['emailMhs'] = $emailMhs;
            $user['alamat'] = $alamat;
        }

        $user['namaLengkap'] = $namaLengkap;
        $user['email'] = $emailUser;
        $success = "Profil berhasil diperbarui.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #6f42c1, #0dcaf0);
            min-height: 100vh;
            padding-top: 40px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(90deg, #6610f2, #0d6efd);
            color: white;
            padding: 25px 30px;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #0dcaf0;
            box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 25px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            color: #495057;
        }
        .btn-primary {
            background-color: #0dcaf0;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0bb6d4;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3><i class="bi bi-person-circle"></i> Edit Profil</h3>
                </div>
                <div class="card-body p-4">

                    <form method="POST">
                        <div class="section-title">Data User</div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="namaLengkap" class="form-control" value="<?= htmlspecialchars($user['namaLengkap']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="emailUser" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <?php if (!empty($user['idMhs'])): ?>
                            <div class="section-title">Data Mahasiswa</div>
                            <div class="mb-3">
                                <label class="form-label">NPM</label>
                                <input type="text" name="npm" class="form-control" value="<?= htmlspecialchars($user['npm']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prodi</label>
                                <input type="text" name="prodi" class="form-control" value="<?= htmlspecialchars($user['prodi']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Mahasiswa</label>
                                <input type="email" name="emailMhs" class="form-control" value="<?= htmlspecialchars($user['emailMhs']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" required><?= htmlspecialchars($user['alamat']) ?></textarea>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary px-4">💾 Simpan</button>
                            <a href="index.php" class="btn btn-secondary px-4">← Kembali</a>
                        </div>
                    </form>

                    <!-- SweetAlert Notification -->
                    <?php if ($success): ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: '<?= $success ?>',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        </script>
                    <?php elseif ($error): ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: <?= json_encode($error) ?>,
                            });
                        </script>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
