<?php

session_start();

require_once "../config/database.php";

$pesan = "";
$pesan_type = "";
$mode = "login";

/*
|--------------------------------------------------------------------------
| Tentukan mode dari URL
|--------------------------------------------------------------------------
| auth.php              = login
| auth.php?mode=register = register
*/

if (isset($_GET["mode"]) && $_GET["mode"] === "register") {
    $mode = "register";
}

/*
|--------------------------------------------------------------------------
| PROSES FORM
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $aksi = $_POST["aksi"] ?? "";

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    if ($aksi === "login") {

        $mode = "login";

        $login = trim($_POST["login"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($login === "" || $password === "") {

            $pesan = "Email/username dan password wajib diisi.";
            $pesan_type = "error";

        } else {

            /*
            |--------------------------------------------------------------------------
            | CEK DONATUR
            |--------------------------------------------------------------------------
            */

            $query = $pdo->prepare("
                SELECT id_donatur, nama, email, password
                FROM donatur
                WHERE email = ?
            ");

            $query->execute([$login]);

            $donatur = $query->fetch(PDO::FETCH_ASSOC);

            if ($donatur && password_verify($password, $donatur["password"])) {

                $_SESSION["login"] = true;
                $_SESSION["role"] = "donatur";
                $_SESSION["id_donatur"] = $donatur["id_donatur"];
                $_SESSION["nama"] = $donatur["nama"];

                header("Location: ../index.php");
                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK ADMIN
            |--------------------------------------------------------------------------
            */

            $query = $pdo->prepare("
                SELECT id_admin, nama, username, password
                FROM admin
                WHERE username = ?
            ");

            $query->execute([$login]);

            $admin = $query->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin["password"])) {

                $_SESSION["login"] = true;
                $_SESSION["role"] = "admin";
                $_SESSION["id_admin"] = $admin["id_admin"];
                $_SESSION["nama"] = $admin["nama"];

                header("Location: ../admin/dashboard.php");
                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | LOGIN GAGAL
            |--------------------------------------------------------------------------
            */

            $pesan = "Username/email atau password salah.";
            $pesan_type = "error";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER DONATUR
    |--------------------------------------------------------------------------
    */

    elseif ($aksi === "register") {

        $mode = "register";

        $nama = trim($_POST["nama"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $no_hp = trim($_POST["no_hp"] ?? "");
        $alamat = trim($_POST["alamat"] ?? "");

        if ($nama === "" || $email === "" || $password === "") {

            $pesan = "Nama, email, dan password wajib diisi.";
            $pesan_type = "error";

        } else {

            /*
            |--------------------------------------------------------------------------
            | CEK EMAIL
            |--------------------------------------------------------------------------
            */

            $cek = $pdo->prepare("
                SELECT id_donatur
                FROM donatur
                WHERE email = ?
            ");

            $cek->execute([$email]);

            if ($cek->fetch()) {

                $pesan = "Email sudah terdaftar.";
                $pesan_type = "error";

            } else {

                /*
                |--------------------------------------------------------------------------
                | HASH PASSWORD
                |--------------------------------------------------------------------------
                */

                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /*
                |--------------------------------------------------------------------------
                | INSERT DONATUR
                |--------------------------------------------------------------------------
                */

                $query = $pdo->prepare("
                    INSERT INTO donatur
                    (nama, email, password, no_hp, alamat)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $query->execute([
                    $nama,
                    $email,
                    $password_hash,
                    $no_hp,
                    $alamat
                ]);


                /*
                |--------------------------------------------------------------------------
                | BERHASIL REGISTER
                |--------------------------------------------------------------------------
                */

                header("Location: auth.php?registered=1");
                exit;
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| PESAN BERHASIL REGISTER
|--------------------------------------------------------------------------
*/

if (isset($_GET["registered"]) && $_GET["registered"] == "1") {

    $pesan = "Pendaftaran berhasil. Silakan login.";
    $pesan_type = "success";
    $mode = "login";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website with Login & Register</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style_auth.css">
</head>
<body>

    <header class="header">
        <nav class="navbar">
            <a href="#">Home</a>
            
        </nav>

        
    </header>

    <div class="background"></div>

    <div class="container">
        <div class="content">
            

            <div class="text-sci">
                <h2>Donana!<br><span>Donasi Kita Bersama</span></h2>
                <p>Kita hidup dari apa yang kita dapatkan, tetapi kita menciptakan kehidupan dari apa yang kita berikan.</p>
            </div>

            <div class="social-icons">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
            </div>
        </div>

        <div class="logreg-box<?= $mode === "register" ? " active" : "" ?>">
            <!-- Login Form -->
           <div class="form-box login">

    <form method="POST" action="auth.php">

        <input type="hidden" name="aksi" value="login">

        <h2>Sign In</h2>

        <?php if ($mode === "login" && $pesan): ?>
            <div class="message <?= $pesan_type ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <div class="input-box">
            <input type="text" name="login" required>
            <label>Email / Username</label>
            <i class='bx bxs-user icon'></i>
        </div>

        <div class="input-box">
            <input type="password" name="password" required>
            <label>Password</label>
            <i class='bx bxs-lock-alt icon'></i>
        </div>

        <div class="remember-forgot">
            <label>
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <a href="#">Forgot Password?</a>
        </div>

        <button type="submit" class="btn">
            Sign In
        </button>

        <div class="login-register">
            <p>
                Don't have an account?
                <a href="#" class="register-link">Sign Up</a>
            </p>
        </div>

    </form>

</div>

            <!-- Register Form -->
            <div class="form-box register">

    <form method="POST" action="auth.php">

        <input type="hidden" name="aksi" value="register">

        <h2>Sign Up</h2>

        <?php if ($mode === "register" && $pesan): ?>
            <div class="message <?= $pesan_type ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <div class="input-box">
            <input type="text" name="nama" required>
            <label>Nama</label>
            <i class='bx bxs-user icon'></i>
        </div>

        <div class="input-box">
            <input type="email" name="email" required>
            <label>Email</label>
            <i class='bx bxs-envelope icon'></i>
        </div>

        <div class="input-row">

            <div class="input-box half">
                <input type="text" name="no_hp">
                <label>No. HP</label>
                <i class='bx bxs-phone icon'></i>
            </div>

            <div class="input-box half">
                <input type="text" name="alamat">
                <label>Alamat</label>
                <i class='bx bxs-map icon'></i>
            </div>

        </div>

        <div class="input-box">
            <input type="password" name="password" required>
            <label>Password</label>
            <i class='bx bxs-lock-alt icon'></i>
        </div>

        

        <button type="submit" class="btn">
            Sign Up
        </button>

        <div class="login-register">
            <p>
                Already have an account?
                <a href="#" class="login-link">Sign In</a>
            </p>
        </div>

    </form>

</div>
        </div>
    </div>
<script src="../assets/js/scripct_auth.js"></script>

</body>
</html>