# Touche pas au klaxon

Application web de **covoiturage inter-sites** développée en PHP selon l'architecture MVC.

## Technologies

- PHP 8.1 ou supérieur
- [izniburak/router](https://github.com/izniburak/php-router) pour le routage
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) pour la configuration
- Bootstrap 5, Bootstrap Icons et Sass pour le front
- PHPUnit et PHPStan pour les tests et l'analyse statique

## Prérequis

- PHP 8.1+ et [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) et npm
- Un serveur de base de données (MySQL / MariaDB)

## Installation

```bash
git clone https://github.com/jisodesign-stack/touche-pas-au-klaxon.git
cd touche-pas-au-klaxon

composer install
npm install
npm run build
```

Copiez ensuite `.env.example` vers `.env` (non versionné) et adaptez les paramètres de la base de données. Créez la base, le schéma et les données de démonstration (comptes `admin@klaxon.test`, `bob.martin@klaxon.test`… avec le mot de passe `Password123!`) :

```bash
php bin/db-install.php
```

Cette commande **supprime et recrée** la base définie par `DB_NAME`. Ajoutez `--no-seed` pour ne charger que le schéma. Lancez ensuite le serveur de développement :

```bash
php -S 127.0.0.1:8000 -t public
```

L'application est alors accessible sur http://127.0.0.1:8000.

## Scripts utiles

| Commande | Description |
|---|---|
| `npm run build` | Compile le Sass et copie les assets dans `public/` |
| `npm run watch` | Recompile le Sass à chaque modification |
| `vendor/bin/phpunit` | Lance les tests |
| `vendor/bin/phpstan analyse` | Lance l'analyse statique |

## Structure du projet

```
src/
  Controllers/   Contrôleurs (dont l'espace admin)
  Core/          Noyau de l'application
  Exceptions/    Exceptions personnalisées
  Middleware/    Middlewares (authentification, rôles)
  Models/        Modèles
  Repositories/  Accès aux données
  Security/      Sécurité (CSRF, mots de passe)
  Validation/    Validation des données
  Views/         Vues
config/          Configuration
database/        Schéma et jeu de données
public/          Point d'entrée et assets compilés
scss/            Sources Sass
tests/           Tests PHPUnit
```

## Contribuer

Le workflow Git (branches, issues, pull requests) est décrit dans [CONTRIBUTING.md](./CONTRIBUTING.md).

## Auteur

JivannSo
