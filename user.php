<?php
$user = null;
$author = null;

$params = explode("/", $_SERVER["REQUEST_URI"]);
$username = end($params);
//var_dump($blodId);
//var_dump($_GET);
//var_dump($_SERVER);
//var_dump($_REQUEST);

if ($username) {
    include("database.php");
    $user = getUserbyName($conn, $username);

    $conn->close();
}
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container">
    <div class="user-container">
        <?php if (!$user) : ?>
            <h1><?= $user->title ?></h1>
        <?php else : ?>
            <h1>Nope</h1>
        <?php endif; ?>
    </div>
</div>

<?php include("components/footer.php"); ?>