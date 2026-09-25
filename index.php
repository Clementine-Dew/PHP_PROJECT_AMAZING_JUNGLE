<?php

session_start();

/*
        --------------------------------------------
        --                                        --
        --            PATHS MANAGEMENT            --
        --                                        --
        --------------------------------------------
*/

$paths = [

    '' => [
        'file' => 'pages/home.php',
        'title' => 'HOMEPAGE'
    ],

/*
        --------------------------------------------
                     PLANTS MANAGEMENT
        --------------------------------------------
*/

    'plants' => [
        'file' => 'pages/plants/read.php',
        'title' => 'PLANTS\' LIST',
        'roles' => ['user', 'admin'],
    ],

    'plant-details' => [
        'file' => 'pages/plants/details.php',
        'title' => 'PLANT\'S DETAILS',
        'roles' => ['user', 'admin'],
    ],

    'plant-create' => [
        'file' => 'pages/plants/create.php',
        'title' => 'ADD A PLANT',
        'roles' => ['admin'],
    ],

    'plant-delete' => [
        'file' => 'pages/plants/delete.php',
        'title' => 'REMOVE A PLANT',
        'roles' => ['admin'],
    ],

    'plant-edit' => [
        'file' => 'pages/plants/edit.php',
        'title' => 'EDIT A PLANT',
        'roles' => ['admin'],
    ],

/*
        --------------------------------------------
                        MY JUNGLE 
        --------------------------------------------
*/

    'families' => [
        'file' => 'pages/families/read.php',
        'title' => 'FAMILIES\' LIST',
        'roles' => ['user', 'admin'],
    ],

    'family-details' => [
        'file' => 'pages/families/details.php',
        'title' => 'FAMILIES\' DETAILS',
        'roles' => ['user', 'admin'],
    ],

/*
        --------------------------------------------
                        MY JUNGLE 
        --------------------------------------------
*/

    'jungle' => [
            'file' => 'pages/jungle/read.php',
            'title' => 'MY JUNGLE',
            'roles' => ['user', 'admin'],
    ],

    'add-to-jungle' => [
        'file' => 'pages/jungle/create.php',
        'title' => 'ADD A PLANT',
        'roles' => ['user', 'admin'],
    ],

/*
        --------------------------------------------
                     AUTHENTIFICATION 
        --------------------------------------------
*/

    'register' => [
        'file' => 'pages/auth/register.php',
        'title' => 'REGISTER',
    ],

    'login' => [
        'file' => 'pages/auth/login.php',
        'title' => 'LOGIN',
    ],

    'logout' => [
        'file' => 'pages/auth/logout.php',
        'title' => 'LOGOUT',
    ],

];


/*
        --------------------------------------------
        --                                        --
        --              VERIFICATION              --
        --                                        --
        --------------------------------------------
                     PATH VERIFICATION
        --------------------------------------------
*/

$page = $_GET['page'] ?? '';
$path = $paths[$page] ?? null;

if ($path === null) {
    $path = [
        'file' => 'pages/errors/not_found.php',
        'title' => "404 NOT FOUND",
    ];
}


/*
        --------------------------------------------
                     ROLES VERIFICATION
        --------------------------------------------
*/

$requiredRoles = $path['roles'] ?? null;

if ($requiredRoles !== null) {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?page=login");
        exit;
    }

    if (!in_array($_SESSION['user']['role'], $requiredRoles)) {
        $path = [
            'file' => 'pages/errors/forbidden.php',
            'title' => "403 FORBIDDEN",
        ];
    }
}

$file = $path["file"];
$title = $path["title"];

require_once 'config/database.php';


/*
        --------------------------------------------
        --                                        --
        --             ASSEMBLE PAGES             --
        --                                        --
        --------------------------------------------
*/

ob_start();
require_once $file;
$content = ob_get_clean();

require_once 'partials/header.php';
echo $content;
require_once 'partials/footer.php';