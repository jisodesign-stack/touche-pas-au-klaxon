<?php use App\Core\View; ?>
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
        </div>
    </nav>
    <main class="container py-4">
        <?= $content ?>
    </main>
    <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
