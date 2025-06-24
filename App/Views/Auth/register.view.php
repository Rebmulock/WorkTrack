<?php
use App\Core\LinkGenerator;

/** @var LinkGenerator $link */
?>

<div class="register-container">
    <div id="register-component" data-url="<?= $link->url("register") ?>"></div>

    <script type="module" src="/public/js/register.js"></script>
</div>