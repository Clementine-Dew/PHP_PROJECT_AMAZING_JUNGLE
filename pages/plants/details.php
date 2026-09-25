<?php

/*
        --------------------------------------------
        --                                        --
        --           DETAILS PLANT PAGE           --
        --                                        --
        --------------------------------------------
                        ID RECOVERY
        --------------------------------------------
*/

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$plant = false;

if ($id !== false && $id !== null) {

    $sql = "SELECT  p.id,
                    p.name,
                    p.link,
                    p.watering_frequency_spring_summer,
                    p.watering_frequency_autumn_winter,
                    p.sign_lack_water,
                    p.sign_excessive_water,
                    p.exposure,
                    p.temperature_min,
                    p.temperature_max,
                    p.sign_lack_light,
                    p.sign_excessive_light,
                    p.note,
                    p.fk_family,
                    f.id AS family_id,
                    f.name AS family_name
            FROM  plant AS p
                    JOIN family AS f ON p.fk_family = f.id
            WHERE p.id = ?";

    $stmt = $pdo -> prepare($sql);
    $stmt -> execute([$id]);

    $plant = $stmt -> fetch();
}

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->
        
<?php if (!$plant) : ?>
    <?php http_response_code(404); ?>
    <h1>Plante introuvable</h1>
    <p>Aucune plante ne correspond à l'id <?= $id ?>.</p>
<?php else : ?>
    <h1>Détails de <?= htmlspecialchars($plant["name"]) ?></h1>

    <dl>
        <dt>Température minimum :</dt>
        <dd><?= htmlspecialchars($plant['temperature_min'] ?? 'inconnue') ?></dd>

        <!-- <dt>Prix:</dt>
        <dd><?= number_format($livre['prix'], 2, ',', ' ') ?> &euro;</dd>


        <dt>Famille :</dt>
        <dd>
        <a href="?page=family-details&amp;id=<?= $plant['fk_family'] ?>">
            <?= htmlspecialchars($plant['family_name']) ?>
        </a>
        </dd>

    </dl>

    <a href="?page=plants" class="btn btn-back">Retour à la liste</a>

<?php endif ?>