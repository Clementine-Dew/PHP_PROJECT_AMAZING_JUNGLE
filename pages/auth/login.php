<?php

/*
        --------------------------------------------
        --                                        --
        --               LOGIN PAGE               --
        --                                        --
        --------------------------------------------
*/

$errors = [];

$values = [ 
    'identification' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $values['identification'] = trim($_POST['identification']) ?? '';
    $password = trim($_POST["password"]);

/*
        --------------------------------------------
                        VALIDATION 
        --------------------------------------------
*/

    if ($values['identification'] === '') {
        $errors['identification'] =  "L'email ou l'alias est obligatoire.";
    }

    if ($password === '') {
        $errors['password'] = "Le mot de passe est obligatoire.";
    }

/*
        --------------------------------------------
                  IDENTIFICATION VERIFICATION 
        --------------------------------------------
*/

    if (!$errors) {

        $sql = "SELECT
                    [id],
                    [alias],
                    [email],
                    [password],
                    [role] 
                FROM 
                    consumer
                WHERE
                    [email] = ? OR [alias] = ?";

        $stmt = $pdo -> prepare($sql);
        $stmt -> execute([$values['identification'], $values['identification']]);

        $consumer = $stmt -> fetch();

        if (!$consumer || !password_verify($password, $consumer['password'])) {
            $errors['global'] = "Identifiant ou mot de passe incorrect.";
        }
        else {
            $_SESSION['user'] = [
                'id' => $consumer['id'],
                'alias' => $consumer['alias'],
                'email' => $consumer['email'],
                'role' => $consumer['role']
            ];

            header("Location: index.php?page=plants");
            exit;
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
    <form method="post">
        <h1>Se connecter</h1>
        <br>
        <?php if(isset($errors['global'])) : ?>
        <span class="error"><?= $errors['global'] ?></span>
        <?php endif ?>

        <div>
            <label for="identification">Alias ou email :</label>
            <input type="text" name="identification" id="identification" value="<?= $values['identification'] ?>">
            <?php if(isset($errors['identification'])) : ?>
            <span class="error"><?= $errors['identification'] ?></span>
            <?php endif ?>
        </div>


        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password">
            <?php if(isset($errors['password'])) : ?>
            <span class="error"><?= $errors['password'] ?></span>
            <?php endif ?>
        </div>

        <button class="btn">Se connecter</button>
    </form>
</div>
<br>
<br>
<p>Pas encore inscrit·e ? <a href="index.php?page=register">Inscris-toi !</a></p>