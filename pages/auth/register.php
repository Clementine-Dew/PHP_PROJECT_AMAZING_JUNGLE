<?php

/*
        --------------------------------------------
        --                                        --
        --              REGISTER PAGE             --
        --                                        --
        --------------------------------------------
*/

$errors = [];

$values = [ 
    'lastname' => '',
    'firstname' => '',
    'alias' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $values['lastname'] = trim($_POST['lastname']) ?? '';
    $values['firstname'] = trim($_POST['firstname']) ?? '';
    $values['alias'] = trim($_POST['alias']) ?? '';
    $values['email'] = trim($_POST['email']) ?? '';
    $password = trim($_POST["password"]);
    $confirmation = trim($_POST["confirmation"]);

/*
        --------------------------------------------
                        VALIDATION 
        --------------------------------------------
*/

// FIRSTNAME

    if($values['firstname'] === '') {
        $errors['firstname'] = "Le prénom est obligatoire";
    }

// ALIAS

    if($values['alias'] === '') {
        $errors['alias'] = "L'alias est obligatoire";
    }

// EMAIL

    if ($values['email'] === '') {
        $errors['email'] =  "L'email est obligatoire.";
    }
    else if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Le format de l'email est incorrect.";
    }
    else if (strlen($values['email']) > 180) {
        $errors['email'] = "L'email ne peut pas excéder 180 caractères.";
    }

// PASSWORD

    if ($password === '') {
        $errors['password'] = "Le mot de passe est obligatoire.";
    }
    else if (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir 8 caractères minimum.";
    }

    if ($password !== $confirmation) {
        $errors['confirmation'] = "Les deux saisies du mot de passe ne correspondent pas.";
    }


/*
        --------------------------------------------
                       SAVE DATA IN DB 
        --------------------------------------------
*/

    if (!$errors) {

        try {
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO consumer ([lastname], [firstname], [alias], [email], [password])
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo -> prepare($sql);
        $stmt -> execute([
            $values['lastname'],
            $values['firstname'],
            $values['alias'],
            $values['email'],
            $password_hash
        ]);

        $_SESSION['user'] = [
            'id' => $pdo -> lastInsertId(),
            'email' => $values['email'],
            'role' => 'user'
        ] ;     

        header("Location: index.php");
        exit;
        } 
        catch (PDOException $e) {
            $errors['email'] = "L'email est déjà pris.";
        }
    }
}

?>

<!-- 
        --------------------------------------------
                        TEMPLATE 
        --------------------------------------------
-->


<br>
<br>
<div class="form_style">
    <form method="POST">
        <h1>S'inscrire</h1>
        <br>
        <div>
            <label for="lastname">Nom :</label>
            <input type="text" name="lastname" id="lastname" value="<?= $values['lastname'] ?>">
        </div>


        <div>
            <label for="firstname">Prénom<span class="green_color">*</span> :</label>
            <input type="text" name="firstname" id="firstname" value="<?= $values['firstname'] ?>" required>
            <?php if(isset($errors['firstname'])) : ?>
                <span class="error"><?= $errors['firstname'] ?></span>
            <?php endif ?>
        </div>


        <div>
            <label for="alias">Alias<span class="green_color">*</span> :</label>
            <input type="text" name="alias" id="alias" value="<?= $values['alias'] ?>" required>
            <?php if(isset($errors['alias'])) : ?>
                <span class="error"><?= $errors['alias'] ?></span>
            <?php endif ?>
        </div>


        <div>
            <label for="email">Email<span class="green_color">*</span> :</label>
            <input type="email" name="email" id="email" value="<?= $values['email'] ?>" required>
            <?php if(isset($errors['email'])) : ?>
                <span class="error"><?= $errors['email'] ?></span>
            <?php endif ?>
        </div>


        <div>
            <label for="password">Mot de passe<span class="green_color">*</span> :</label>
            <input type="password" name="password" id="password" minlength="8" required>
            <?php if(isset($errors['password'])) : ?>
                <span class="error"><?= $errors['password'] ?></span>
            <?php endif ?>
        </div>


        <div>
            <label for="confirmation">Confirmation<span class="green_color">*</span> :</label>
            <input type="password" name="confirmation" id="confirmation" minlength="8" required>
            <?php if(isset($errors['confirmation'])) : ?>
                <span class="error"><?= $errors['confirmation'] ?></span>
            <?php endif ?>
        </div>

        <button class="btn">S'inscrire</button>
        <br>
        <p><span class="green_color">*</span> champs obligatoires</p>
    </form>
</div>
<br>
<br>
<p>Déjà inscrit·e ? <a href="index.php?page=login">Connecte-toi !</a></p>
