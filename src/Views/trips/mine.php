<?php

use App\Core\View;
use App\Models\Trip;

/** @var list<Trip> $trips */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0"><?= View::escape($title) ?></h1>
    <a href="/trajets/nouveau" class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Proposer un trajet</a>
</div>

<?php if ($trips === []): ?>
    <p class="text-muted">Vous n'avez encore proposé aucun trajet.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped app-table">
            <thead>
                <tr>
                    <th>Départ</th>
                    <th>Date de départ</th>
                    <th>Arrivée</th>
                    <th>Date d'arrivée</th>
                    <th>Places</th>
                    <th><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= View::escape($trip->agenceDepart) ?></td>
                        <td><?= $trip->dateDepart->format('d/m/Y à H\hi') ?></td>
                        <td><?= View::escape($trip->agenceArrivee) ?></td>
                        <td><?= $trip->dateArrivee->format('d/m/Y à H\hi') ?></td>
                        <td><?= $trip->placesDisponibles ?> / <?= $trip->placesTotal ?></td>
                        <td>
                            <a href="/trajets/<?= $trip->id ?>/modifier" class="btn-icon me-2" title="Modifier" aria-label="Modifier">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="post" action="/trajets/<?= $trip->id ?>/supprimer" class="d-inline"
                                  onsubmit="return confirm('Supprimer ce trajet ?');">
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
<?php endif; ?>
