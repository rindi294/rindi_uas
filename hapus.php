<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah parameter ID mahasiswa dikirim
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data mahasiswa berdasarkan id
    $stmt = $conn->prepare("SELECT foto FROM tbl_mahasiswa WHERE idMhs = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Hapus file foto jika ada dan file tersebut ada di folder uploads/
        if (!empty($data['foto'])) {
            $fotoPath = 'uploads/' . $data['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        // Hapus data dari tbl_mahasiswa
        $delete = $conn->prepare("DELETE FROM tbl_mahasiswa WHERE idMhs = ?");
        $delete->bind_param("i", $id);
        if ($delete->execute()) {
            header("Location: mahasiswa.php?msg=deleted");
            exit;
        } else {
            echo "Gagal menghapus data: " . $delete->error;
        }

        $delete->close();
    } else {
        echo "Data mahasiswa tidak ditemukan.";
    }

    $stmt->close();
} else {
    echo "ID mahasiswa tidak diberikan.";
}
?>
