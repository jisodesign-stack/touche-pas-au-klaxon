<?php

use App\Core\View;
use App\Models\Trip;
use App\Models\User;

/** @var list<Trip> $trips */
/** @var User|null $currentUser */
$format = static fn (DateTimeImmutable $date): string => $date->format('d/m/Y à H\hi');
?>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert"><?= View::escape($error) ?></div>
<?php elseif ($trips === []): ?>
    <p class="text-muted">Aucun trajet disponible pour le moment.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped app-table">
            <thead>
                <tr>
                    <th>Départ</th>
                    <th>Date de départ</th>
                    <th>Arrivée</th>
                    <th>Date d'arrivée</th>
                    <th>Places disponibles</th>
                    <?php if ($currentUser !== null): ?>
                        <th><span class="visually-hidden">Actions</span></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= View::escape($trip->agenceDepart) ?></td>
                        <td><?= $format($trip->dateDepart) ?></td>
                        <td><?= View::escape($trip->agenceArrivee) ?></td>
                        <td><?= $format($trip->dateArrivee) ?></td>
                        <td><?= $trip->placesDisponibles ?></td>
                        <?php if ($currentUser !== null): ?>
                            <td>
                                <button type="button" class="btn-icon me-2" title="Détails" aria-label="Détails"
                                        data-bs-toggle="modal" data-bs-target="#trip-modal-<?= $trip->id ?>">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <?php if ($trip->auteurId === $currentUser->id): ?>
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
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($currentUser === null): ?>
        <p class="text-muted"><a href="/connexion">Connectez-vous</a> pour consulter le détail des trajets et en proposer.</p>
    <?php else: ?>
        <?php foreach ($trips as $trip): ?>
            <div class="modal fade" id="trip-modal-<?= $trip->id ?>" tabindex="-1" aria-labelledby="trip-modal-title-<?= $trip->id ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="trip-modal-title-<?= $trip->id ?>">
                                <?= View::escape($trip->agenceDepart) ?> &rarr; <?= View::escape($trip->agenceArrivee) ?>
                            </h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Proposé par</dt>
                                <dd class="col-sm-7"><?= View::escape($trip->auteurNom) ?></dd>
                                <dt class="col-sm-5">Téléphone</dt>
                                <dd class="col-sm-7"><?= View::escape($trip->auteurTelephone) ?></dd>
                                <dt class="col-sm-5">Email</dt>
                                <dd class="col-sm-7"><?= View::escape($trip->auteurEmail) ?></dd>
                                <dt class="col-sm-5">Nombre total de places</dt>
                                <dd class="col-sm-7 mb-0"><?= $trip->placesTotal ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
