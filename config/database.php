<?php

/*
        --------------------------------------------
        --                                        --
        --           LOGIN INFORMATIONS           --
        --                                        --
        --------------------------------------------
*/

$source = "sqlsrv";
$host = "LAPTOP-CLEMY\SQL_COURS_CLEM";
// $host = "WAD-12\IF3";
$dbname = "amazing_jungle";

$dsn = "$source:Server=$host;Database=$dbname;TrustServerCertificate=true";
$user = "demo_user";
$pass = "Test1234=";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

/*
        --------------------------------------------
                       LOGIN ATTEMPT
        --------------------------------------------
*/

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (PDOException $e) {
    die("Erreur de connexion : " . $e -> getMessage());
}