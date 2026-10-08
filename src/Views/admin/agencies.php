<?php

use App\Core\View;

/** @var list<array{id: int, nom: string, trajets: int}> $agencies */
?>
<p><a href="/admin">&larr; Administration</a></p>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0"><?= View::escape($title) ?></h1>
    <a href="/admin/agences/nouveau" class="btn btn-primary">Nouvelle agence</a>
</div>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr><th>Nom</th><th class="text-center">Trajets</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($agencies as $agency): ?>
                <tr>
                    <td><?= View::escape($agency['nom']) ?></td>
                    <td class="text-center"><?= $agency['trajets'] ?></td>
                    <td class="text-end">
                        <a href="/admin/agences/<?= $agency['id'] ?>/modifier" class="btn btn-outline-secondary btn-sm">Modifier</a>
                        <form method="post" action="/admin/agences/<?= $agency['id'] ?>/supprimer" class="d-inline"
                              onsubmit="return confirm('Supprimer cette agence ?');">
                            <?= View::csrfField() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
