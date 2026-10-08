<?php

use App\Core\View;

/** @var list<array{id: int, nom: string}> $agencies */
/** @var array<string, string> $values */
/** @var array<string, string> $errors */
$value = static fn (string $key): string => View::escape($values[$key] ?? '');
$invalid = static fn (string $key): string => isset($errors[$key]) ? ' is-invalid' : '';
$error = static fn (string $key): string => isset($errors[$key])
    ? '<div class="invalid-feedback">' . View::escape($errors[$key]) . '</div>'
    : '';
$agencyOptions = static function (string $key) use ($agencies, $values): string {
    $html = '<option value="">Choisir…</option>';
    foreach ($agencies as $agency) {
        $selected = ($values[$key] ?? '') === (string) $agency['id'] ? ' selected' : '';
        $html .= '<option value="' . $agency['id'] . '"' . $selected . '>' . View::escape($agency['nom']) . '</option>';
    }

    return $html;
};
?>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>
<form method="post" action="<?= View::escape($action) ?>" class="row g-3" novalidate>
    <?= View::csrfField() ?>
    <div class="col-md-6">
        <label for="agence_depart_id" class="form-label">Agence de départ</label>
        <select class="form-select<?= $invalid('agence_depart_id') ?>" id="agence_depart_id" name="agence_depart_id" required>
            <?= $agencyOptions('agence_depart_id') ?>
        </select>
        <?= $error('agence_depart_id') ?>
    </div>
    <div class="col-md-6">
        <label for="agence_arrivee_id" class="form-label">Agence d'arrivée</label>
        <select class="form-select<?= $invalid('agence_arrivee_id') ?>" id="agence_arrivee_id" name="agence_arrivee_id" required>
            <?= $agencyOptions('agence_arrivee_id') ?>
        </select>
        <?= $error('agence_arrivee_id') ?>
    </div>
    <div class="col-md-6">
        <label for="date_depart" class="form-label">Date et heure de départ</label>
        <input type="datetime-local" class="form-control<?= $invalid('date_depart') ?>" id="date_depart" name="date_depart" value="<?= $value('date_depart') ?>" required>
        <?= $error('date_depart') ?>
    </div>
    <div class="col-md-6">
        <label for="date_arrivee" class="form-label">Date et heure d'arrivée</label>
        <input type="datetime-local" class="form-control<?= $invalid('date_arrivee') ?>" id="date_arrivee" name="date_arrivee" value="<?= $value('date_arrivee') ?>" required>
        <?= $error('date_arrivee') ?>
    </div>
    <div class="col-md-6">
        <label for="places_total" class="form-label">Nombre de places</label>
        <input type="number" min="1" max="9" class="form-control<?= $invalid('places_total') ?>" id="places_total" name="places_total" value="<?= $value('places_total') ?>" required>
        <?= $error('places_total') ?>
    </div>
    <div class="col-md-6">
        <label for="places_disponibles" class="form-label">Places disponibles</label>
        <input type="number" min="0" max="9" class="form-control<?= $invalid('places_disponibles') ?>" id="places_disponibles" name="places_disponibles" value="<?= $value('places_disponibles') ?>" required>
        <?= $error('places_disponibles') ?>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="/mes-trajets" class="btn btn-link">Annuler</a>
    </div>
</form>
