<?php

use App\Core\View;
use App\Models\Trip;

/** @var list<Trip> $trips */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0"><?= View::escape($title) ?></h1>
    <a href="/trajets/nouveau" class="btn btn-primary">Proposer un trajet</a>
</div>

<?php if ($trips === []): ?>
    <p class="text-muted">Vous n'avez encore proposé aucun trajet.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Départ</th>
                    <th>Date de départ</th>
                    <th>Arrivée</th>
                    <th>Date d'arrivée</th>
                    <th class="text-center">Places</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= View::escape($trip->agenceDepart) ?></td>
                        <td><?= $trip->dateDepart->format('d/m/Y à H\hi') ?></td>
                        <td><?= View::escape($trip->agenceArrivee) ?></td>
                        <td><?= $trip->dateArrivee->format('d/m/Y à H\hi') ?></td>
                        <td class="text-center"><?= $trip->placesDisponibles ?> / <?= $trip->placesTotal ?></td>
                        <td class="text-end">
                            <a href="/trajets/<?= $trip->id ?>/modifier" class="btn btn-outline-secondary btn-sm">Modifier</a>
                            <form method="post" action="/trajets/<?= $trip->id ?>/supprimer" class="d-inline"
                                  onsubmit="return confirm('Supprimer ce trajet ?');">
                                <?= View::csrfField() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
