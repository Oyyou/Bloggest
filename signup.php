<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>

<div class="container table-container">
    <form method="post" class="form-login">
        <?php
        $rand = rand();
        $_SESSION['rand'] = $rand;
        ?>
        <input type="hidden" value="<?php echo $rand; ?>" name="randcheck" />
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

    $sql1 = mysqli_query($conn, "SELECT * FROM Users where Email='$email'");
    $sql2 = mysqli_query($conn, "SELECT * FROM Users where username='$username'");
    if (mysqli_num_rows($sql1) > 0) {
        echo "Email exists!";
        exit;
    } else if (mysqli_num_rows($sql2) > 0) {
        echo "Username exists!";
        exit;
    } else if (isset($_POST['submit'])) {

        $stmt = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        $stmt->execute();
        header('location:signup?status=success');
    }
    $conn->close();
}
?>