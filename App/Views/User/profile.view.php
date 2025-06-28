<?php
use App\Core\LinkGenerator;

/** @var LinkGenerator $link */
?>

<div class="general-container">
    <h2>Profil</h2>
    <div
        id="profile-component"
        data-post="<?= $link->url("user.profile") ?>"
        data-get="<?= $link->url("user.getUser") ?>"></div>

    <script type="module" src="/public/js/profile.js"></script>
</div>