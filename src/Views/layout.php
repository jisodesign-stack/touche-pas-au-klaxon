<?php use App\Core\Flash; use App\Core\View; use App\Security\Auth; $currentUser = Auth::user(); $flash = Flash::pull(); ?>
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
            <a class="navbar-brand fw-bold text-primary" href="/">
                <i class="bi bi-car-front-fill" aria-hidden="true"></i> <?= View::escape($appName) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Afficher le menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <?php if ($currentUser !== null): ?>
                    <ul class="navbar-nav me-auto text-nowrap">
                        <li class="nav-item">
                            <a class="nav-link" href="/mes-trajets"><i class="bi bi-signpost-2" aria-hidden="true"></i> Mes trajets</a>
                        </li>
                        <?php if ($currentUser->isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin"><i class="bi bi-gear" aria-hidden="true"></i> Administration</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <form method="post" action="/deconnexion" class="d-flex align-items-center gap-3 m-0">
                        <?= View::csrfField() ?>
                        <span class="navbar-text text-body text-nowrap"><i class="bi bi-person-circle" aria-hidden="true"></i> <?= View::escape($currentUser->fullName()) ?></span>
                        <button type="submit" class="btn btn-outline-dark btn-sm">Déconnexion</button>
                    </form>
                <?php else: ?>
                    <div class="ms-auto">
                        <a class="btn btn-primary btn-sm" href="/connexion">Connexion</a>
                    </div>
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
        &copy; <?= date('Y') ?> <?= View::escape($appName) ?> &middot; Covoiturage inter-sites
    </footer>
    <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
