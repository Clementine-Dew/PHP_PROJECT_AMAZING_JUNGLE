<?php

/*
        --------------------------------------------
        --                                        --
        --            CREATE PLANT PAGE           --
        --                                        --
        --------------------------------------------
                  PRE-FILL SELECT's FAMILY
        --------------------------------------------
*/
$query = "SELECT
                [id],
                [name]
          FROM family
          ORDER BY [name]";

$families = $pdo -> query($query) -> fetchAll();


$values = [
        'plant-name' => '',
        'plant-link' => '',
        'plant-watering_frequency_spring_summer' => '0',
        'plant-watering_frequency_autumn_winter' => '0',
        'plant-sign_lack_water' => '',
        'plant-sign_excessive_water' => '',
        'plant-exposure' => '',
        'plant-temperature_min' => '0',
        'plant-temperature_max' => '0',
        'plant-sign_lack_light' => '',
        'plant-sign_excessive_light' => '',
        'plant-note' => '',
        'plant-family' => '',
];

$errors = [];


/*
        --------------------------------------------
                           FORM
        --------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($_POST) as $field) {
        $values[$field] = trim($_POST[$field]) ?? '';
    }

/*
        --------------------------------------------
                    INPUTS VALIDATION
        --------------------------------------------
*/

    // NAME
    if ($values['plant-name'] === '') {
        $errors["plant-name"] = "Le nom de la plante est obligatoire";
    } else if (strlen($values['plant-name']) > 50) {
        $errors["plant-name"] = "Le nom de la plante ne peut pas dépasser 50 caractères.";
    }

    // LINK (PICTURE)
    $link = null;
    $hasImage = !empty($_FILES['plant-link']['name']);

    if ($hasImage) {
        $extension = strtolower(pathinfo($_FILES['plant-link']['name'], PATHINFO_EXTENSION));

        if ($_FILES['plant-link']['error'] !== UPLOAD_ERR_OK) {
            $errors['plant-link'] = "L'envoi de l'image a échoué.";
        } else if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $errors['plant-link'] = "Problème de format de l'image. Formats acceptés : jpg, jpeg, png et webp";
        }
    }

    // WATERING FREQUENCY SPRING/SUMMER
    if ($values["plant-watering_frequency_spring_summer"] === '') {
        $errors["plant-watering_frequency_spring_summer"] = "La fréquence d'arrosage recommandée au printemps/été est obligatoire";
    }

    // WATERING FREQUENCY AUTUMN/WINTER
    if ($values["plant-watering_frequency_autumn_winter"] === '') {
        $errors["plant-watering_frequency_autumn_winter"] = "La fréquence d'arrosage recommandée en automne/hiver est obligatoire";
    }

    // FAMILY
    $fk_family = array_map('intval', array_column($families, 'id'));
    if ($values['plant-family'] === '') {
        $errors["plant-family"] = "La famille est obligatoire.";
    } else if (!in_array($values['plant-family'], $fk_family)) {
        $errors['plant-family'] = "La famille n'existe pas.";
    }


/*
        --------------------------------------------
                 IF !ERROR, INSERT INTO DB
        --------------------------------------------
*/

    if (!$errors) {

        // MOVE PICTURE TO UPLOADS FOLDER
        if ($hasImage) {
            $fileName = uniqid() . '.' . $extension;
            $destination = "uploads/" . $fileName;

            if (move_uploaded_file($_FILES['plant-link']['tmp_name'], $destination)) {
                $link = $destination;
            }
        }

        try {
            $sql = "INSERT INTO
                        plant ([name], [link], [watering_frequency_spring_summer], [watering_frequency_autumn_winter], [sign_lack_water], [sign_excessive_water], [exposure], [temperature_min], [temperature_max], [sign_lack_light], [sign_excessive_light], [note], [fk_family])
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo -> prepare($sql);
            $stmt -> execute([
                $values['plant-name'],
                $link,
                $values['plant-watering_frequency_spring_summer'],
                $values['plant-watering_frequency_autumn_winter'],
                $values['plant-sign_lack_water'] !== '' ? $values['plant-sign_lack_water'] : null,
                $values['plant-sign_excessive_water'] !== '' ? $values['plant-sign_excessive_water'] : null,
                $values['plant-exposure'] !== '' ? $values['plant-exposure'] : null,
                $values['plant-temperature_min'] !== '' ? $values['plant-temperature_min'] : null,
                $values['plant-temperature_max'] !== '' ? $values['plant-temperature_max'] : null,
                $values['plant-sign_lack_light'] !== '' ? $values['plant-sign_lack_light'] : null,
                $values['plant-sign_excessive_light'] !== '' ? $values['plant-sign_excessive_light'] : null,
                $values['plant-note'] !== '' ? $values['plant-note'] : null,
                (int) $values['plant-family']
            ]);

            $newPlantId = $pdo -> lastInsertId();

            header('Location: index.php?page=plant-details&id=' . $newPlantId);
            exit;
        } catch (PDOException $e) {
            $errors["global"] = "Une erreur est survenue lors de la création.";
        }
    }
}

?>

<style>
  form * {
    display: block;
    margin: 10px 0;
  }
</style>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->

<div class="form_style">
    <form method="post" enctype="multipart/form-data">
        <h1>Insertion d'une plante</h1> 
        <div>
            <label for="plant-name">Nom<span class="green_color">*</span> :</label>
            <input type="text" name="plant-name" id="plant-name" required value="<?= htmlspecialchars($values['plant-name']) ?>">
            <?php if (isset($errors['plant-name'])) : ?>
                <span class="error"><?= $errors['plant-name'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-link">Photo :</label>
            <input type="file" name="plant-link" id="plant-link" accept="image/*">
            <?php if (isset($errors['plant-link'])) : ?>
                <span class="error"><?= $errors['plant-link'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-watering_frequency_spring_summer">Fréquence d'arrosage<span class="green_color">*</span> (printemps/été) :</label>
            <input type="number" min="0" step="0.001" required name="plant-watering_frequency_spring_summer" id="plant-watering_frequency_spring_summer" value="<?= $values['plant-watering_frequency_spring_summer'] ?>">
            <?php if (isset($errors['plant-watering_frequency_spring_summer'])) : ?>
                <span class="error"><?= $errors['plant-watering_frequency_spring_summer'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-watering_frequency_autumn_winter">Fréquence d'arrosage<span class="green_color">*</span> (automne/hiver) :</label>
            <input type="number" min="0" step="0.001" required name="plant-watering_frequency_autumn_winter" id="plant-watering_frequency_autumn_winter" value="<?= $values['plant-watering_frequency_autumn_winter'] ?>">
            <?php if (isset($errors['plant-watering_frequency_autumn_winter'])) : ?>
                <span class="error"><?= $errors['plant-watering_frequency_autumn_winter'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-sign_lack_water">Signe d'un manque d'eau :</label>
            <input type="text" name="plant-sign_lack_water" id="plant-sign_lack_water" value="<?= htmlspecialchars($values['plant-sign_lack_water']) ?>">
            <?php if (isset($errors['plant-sign_lack_water'])) : ?>
                <span class="error"><?= $errors['plant-sign_lack_water'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-sign_excessive_water">Signe d'un excès d'eau :</label>
            <input type="text" name="plant-sign_excessive_water" id="plant-sign_excessive_water" value="<?= $values['plant-sign_excessive_water'] ?>">
            <?php if (isset($errors['plant-sign_excessive_water'])) : ?>
                <span class="error"><?= $errors['plant-sign_excessive_water'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-exposure">Exposition :</label>
            <input type="text" name="plant-exposure" id="plant-exposure" value="<?= $values['plant-exposure'] ?>">
            <?php if (isset($errors['plant-exposure'])) : ?>
                <span class="error"><?= $errors['plant-exposure'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-temperature_min">Température minimale :</label>
            <input type="number" name="plant-temperature_min" id="plant-temperature_min" value="<?= $values['plant-temperature_min'] ?>">
            <?php if (isset($errors['plant-temperature_min'])) : ?>
                <span class="error"><?= $errors['plant-temperature_min'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-temperature_max">Température maximale :</label>
            <input type="number" name="plant-temperature_max" id="plant-temperature_max" value="<?= $values['plant-temperature_max'] ?>">
            <?php if (isset($errors['plant-temperature_max'])) : ?>
                <span class="error"><?= $errors['plant-temperature_max'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-sign_lack_light">Signe d'un manque de luminosité :</label>
            <input type="text" name="plant-sign_lack_light" id="plant-sign_lack_light" value="<?= $values['plant-sign_lack_light'] ?>">
            <?php if (isset($errors['plant-sign_lack_light'])) : ?>
                <span class="error"><?= $errors['plant-sign_lack_light'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-sign_excessive_light">Signe d'un excès de luminosité :</label>
            <input type="text" name="plant-sign_excessive_light" id="plant-sign_excessive_light" value="<?= $values['plant-sign_excessive_light'] ?>">
            <?php if (isset($errors['plant-sign_excessive_light'])) : ?>
                <span class="error"><?= $errors['plant-sign_excessive_light'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-note">Note :</label>
            <textarea name="plant-note" id="plant-note"><?= $values['plant-note'] ?></textarea>
            <?php if (isset($errors['plant-note'])) : ?>
                <span class="error"><?= $errors['plant-note'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-family">Famille* :</label>
            <select name="plant-family" id="plant-family" required value="<?= $values['plant-family'] ?>">
                <option value=""> --- Sélectionner une famille --- </option>
                    <?php foreach ($families as $family) : ?>
                        <option value="<?= htmlspecialchars($family["id"]) ?>"
                        <?= $values["plant-family"] == $family["id"] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($family["name"]) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?php if (isset($errors['plant-family'])) : ?>
                    <span class="error"><?= $errors['plant-family'] ?></span>
                <?php endif ?>
        </div>

        <button class="btn">Créer une plante</button>
        <br>
        <p><span class="green_color">*</span> champs obligatoires</p>

    </form>
</div>