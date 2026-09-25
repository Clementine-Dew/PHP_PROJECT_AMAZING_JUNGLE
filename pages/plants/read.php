<?php

/*
        --------------------------------------------
        --                                        --
        --   PLANTS' LIST BY ALPHABETICAL ORDER   --
        --                                        --
        --------------------------------------------
*/

$sql = "SELECT
            [id],
            [name],
            [link],
            [fk_family]
        FROM
            plant
        ORDER BY [name]";

$plants = $pdo -> query($sql) -> fetchAll();

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->

<h1>Liste des plantes</h1>

<p><?= count($plants) ?> plante(s)</p>

<div class="cards">

    <?php foreach ($plants as $plant) : ?>

        <article class="card">
            <h2><?= $plant["name"] ?></h2>
            <a href="<?= $plant['link'] ?>" target="_blank" class="plant_picture">
                <img src="<?= $plant['link'] ?>"" alt="Photo de <?= $plant['name'] ?>" class="plant_picture">
            </a>

            <div class="actions">
                <a href="index.php?page=plant-details&amp;id=<?= $plant['id'] ?>" class="btn cube_style"><img src="assets/icons/loupe_icon.png" alt="Icone (loupe) du lien pour obtenir plus de détails sur la plante" style="height: 20px;"></a>

                <?php if (isset($_SESSION['user'])) : ?>
                    <form action="index.php?page=add-to-jungle" method="post">
                        <input type="hidden" name="plant_id" value="<?= $plant['id'] ?>">
                        <button class="btn actions cube_style"><img src="assets/icons/plant_love_icon.png" alt="Ajouter <?= htmlspecialchars($plant['name']) ?> à ma jungle" style="height: 20px;"></button>
                    </form>
                <?php endif ?>

                <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                <a href="index.php?page=plant-edit&amp;id=<?= $plant['id'] ?>" class="btn cube_style"><img src="assets/icons/pen_icon.png" alt="Icone (crayon) du lien pour modifier des informations de la plante" style="height: 20px;"></a>

                <form method="post" action="index.php?page=plant-delete" onsubmit="return confirm('Voulez-vous supprimer <?= $plant['name'] ?> ?')">
                    <input type="hidden" name="id" value="<?= $plant['id'] ?>">
                    <button class="btn actions cube_style"><img src="assets/icons/garbage_icon.png" alt="Icone (poubelle) du lien pour supprimer la plante" style="height: 20px;"></button>
                </form>

                <?php endif ?>

            </div>

        </article>

    <?php endforeach ?>

</div>