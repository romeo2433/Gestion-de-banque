<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>IGOR PRO</title>

    <!-- CSS -->
    <link rel="stylesheet" href="./ASSET/CSS/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="./ASSET/CSS/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="./ASSET/CSS/admin-pro.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

    <!-- SIDEBAR -->
    <?php include("partials/sidebar.php"); ?>

    <!-- CONTENT -->
    <div class="content-wrapper">
        <section class="content pt-4">
            <div class="container-fluid">

                <?php
                if (isset($content) && file_exists($content)) {
                    include $content;
                } else {
                    echo "<div class='alert alert-danger'>Page introuvable</div>";
                }
                ?>

            </div>
        </section>
    </div>

    <!-- FOOTER -->
    <footer class="main-footer text-center">
        <strong>&copy; <?php echo date('Y'); ?> IGOR PRO</strong>
    </footer>

</div>

<!-- JS -->
<script src="./ASSET/CSS/plugins/jquery/jquery.min.js"></script>
<script src="./ASSET/CSS/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="./ASSET/CSS/dist/js/adminlte.js"></script>

</body>
</html>