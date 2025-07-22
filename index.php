<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(to right, #0d47a1, #1976d2);
        }
        .card {
            border-radius: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.9);
            font-size: 24px;
        }
        .welcome {
            font-weight: 600;
            color: #0d47a1;
        }
        .card-title {
            font-weight: bold;
        }
        .btn-custom {
            background-color: #0d47a1;
            color: white;
        }
        .btn-custom:hover {
            background-color: #1565c0;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <div class="d-flex">
            <span class="navbar-text text-white me-3">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($username); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="card shadow-lg p-4">
        <h3 class="welcome mb-2"><i class="bi bi-hand-thumbs-up"></i> Selamat datang, <?= htmlspecialchars($username); ?>!</h3>
        <p class="text-muted">Silakan jelajahi menu yang tersedia di bawah ini.</p>

        <div class="row mt-4 g-4">
            <div class="col-md-4">
                <div class="card bg-white text-center p-4 h-100 border-start border-4 border-success">
                    <div class="icon-circle mx-auto text-success mb-2">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="card-title text-success">Data Mahasiswa</h5>
                    <p class="card-text">Kelola data mahasiswa.</p>
                    <a href="mahasiswa.php" class="btn btn-success btn-sm mt-2">Lihat Data</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white text-center p-4 h-100 border-start border-4 border-primary">
                    <div class="icon-circle mx-auto text-primary mb-2">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <h5 class="card-title text-primary">Profil Akun</h5>
                    <p class="card-text">Lihat dan ubah profil pengguna.</p>
                    <a href="profil.php" class="btn btn-primary btn-sm mt-2">Lihat Profil</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white text-center p-4 h-100 border-start border-4 border-danger">
                    <div class="icon-circle mx-auto text-danger mb-2">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <h5 class="card-title text-danger">Logout</h5>
                    <p class="card-text">Keluar dari aplikasi dengan aman.</p>
                    <a href="logout.php" class="btn btn-danger btn-sm mt-2">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
