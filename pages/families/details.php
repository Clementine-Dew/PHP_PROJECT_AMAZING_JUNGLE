<?php

/*
        --------------------------------------------
        --                                        --
        --          DETAILS FAMILY PAGE           --
        --                                        --
        --------------------------------------------
                       ID RECOVERY
        --------------------------------------------
*/

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$family = false;
$plants = false;

if ($id !== false && $id !== null) {

    $sql = "SELECT  
                f.[id],
                f.[name]
            FROM 
                family AS f
            WHERE
                f.id = ?";

    $stmt = $pdo -> prepare($sql);
    $stmt -> execute([$id]);

    $family = $stmt -> fetch();

    $title = 'Famille : ' . $family["name"];

/*
        --------------------------------------------
                    PLANTS' FAMILY RECOVERY
        --------------------------------------------
*/

    $sql = "SELECT
                p.id,
                p.name
            FROM
                plant AS p
            WHERE
                p.fk_family = ?";

    $stmt = $pdo -> prepare($sql);
    $stmt -> execute([$id]);

    $plants = $stmt -> fetchAll();
}

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->

<?php if (!$family) : ?>

    <h1>Famille introuvable.</h1>

<?php else : ?>

    <h1><?= htmlspecialchars($family['name']) ?></h1>

    <dl>
        <dt>Nom :</dt>
        <dd><?= htmlspecialchars($family['name']) ?></dd>

        <dt>Liste des plantes appartenant à cette famille :</dt>
        <dd>
            <?php if (count($plants) === 0) : ?>
                <p>Aucune plante répertoriée.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($plants as $plant) : ?>
                        <li>
                            <a href="?page=plant-details&id=<?= $plant['id'] ?>">
                                <?= htmlspecialchars($plant['name']) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </dd>
    </dl>

<?php endif ?>