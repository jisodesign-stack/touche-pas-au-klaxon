<?php

use App\Core\Flash;
use App\Core\View;
use App\Security\Auth;

$currentUser = Auth::user();
$flash = Flash::pull();
$isAdmin = $currentUser !== null && $currentUser->isAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::escape($title ?? $appName) ?> - <?= View::escape($appName) ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <header class="container pt-3">
        <nav class="navbar navbar-expand-md app-navbar px-3">
            <a class="navbar-brand fw-bold text-primary" href="<?= $isAdmin ? '/admin' : '/' ?>">
                <i class="bi bi-car-front-fill" aria-hidden="true"></i> <?= View::escape($appName) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Afficher le menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNav">
                <?php if ($currentUser === null): ?>
                    <a class="btn btn-primary btn-sm" href="/connexion">Connexion</a>
                <?php elseif ($isAdmin): ?>
                    <form method="post" action="/deconnexion" class="d-flex flex-column flex-md-row align-items-md-center gap-2 gap-md-3 m-0">
                        <?= View::csrfField() ?>
                        <ul class="navbar-nav flex-md-row text-nowrap mb-0">
                            <li class="nav-item"><a class="nav-link" href="/admin/utilisateurs"><i class="bi bi-people" aria-hidden="true"></i> Utilisateurs</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/agences"><i class="bi bi-buildings" aria-hidden="true"></i> Agences</a></li>
                            <li class="nav-item"><a class="nav-link" href="/admin/trajets"><i class="bi bi-signpost-2" aria-hidden="true"></i> Trajets</a></li>
                        </ul>
                        <button type="submit" class="btn btn-outline-dark btn-sm">Déconnexion</button>
                    </form>
                <?php else: ?>
                    <form method="post" action="/deconnexion" class="d-flex flex-column flex-md-row align-items-md-center gap-2 gap-md-3 m-0">
                        <?= View::csrfField() ?>
                        <a class="btn btn-primary btn-sm" href="/trajets/nouveau"><i class="bi bi-plus-lg" aria-hidden="true"></i> Proposer un trajet</a>
                        <span class="navbar-text text-body text-nowrap"><i class="bi bi-person-circle" aria-hidden="true"></i> <?= View::escape($currentUser->fullName()) ?></span>
                        <button type="submit" class="btn btn-outline-dark btn-sm">Déconnexion</button>
                    </form>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container py-4 flex-grow-1">
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= View::escape($flash['type']) ?>" role="alert"><?= View::escape($flash['message']) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
    <footer class="container app-footer py-3 text-center small">
        &copy; <?= date('Y') ?> <?= View::escape($appName) ?>
    </footer>
    <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
