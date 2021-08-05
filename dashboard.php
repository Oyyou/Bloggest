<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container dashboard-container">
    <?php if (isset($_SESSION["loggedin"])) : ?>
        <h1>
            Welcome to your dashboard, <?= $_SESSION["user"]; ?>.
        </h1>
        <h2>How can we help you today?</h2>
        <h3><a href="/posts/create">Create a post</a></h3>

        <div class="dashboard-body">
            <h4>Recent posts</h4>
            <div class="blog-list">
                <?php
                include("database.php");
                $conn = getConnection();
                $id = $_SESSION["id"];
                $sql = "SELECT id, userId, title, shortDescription, tags FROM Blogs where UserId=" . $id;
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                ?>
                    <div class="recent-blogs-table">
                        <p class="col-1"><b>Title</b></p>
                        <p class="col-2"><b>Action</b></p>

                        <?php while ($row = $result->fetch_assoc()) { ?>

                            <p class="col-1"><?php print $row["title"] ?></p>
                            <p class="col-2"><a href=<?= "/post/" . $row["id"] ?>>View</a> | <a href=<?= "/posts/edit/" . $row["id"] ?>>Edit</a></p>
                        <?php } ?>
                    </div>
                <?php
                }
                $conn->close();
                ?>
            </div>
        </div>
    <?php else : ?>
        <h1>You shouldn't be here =)</h1>
    <?php endif; ?>

</div>
<?php include("components/footer.php"); ?>