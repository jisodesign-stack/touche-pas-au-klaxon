<?php

use App\Core\View;
use App\Models\User;

/** @var list<User> $users */
?>
<p><a href="/admin"><i class="bi bi-arrow-left" aria-hidden="true"></i> Administration</a></p>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>
<div class="table-responsive">
    <table class="table table-striped app-table">
        <thead>
            <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= View::escape($user->fullName()) ?></td>
                    <td><?= View::escape($user->email) ?></td>
                    <td><?= View::escape($user->telephone) ?></td>
                    <td><span class="badge text-bg-<?= $user->isAdmin() ? 'danger' : 'secondary' ?>"><?= View::escape($user->role) ?></span></td>
                    <td>
                        <?php if ($user->id !== $currentId): ?>
                            <form method="post" action="/admin/utilisateurs/<?= $user->id ?>/supprimer" class="d-inline"
                                  onsubmit="return confirm('Supprimer cet utilisateur et tous ses trajets ?');">
                                <?= View::csrfField() ?>
                                <button type="submit" class="btn-icon text-danger" title="Supprimer" aria-label="Supprimer">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
