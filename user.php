<?php
$foundUser = false;
$user = null;


$params = explode("/", $_SERVER["REQUEST_URI"]);
$username = end($params);
//var_dump($blodId);
//var_dump($_GET);
//var_dump($_SERVER);
//var_dump($_REQUEST);

if ($username) {
    include("database.php");
    $conn = getConnection();
    $user = getUserbyName($conn, $username);

    if ($user) {
        $foundUser = true;
    }


    $conn->close();
}
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container">
    <div class="user-container">
        <?php if ($foundUser) : ?>
            <h1><?= $user->Username ?></h1>
        <?php else : ?>
            <h1>Nope</h1>
        <?php endif; ?>
    </div>
</div>

<?php include("components/footer.php"); ?>