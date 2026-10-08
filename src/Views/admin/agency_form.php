<?php use App\Core\View; ?>
<p><a href="/admin/agences">&larr; Agences</a></p>
<h1 class="h3 mb-3"><?= View::escape($title) ?></h1>
<form method="post" action="<?= View::escape($action) ?>" class="col-md-6" novalidate>
    <?= View::csrfField() ?>
    <div class="mb-3">
        <label for="nom" class="form-label">Nom de l'agence</label>
        <input type="text" class="form-control<?= !empty($error) ? ' is-invalid' : '' ?>" id="nom" name="nom" maxlength="100" value="<?= View::escape($nom) ?>" required autofocus>
        <?php if (!empty($error)): ?>
            <div class="invalid-feedback"><?= View::escape($error) ?></div>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="/admin/agences" class="btn btn-link">Annuler</a>
</form>
