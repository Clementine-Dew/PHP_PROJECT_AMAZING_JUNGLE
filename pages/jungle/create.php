<?php

/*
        --------------------------------------------
        --                                        --
        --           ADD PLANT TO JUNGLE          --
        --                                        --
        --------------------------------------------
*/

$plantId = filter_input(INPUT_POST, 'plant_id', FILTER_VALIDATE_INT);
$consumerId = $_SESSION['user']['id'];

if ($plantId) {
    $sql = "INSERT INTO my_jungle (fk_consumer, fk_plant)
            VALUES (?, ?)";

    $stmt = $pdo -> prepare($sql);
    $stmt -> execute([$consumerId, $plantId]);
}

header('Location: index.php?page=jungle');
exit;

?>