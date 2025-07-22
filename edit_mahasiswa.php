<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

$idMhs = $_GET['id'] ?? '';
$error = '';
$success = '';
$mahasiswa = [];

if ($idMhs) {
    $stmt = $conn->prepare("SELECT * FROM tbl_mahasiswa WHERE idMhs = ?");
    $stmt->bind_param("i", $idMhs);
    $stmt->execute();
    $result = $stmt->get_result();
    $mahasiswa = $result->fetch_assoc();
    $stmt->close();

    if (!$mahasiswa) {
        $error = "Data mahasiswa tidak ditemukan.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npm = trim($_POST['npm']);
    $nama = trim($_POST['nama']);
    $prodi = trim($_POST['prodi']);
    $email = trim($_POST['email']);
    $alamat = trim($_POST['alamat']);

    $fotoLama = $mahasiswa['foto'] ?? '';
    $fotoFinal = $fotoLama;

    if (!empty($_FILES['foto']['name'])) {
        $tmpName = $_FILES['foto']['tmp_name'];
        $namaBaru = uniqid() . "_" . basename($_FILES['foto']['name']);
        move_uploaded_file($tmpName, "uploads/" . $namaBaru);
        $fotoFinal = $namaBaru;
    }

    if ($npm && $nama && $prodi) {
        $stmt = $conn->prepare("UPDATE tbl_mahasiswa SET npm=?, nama=?, prodi=?, email=?, alamat=?, foto=? WHERE idMhs=?");
        $stmt->bind_param("ssssssi", $npm, $nama, $prodi, $email, $alamat, $fotoFinal, $idMhs);
        if ($stmt->execute()) {
            $success = "Data berhasil diperbarui!";
            $mahasiswa = array_merge($mahasiswa, $_POST, ['foto' => $fotoFinal]);
        } else {
            $error = "Gagal update: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "NPM, Nama, dan Prodi wajib diisi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #e0f2f1;
        }
        .card {
            margin-top: 60px;
            border-radius: 15px;
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-label {
            color: #00695c;
            font-weight: 600;
        }
        .card-title {
            color: #004d40;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #00796b;
            border: none;
        }
        .btn-primary:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card mx-auto p-4" style="max-width: 720px;">
        <h3 class="card-title mb-3">Edit Mahasiswa</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">NPM *</label>
                <input type="text" name="npm" class="form-control" required value="<?= htmlspecialchars($mahasiswa['npm'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nama *</label>
                <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($mahasiswa['nama'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Prodi *</label>
                <input type="text" name="prodi" class="form-control" required value="<?= htmlspecialchars($mahasiswa['prodi'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($mahasiswa['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control"><?= htmlspecialchars($mahasiswa['alamat'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto (opsional)</label><br>
                <?php if (!empty($mahasiswa['foto'])): ?>
                    <img src="uploads/<?= $mahasiswa['foto'] ?>" width="100" class="mb-2 rounded border"><br>
                <?php endif; ?>
                <input type="file" name="foto" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="mahasiswa.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<!-- SweetAlert Notification -->
<?php if ($success): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= $success ?>',
    timer: 1500,
    showConfirmButton: false
}).then(() => {
    window.location.href = 'mahasiswa.php';
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
