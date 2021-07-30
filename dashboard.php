<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container dashboard-container">
    <?php if (isset($_SESSION["loggedin"])) : ?>
        <h1>
            Welcome to your dashboard, <?= $_SESSION["user"]; ?>.
        </h1>
        <h2>How can we help you today?</h2>
        <h3><a href="/blogs/create">Create a blog</a></h3>

        <p>Recent blogs</p>
        <div class="blog-list">
            <?php
            include("database.php");
            $id = $_SESSION["id"];
            $sql = "SELECT id, userId, title, subTitle, body, tags FROM Blogs where UserId=" . $id;
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
            ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>SubTitle</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php print $row["title"] ?></td>
                                <td><?php print $row["subTitle"] ?></td>
                                <td><a href=<?= "/blogs/edit/" . $row["id"] ?>>Edit</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php
            }
            $conn->close();
            ?>
        </div>
    <?php else : ?>
        <h1>You shouldn't be here =)</h1>
    <?php endif; ?>

</div>
<?php include("components/footer.php"); ?>