<?php

use App\Core\View;
use App\Models\Trip;

/** @var list<Trip> $trips */
?>
<p><a href="/admin"><i class="bi bi-arrow-left" aria-hidden="true"></i> Administration</a></p>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>
<div class="table-responsive">
    <table class="table table-striped app-table">
        <thead>
            <tr>
                <th>Départ</th><th>Date de départ</th><th>Arrivée</th><th>Date d'arrivée</th>
                <th>Places</th><th>Auteur</th><th><span class="visually-hidden">Actions</span></th>
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
                    <td><?= View::escape($trip->auteurNom) ?></td>
                    <td>
                        <form method="post" action="/admin/trajets/<?= $trip->id ?>/supprimer" class="d-inline"
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
