<?php
// Main page: only logged-in users may see it.
require_once __DIR__ . '/includes/functions.php';
require_login();

include 'includes/head.php';
?>

<body class="text-light">

    <!-- NAVIGATION -->
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- HERO -->
        <?php include 'includes/hero.php'; ?>

        <!-- ORIGIN -->
        <?php include 'includes/origin.php'; ?>

        <!-- POWERS -->
        <?php include 'includes/powers.php'; ?>
    </main>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>

    <!-- SCRIPTS -->
    <?php include 'includes/scripts.php'; ?>

</body>
</html>
