<?php

session_start();
$loggedIn = false;

if (isset($_SESSION["loggedin"])) {
    $loggedIn = $_SESSION["loggedin"];
}

?>

<nav class="nav">
    <ul class="nav-ul nav-left">
        <li><a href="/">Bloggest</a></li>
    </ul>
    <ul class="nav-ul nav-right">
        <li><a href="/randomPost">Random post</a></li>
        <?php if ($loggedIn) : ?>
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/logout">Sign out</a></li>
        <?php else : ?>
            <li><a href="/login">Log in</a></li>
            <li><a href="/signup">Sign up</a></li>
        <?php endif; ?>
    </ul>
</nav>