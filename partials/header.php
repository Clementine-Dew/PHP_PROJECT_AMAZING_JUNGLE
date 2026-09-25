<!DOCTYPE html>
<html lang="fr-BE">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "Amazing jungle" ?></title>

    <!-- ICON -->
    <link rel="shortcut icon" href="assets/icons/icon_plant.png" type="image/x-icon">

    <!-- CSS -->
     <link rel="stylesheet" href="styles/root.css">
    <link rel="stylesheet" href="styles/style.css">

</head>

<body>

    <!-- CODE NAV_BAR's FILE IMPORT -->
    <?php require_once 'nav_bar.php' ?>
    
    <?php if (isset($_SESSION['user'])) : ?>
        <p id="user_log_info">Bienvenue, <?= $_SESSION['user']['alias'] ?> (<span class="italic"><?= $_SESSION['user']['role'] ?></span>) !</p>
    <?php endif ?>
    
    <main>