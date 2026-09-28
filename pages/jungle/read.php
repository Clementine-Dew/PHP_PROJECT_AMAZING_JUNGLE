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
            my_jungle.[id] AS my_jungle_id,
            my_jungle.[location],
            my_jungle.[watered_date],
            plant.[id] AS plant_id,
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

/*
        --------------------------------------------
                       WATERING DATE
        --------------------------------------------
*/

$next_watering = null;

foreach ($my_jungle as $jungle_plant) {
    $plantId = $jungle_plant['plant_id'] . " ";
    echo $plantId . " : " ;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_watering'])) {
        $last_watering = $_POST['start_watering'];

        $sql =" INSERT INTO
                    my_jungle(watered_date)
                VALUES
                    ($last_watering)";

        $stmt = $pdo -> prepare($sql);

        $frequency = $jungle_plant['watering_frequency_autumn_winter'];

        $date = new DateTime($last_watering);
        $date -> modify("+ $frequency days");
        $jungle_plant['next_watering'] = $date -> format('d-m-Y');

        echo $jungle_plant['next_watering'] . " ";
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
                    <label for="start_watering">Dernier arrosage :</label>
                    <input type="date" name="start_watering" id="start_watering">

                    <button>Calculer</button>
                </form>

                <div>
                    <?php if(isset($last_watering)) : ?>
                        <p>Date précédente : <?= $last_watering ?></p>
                        <p>Suivant : <?= $jungle_plant['next_watering'] ?></p>
                    <?php endif ?>
                </div>

                <!-- <div class="next_watering">
                    <?php foreach ($my_jungle as $jungle_plant) : ?>
                        <?php if(isset($last_watering)) : ?>
                            <?= $jungle_plant['next_watering'] ?>
                        <?php endif ?>
                    <?php endforeach ?>
                </div> -->
                

            </article>
        <?php endforeach ?>
    </div>
<?php endif ?>