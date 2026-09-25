<?php

/*
        --------------------------------------------
        --                                        --
        --           MY AMAZING JUNGLE            --
        --                                        --
        --------------------------------------------
*/

$consumerId = $_SESSION['user']['id'];

$sql = "SELECT
            my_jungle.[id],
            my_jungle.[location],
            plant.[name],
            plant.[link],
            plant.[exposure],
            plant.[watering_frequency_autumn_winter],
            plant.[watering_frequency_spring_summer]
        FROM my_jungle
        JOIN plant ON plant.id = my_jungle.fk_plant
        WHERE
            my_jungle.fk_consumer = ?
        ORDER BY plant.name";

$stmt = $pdo -> prepare($sql);
$stmt -> execute([$consumerId]);

$my_jungle = $stmt -> fetchAll();

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->

<h1>Ma jungle</h1>

<?php if (empty($my_jungle)) : ?>
    <p class="empty_jungle">Ta jungle est vide ...</p>
    <p>Va faire un tour dans le <a href="index.php?page=plants">catalogue</a></p>

<?php else : ?>
    <div class="cards">
        <?php foreach ($my_jungle as $plant) : ?>

            <article class="card">
                <h2><?= htmlspecialchars($plant["name"]) ?></h2>

                <img src="<?= $plant['link'] ? htmlspecialchars($plant['link']) : 'assets/pictures/default-plant.jpg' ?>" alt="<?= htmlspecialchars($plant['name']) ?>" class="plant_picture">

                <div class="exposure"><img src="assets/icons/sun_icon.png" alt="Icone représentant un soleil" style="height: 20px;"> <?= $plant['exposure'] ?></div>

                <div class="watering"><img src="assets/icons/watering_icon.png" alt="Icone d'une goutte d'eau" style="height: 15px;">Tous les <?= $plant['watering_frequency_spring_summer'] ?> à <?= $plant['watering_frequency_autumn_winter'] ?> jour(s)</div>
            </article>
        <?php endforeach ?>
    </div>
<?php endif ?>