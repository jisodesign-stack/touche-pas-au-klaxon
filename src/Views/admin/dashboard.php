<?php use App\Core\View; ?>
<h1 class="h3 mb-4"><?= View::escape($title) ?></h1>
<div class="row g-3">
    <div class="col-md-4">
        <a href="/admin/utilisateurs" class="card text-decoration-none h-100">
            <div class="card-body">
                <div class="display-6"><?= (int) $users ?></div>
                <div class="text-muted">Utilisateurs</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="/admin/agences" class="card text-decoration-none h-100">
            <div class="card-body">
                <div class="display-6"><?= (int) $agencies ?></div>
                <div class="text-muted">Agences</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="/admin/trajets" class="card text-decoration-none h-100">
            <div class="card-body">
                <div class="display-6"><?= (int) $trips ?></div>
                <div class="text-muted">Trajets</div>
            </div>
        </a>
    </div>
</div>
