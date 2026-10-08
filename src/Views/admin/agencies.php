<?php

use App\Core\View;

/** @var list<array{id: int, nom: string, trajets: int}> $agencies */
?>
<p><a href="/admin"><i class="bi bi-arrow-left" aria-hidden="true"></i> Administration</a></p>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0"><?= View::escape($title) ?></h1>
    <a href="/admin/agences/nouveau" class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nouvelle agence</a>
</div>
<div class="table-responsive">
    <table class="table table-striped app-table">
        <thead>
            <tr><th>Nom</th><th>Trajets</th><th><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            <?php foreach ($agencies as $agency): ?>
                <tr>
                    <td><?= View::escape($agency['nom']) ?></td>
                    <td><?= $agency['trajets'] ?></td>
                    <td>
                        <a href="/admin/agences/<?= $agency['id'] ?>/modifier" class="btn-icon me-2" title="Modifier" aria-label="Modifier">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form method="post" action="/admin/agences/<?= $agency['id'] ?>/supprimer" class="d-inline"
                              onsubmit="return confirm('Supprimer cette agence ?');">
                            <?= View::csrfField() ?>
                            <button type="submit" class="btn-icon text-danger" title="Supprimer" aria-label="Supprimer">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
