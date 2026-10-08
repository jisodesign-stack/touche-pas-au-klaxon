# Workflow Git

## Branches

| Branche | Rôle |
|---|---|
| `main` | Version stable (livrable). Jamais de commit direct. |
| `develop` | Intégration des fonctionnalités terminées. |
| `feature/<n°issue>-<slug>` | Une branche par issue de fonctionnalité. |
| `fix/<n°issue>-<slug>` | Correction de bug. |
| `docs/<n°issue>-<slug>` | Documentation. |

## Cycle d'une issue

1. Choisir une issue et se l'assigner.
2. Créer la branche depuis `develop` : `git switch develop && git pull && git switch -c feature/<n°>-<slug>`.
3. Commiter avec des messages clairs (`feat:`, `fix:`, `docs:`, `test:`, `chore:`).
4. Pousser puis ouvrir une Pull Request vers `develop` avec `Closes #<n°>` dans la description.
5. Après relecture, fusion (squash) dans `develop`, puis suppression de la branche.
6. Quand `develop` est stable, Pull Request `develop` → `main`.
