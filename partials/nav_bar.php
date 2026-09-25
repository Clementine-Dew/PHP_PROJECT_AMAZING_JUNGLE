<nav>
    <ul>
        <li><a href="index.php">🌵 Amazing Jungle</a></li>
        <?php if (isset($_SESSION['user'])) : ?>
            <li><a href="index.php?page=plants">Catalogue</a></li>
            <li><a href="index.php?page=families">Familles</a></li>
            <li><a href="index.php?page=jungle">Ma jungle</a></li>
        <?php endif ?>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') : ?>
            <li><a href="index.php?page=plant-create">Ajouter une plante</a></li>
        <?php endif ?>
        <?php if (!isset($_SESSION['user'])) : ?>
            <li><a href="index.php?page=login" class="btn_style">Se connecter</a></li>
            <li><a href="index.php?page=register" class="btn_style">S'inscrire</a></li>
        <?php else : ?>
            <li><a href="index.php?page=logout" class="btn_style">Se déconnecter</a></li>
        <?php endif ?>
    </ul>
</nav>