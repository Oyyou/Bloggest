<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container table-container">
    <form method="post" class="form-login">
        <table>
            <tbody>
                <tr>
                    <td><label for="email">Email</label></td>
                    <td><input type="email" id="email" name="email"></td>
                </tr>
                <tr>
                    <td class="flex-row"><label for="password">Password</label><a href="/passwordReset">Forgot password?</a></td>
                    <td><input type="password" id="password" name="password"></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Log in"></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php include("components/footer.php"); ?>

<?php
extract($_POST);
if (isset($_POST["email"]) && isset($_POST["password"])) {

    include("database.php");
    $conn = getConnection();

    $email = $_POST["email"];
    $password = $_POST["password"];
    $user = $_POST["password"];
    $login_err = "";

    $sql = "SELECT id, username, password FROM Users where Email=?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $email);

        // Set parameters
        $param_username = $email;
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if username exists, if yes then verify password
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $id, $user, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, so start a new session

                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["user"] = $user;

                        // Redirect user to welcome page
                        header("location: dashboard");
                    } else {
                        // Password is not valid, display a generic error message
                        $login_err = "Invalid username or password.";
                    }
                }
            } else {
                // Username doesn't exist, display a generic error message
                $login_err = "Invalid username or password.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
    $conn->close();

    if(!empty($login_err)) {
        echo $login_err;
    }
}
?>