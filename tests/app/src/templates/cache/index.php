<?php
/** @var $id */
/** @var $timestamp */

?>
<h1>Cache test</h1>
<div>timestamp: <?= $timestamp ?></div>
<div>id: <?= $id ?></div>

<form method="post" action="/cache/remove">
    <input type="hidden" name="id" value="<?= $id ?>" />
    <button type="submit">Remove from cache</button>
</form>

<form method="post" action="/cache/remove-all">
    <input type="hidden" name="id" value="<?= $id ?>" />
    <button type="submit">Remove all from cache</button>
</form>