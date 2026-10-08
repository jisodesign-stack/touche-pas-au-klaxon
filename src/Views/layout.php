<?php use App\Core\Flash; use App\Core\View; use App\Security\Auth; $currentUser = Auth::user(); $flash = Flash::pull(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::escape($title ?? $appName) ?> - <?= View::escape($appName) ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= View::escape($appName) ?></a>
            <?php if ($currentUser !== null): ?>
                <form method="post" action="/deconnexion" class="d-flex align-items-center gap-3 m-0">
                    <?= View::csrfField() ?>
                    <a class="nav-link text-white-50" href="/mes-trajets">Mes trajets</a>
                    <span class="navbar-text"><?= View::escape($currentUser->fullName()) ?></span>
                    <button type="submit" class="btn btn-outline-light btn-sm">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="/connexion">Connexion</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container py-4">
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= View::escape($flash['type']) ?>" role="alert"><?= View::escape($flash['message']) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
    <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
