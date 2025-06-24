<?php

/** @var LinkGenerator $link
 *  @var IAuthenticator $auth
 *  */

use App\Core\LinkGenerator;
use App\Core\IAuthenticator;

?>

<nav class="navbar">
    <a class="title" href="<?= $link->url("home.index") ?>">WorkTrack</a>

    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

            <li>
                <a class="nav-link" href="<?= $link->url("home.index") ?>">Domov</a>
            </li>

            <?php if ($auth->isLogged()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url("auth.logout") ?>">Odhlásiť sa</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url("auth.login") ?>">Prihlásiť sa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url("auth.register") ?>">Registrovať sa</a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</nav>
