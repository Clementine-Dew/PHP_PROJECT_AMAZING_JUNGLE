<?php

/*
        --------------------------------------------
        --                                        --
        --           MY AMAZING JUNGLE            --
        --                                        --
        --------------------------------------------
*/

$consumerId = $_SESSION['user']['id'];

/*
        --------------------------------------------
                       WATERING DATE
        --------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['watered_date'])) {
    $myJungleId = $_POST['my_jungle_id'];
    $wateredDate = $_POST['watered_date'];

    $sql =" UPDATE 
                my_jungle
            SET
                watered_date = ?
            WHERE 
                id = ?
            AND 
                fk_consumer = ?";

    $stmt = $pdo -> prepare($sql);
    $stmt -> execute([
        $wateredDate,
        $myJungleId,
        $consumerId
    ]);
}


$sql = "SELECT
            my_jungle.[id] AS my_jungle_id,
            my_jungle.[location],
            my_jungle.[watered_date],
            plant.[id] AS fk_plant,
            plant.[name],
            plant.[link],
            plant.[exposure],
            plant.[watering_frequency_autumn_winter],
            plant.[watering_frequency_spring_summer]
        FROM 
            my_jungle
        JOIN 
            plant ON plant.id = my_jungle.fk_plant
        WHERE
            my_jungle.fk_consumer = ?
        ORDER BY 
            plant.name";

$stmt = $pdo -> prepare($sql);
$stmt -> execute([$consumerId]);

$my_jungle = $stmt -> fetchAll();

/*
        --------------------------------------------
                       NEXT WATERING
        --------------------------------------------
*/

foreach ($my_jungle as $elem => $jungle_plant) {
    $my_jungle[$elem]['next_watering'] = null;
    $frequency = $jungle_plant['watering_frequency_autumn_winter'];

    if (!empty($jungle_plant['watered_date'])) {
        $nextWatering = new DateTime($jungle_plant['watered_date']);
        $nextWatering -> modify("+ $frequency days");
        $my_jungle[$elem]['next_watering'] = $nextWatering -> format('d-m-Y');
    }
}

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->

<h1>Ma jungle</h1>
<br>
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

                
                <form action="" method="post">
                    <label for="watered_date_<?= $plant['my_jungle_id'] ?>">Dernier arrosage :</label>
                    <input type="hidden" name="my_jungle_id" value="<?= $plant['my_jungle_id'] ?>">
                    <input type="date" name="watered_date" id="watered_date_<?= $plant['my_jungle_id'] ?>">
                    
                    <button>Calculer</button>
                </form>

                <?php if ($plant['next_watering']) : ?>
                    <div class="next_watering">
                        <p>Dernier arrosage : <?= date('d/m/Y', strtotime($plant['watered_date'])) ?></p>
                        <p>Prochain arrosage : <?= date('d/m/Y', strtotime($plant['next_watering'])) ?></p>
                    </div>
                <?php endif ?>
            </article>
        <?php endforeach ?>
    </div>
<?php endif ?>