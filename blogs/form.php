<?php if (isset($blogTitle)) : ?>
    <h1 class="blog-title">
        <?= $blogTitle ?>
    </h1>
<?php endif; ?>
<form method="post" class="blog-form">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required="required" value="<?= (isset($title) ? $title : "") ?>">

    <label for="subtitle">Subtitle:</label>
    <input type="text" id="subtitle" name="subtitle" value="<?= (isset($subTitle) ? $subTitle : "") ?>">

    <div>
        <label class="full-row" for="body">Body:</label>
        <textarea class="full-row" id="body" name="body"><?= (isset($body) ? $body : "") ?></textarea>
    </div>

    <label for="tags">Tags (split by comma):</label>
    <input type="text" id="tags" name="tags" value="<?= (isset($tags) ? $tags : "") ?>" ?>

    <input class="button" type="submit" name="submit" value="Save">

</form>