<?php

use App\Core\View;
use App\Models\Trip;
use App\Security\Auth;

/** @var list<Trip> $trips */
$isLogged = Auth::check();
?>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert"><?= View::escape($error) ?></div>
<?php elseif ($trips === []): ?>
    <p class="text-muted">Aucun trajet disponible pour le moment.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Départ</th>
                    <th>Date et heure de départ</th>
                    <th>Arrivée</th>
                    <th>Date et heure d'arrivée</th>
                    <th class="text-center">Places</th>
                    <?php if ($isLogged): ?>
                        <th>Contact</th>
                    <?php endif; ?>
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
                        <?php if ($isLogged): ?>
                            <td>
                                <?= View::escape($trip->auteurNom) ?><br>
                                <small><?= View::escape($trip->auteurTelephone) ?> · <?= View::escape($trip->auteurEmail) ?></small>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$isLogged): ?>
        <p class="text-muted"><a href="/connexion">Connectez-vous</a> pour voir les coordonnées des conducteurs.</p>
    <?php endif; ?>
<?php endif; ?>
