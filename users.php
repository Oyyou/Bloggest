<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include("database.php");
    $sql = "SELECT id, firstname, lastname, email FROM Person";
    $result = $conn->query($sql);
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    if ($result->num_rows > 0) {
    ?>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>FirstName</th>
                    <th>LastName</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php print $row["id"] ?></td>
                        <td><?php print $row["firstname"] ?></td>
                        <td><?php print $row["lastname"] ?></td>
                        <td><?php print $row["email"] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php
    }
    $conn->close();
    ?>

</body>

</html>