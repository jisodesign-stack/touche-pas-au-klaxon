<?php

use App\Core\View;
use App\Models\Trip;

/** @var list<Trip> $trips */
?>
<p><a href="/admin">&larr; Administration</a></p>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Départ</th><th>Date de départ</th><th>Arrivée</th><th>Date d'arrivée</th>
                <th class="text-center">Places</th><th>Auteur</th><th></th>
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
                    <td><?= View::escape($trip->auteurNom) ?></td>
                    <td class="text-end">
                        <form method="post" action="/admin/trajets/<?= $trip->id ?>/supprimer" class="d-inline"
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
