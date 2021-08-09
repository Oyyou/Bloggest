<?php
$foundUser = false;
$user = null;

$params = explode("/", $_SERVER["REQUEST_URI"]);
$username = end($params);

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
            <h2><?= $user->Username ?></h2>
        <?php else : ?>
            <h2>Nope</h2>
        <?php endif; ?>
    </div>
</div>

<?php include("components/footer.php"); ?>