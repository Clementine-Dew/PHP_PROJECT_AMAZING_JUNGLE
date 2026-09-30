<?php

/*
        --------------------------------------------
        --                                        --
        --              FAMILY PAGE               --
        --                                        --
        --------------------------------------------
*/

$sql = "SELECT
            f.id,
            f.name
        FROM 
            family AS f
        ORDER BY 
            [name]";

$families = $pdo -> query($sql) -> fetchAll();

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->
<div class="style">
    <div>
        <h1>Classification par famille</h1>
        <br>
        <table>
            <thead>
                <tr>
                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <th>Id</th>
                    <?php endif ?>
                    <th class="table_plant_name">Nom complet</th>
                    <th class="element"></th>
                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <th class="element"></th>
                        <th class="element"></th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($families as $family): ?>

                <tr>
                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <td><?= htmlspecialchars($family["id"]) ?></td>
                    <?php endif ?>
                    
                    <td class="table_plant_name"><?= htmlspecialchars($family["name"]) ?></td>

                    <td class="element"><a href="?page=family-details&id=<?= $family['id'] ?>"  class="btn"><img src="assets/icons/loupe_icon.png" alt="Icone (loupe) du lien pour obtenir plus de détails sur la plante" style="height: 20px;"></a></td>

                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <td class="element"><a href="?page=family-edit&id=<?= $family['id'] ?>"  class="btn"><img src="assets/icons/pen_icon.png" alt="Icone (crayon) du lien pour modifier des informations de la plante" style="height: 20px;"></a></td>
                    
                        <td class="element"><a href="?page=family-delete&id=<?= $family['id'] ?>"  class="btn"><img src="assets/icons/garbage_icon.png" alt="Icone (poubelle) du lien pour supprimer la plante" style="height: 20px;"></a></td>
                    <?php endif ?>
                </tr>

                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>