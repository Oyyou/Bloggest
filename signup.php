<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>

<div class="container table-container">
    <form method="post" class="form-login">
        <table>
            <tr>
                <td><label for="username">Username:</label></td>
                <td><input type="text" id="username" name="username" required="required"></td>
            </tr>
            <tr>

            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" id="email" name="email" required="required"></td>
            </tr>

            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" required="required"></td>
            </tr>

            <tr>
                <td><input class="button" type="submit" name="submit" value="Sign up"></td>
            </tr>
        </table>
    </form>

    <?php if (isset($_GET['status']) and $_GET['status'] == 'success') : ?>
        <p>Sign up successful! <a href="/login">Log in</a></p>
    <?php elseif (isset($_GET['status']) and $_GET['status'] == 'fail') : ?>
        <p>Issue creating profile. Try again</p>
    <?php endif; ?>
</div>
<?php include("components/footer.php"); ?>

<?php
extract($_POST);
if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])) {

    include("database.php");

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $emailUser = getUserbyName($conn, $username);
    $emailEmail = getUserbyEmail($conn, $email);

    //var_dump($emailUser);
    //var_dump($emailEmail);

    if (!empty($emailEmail)) {
        echo "Email exists!";
        exit;
    } else if (!empty($emailUser)) {
        echo "Username exists!";
        exit;
    } else if (isset($_POST['submit'])) {
        if (addNewUser($conn, $username, $email, $password)) {
            header('location:signup?status=success');
        } else {
            header('location:signup?status=fail');
        }
    }
    $conn->close();
}
?>