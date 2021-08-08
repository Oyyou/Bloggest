<?php
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$pageTitle = isset($pageTitle) ? $pageTitle : "Bloggest";
$pageDescription = isset($pageDescription) ? $pageDescription : "We have blogs";
$pageAuthor = isset($pageAuthor) ? $pageAuthor : "";
$pageType = isset($pageType) ? $pageType  : "website";
$additionalHeaders = isset($additionalHeaders) ? $additionalHeaders  : array();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDescription ?>" />
    <meta name="author" content="<?= $pageAuthor ?>">

    <meta property="og:url" content="<?= $actual_link ?>" />
    <meta property="og:type" content="<?= $pageType ?>" />
    <meta property="og:title" content="<?= $pageTitle ?>" />
    <meta property="og:description" content="<?= $pageDescription ?>" />
    <!--<meta property="og:image" content="" />-->

    <?php foreach ($additionalHeaders as $header) : ?>
        <?php if (!empty($header['content'])) : ?>
            <meta property="<?= $header['property'] ?>" content="<?= $header['content'] ?>" />
        <?php endif; ?>
    <?php endforeach; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="shortcut icon" type="image/jpg" href="/Favicon.ico" />
</head>

<body>