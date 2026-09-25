<?php

/*
        --------------------------------------------
        --                                        --
        --             EDIT PLANT PAGE            --
        --                                        --
        --------------------------------------------
*/

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

/*
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

$stmt = $pdo -> prepare("SELECT 
                                [id],
                                [name],
                                [link],
                                [watering_frequency_spring_summer], 
                                [watering_frequency_autumn_winter],[sign_lack_water], 
                                [sign_excessive_water],
                                [exposure],
                                [temperature_min],
                                [temperature_max],
                                [sign_lack_light],
                                [sign_excessive_light],
                                [note],
                                [fk_family]
                        FROM plant 
                        WHERE id = ?");
$stmt -> execute([$id]);
$plant = $stmt -> fetch();

if (!$plant) {
    header("Location: index.php?page=plants");
}

$values = [
    'plant-name' => $plant['name'],
    'plant-link' => $plant['link'],
    'plant-watering_frequency_spring_summer' => $plant['watering_frequency_spring_summer'],
    'plant-watering_frequency_autumn_winter' => $plant['watering_frequency_autumn_winter'],
    'plant-sign_lack_water' => $plant['sign_lack_water'],
    'plant-sign_excessive_water' => $plant['sign_excessive_water'],
    'plant-exposure' => $plant['exposure'],
    'plant-temperature_min' => $plant['temperature_min'],
    'plant-temperature_max' => $plant['temperature_max'],
    'plant-sign_lack_light' => $plant['sign_lack_light'],
    'plant-sign_excessive_light' => $plant['sign_excessive_light'],
    'plant-note' => $plant['note'],
    'plant-family' => $plant['fk_family'],
];

$errors = [];

/*
        --------------------------------------------
                    INPUTS VALIDATION
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

        try {
            $sql = " UPDATE plant
                     SET
                        [name] = ?,
                        [link] = ?,
                        [watering_frequency_spring_summer] = ?,
                        [watering_frequency_autumn_winter] = ?,
                        [sign_lack_water] = ?,
                        [sign_excessive_water] = ?,
                        [exposure] = ?,
                        [temperature_min] = ?,
                        [temperature_max] = ?,
                        [sign_lack_light] = ?,
                        [sign_excessive_light] = ?,
                        [note] = ?,
                        [fk_family] = ?
                     WHERE id = ?";

            $stmt = $pdo -> prepare($sql);
            $stmt -> execute([
                $values['plant-name'],
                $values['plant-link'] !== '' ? $values['plant-link'] : null,
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
                (int) $values['plant-family'],
                $id
            ]);

            
            header('Location: index.php?page=plant-details&id=' . $id);
            exit;
        
        } catch (PDOException $e) {
            $errors["global"] = "Une erreur est survenue lors de la modification.";
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
    <h1>Modification d'une plante</h1>
        <div>
            <label for="plant-name">Nom<span class="green_color">*</span> :</label>
            <input type="text" name="plant-name" id="plant-name" required value="<?= htmlspecialchars($values['plant-name']) ?>">
            <?php if (isset($errors['plant-name'])) : ?>
                <span class="error"><?= $errors['plant-name'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-link">Photo :</label>
            <?php if (!empty($values['plant-link'])) : ?>
                <img src="<?= htmlspecialchars($values['plant-link']) ?>" alt="Photo actuelle de la plante" style="max-height:100px;">
            <?php endif ?>
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
            <textarea name="plant-note" id="plant-note" rows="12"><?= $values['plant-note'] ?></textarea>
            <!-- <input type="text" name="plant-note" id="plant-note" value="<?= $values['plant-note'] ?>"> -->
            <?php if (isset($errors['plant-note'])) : ?>
                <span class="error"><?= $errors['plant-note'] ?></span>
            <?php endif ?>
        </div>

        <div>
            <label for="plant-family">Familles<span class="green_color">*</span> :</label>
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

        <button class="btn">Modifier la plante</button>
        <br>
        <p><span class="green_color">*</span> champs obligatoires</p>
    </form>
</div>