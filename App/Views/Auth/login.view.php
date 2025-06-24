<?php
use App\Core\LinkGenerator;

/** @var LinkGenerator $link */
?>


<div class="auth-container">
    <div id="login-component" data-url="<?= $link->url("login") ?>"></div>

    <script type="module" src="/public/js/login.js"></script>
</div>
