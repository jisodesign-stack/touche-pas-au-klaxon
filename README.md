# Touche pas au klaxon

Application web de **covoiturage inter-sites** : elle diffuse au sein de l'entreprise les trajets
prévus entre les différentes agences (villes) afin de limiter le nombre de véhicules quasi vides.

Développée en **PHP 8** selon l'architecture **MVC**, avec une base **MySQL / MariaDB**,
**Bootstrap 5** et **Sass**.

## Fonctionnalités

| Profil | Fonctionnalités |
|---|---|
| **Visiteur** | Page d'accueil : liste des trajets à venir qui ont encore des places, triée par date de départ croissante. Formulaire de connexion. |
| **Employé connecté** | En plus : détails d'un trajet dans une fenêtre modale (auteur, téléphone, email, nombre total de places), création d'un trajet, modification et suppression de **ses** trajets. |
| **Administrateur** | Tableau de bord : liste des utilisateurs, liste des agences (création, modification, suppression), liste des trajets (suppression). Lui seul gère les agences. |

Les employés proviennent du système RH : l'application ne permet ni de les créer ni de les modifier.

## Comptes de démonstration

Disponibles après `php bin/db-install.php` (mot de passe commun : `Password123!`).

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@klaxon.test` | `Password123!` |
| Utilisateur | `bob.martin@klaxon.test` | `Password123!` |

Autres utilisateurs de démonstration : `chloe.durand@klaxon.test`, `david.petit@klaxon.test`.
Ces comptes sont destinés au développement : ne pas les utiliser en production.

## Prérequis

- PHP 8.1 ou supérieur, avec les extensions `pdo_mysql` et `mbstring`
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) et npm (compilation du Sass)
- Un serveur MySQL 8 (version testée en CI : 8.4) ou MariaDB récent : le schéma utilise des contraintes `CHECK`

## Installation et lancement

```bash
git clone https://github.com/jisodesign-stack/touche-pas-au-klaxon.git
cd touche-pas-au-klaxon

composer install        # dépendances PHP
npm install             # dépendances front
npm run build           # compile le Sass et copie les assets dans public/
```

1. Copiez `.env.example` vers `.env` (fichier non versionné) et renseignez l'accès à votre serveur MySQL
   (`DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`) ainsi que le nom de la base (`DB_NAME`).
2. Créez la base, le schéma et le jeu d'essais :

   ```bash
   php bin/db-install.php
   ```

   Cette commande **supprime et recrée** la base `DB_NAME`. Avec `--no-seed`, seul le schéma est créé.
   Les scripts SQL sont dans [database/schema.sql](./database/schema.sql) (création) et
   [database/seed.sql](./database/seed.sql) (jeu d'essais) ; ils peuvent aussi être exécutés à la main.
3. Lancez le serveur de développement :

   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

   L'application est accessible sur http://127.0.0.1:8000. En production, pointez le serveur web
   (Apache avec `public/.htaccess`, ou Nginx) sur le dossier `public/` et mettez `APP_DEBUG=false`.

## Utilisation

1. Ouvrez la page d'accueil : les trajets disponibles sont visibles sans connexion.
2. Cliquez sur **Connexion** et saisissez un compte de démonstration.
3. Employé : utilisez l'icône « œil » d'un trajet pour afficher son détail, **Proposer un trajet** pour en créer un,
   et les icônes crayon / corbeille sur vos propres trajets pour les modifier ou les supprimer.
4. Administrateur : le menu de l'en-tête donne accès aux utilisateurs, aux agences et aux trajets.

## Architecture

Le code est organisé en MVC, avec un noyau réutilisable par les futurs sites thématiques de l'entreprise.

```
public/          Point d'entrée (index.php) et assets compilés
config/          app.php (configuration) et routes.php (routes)
src/
  Core/          Noyau réutilisable : Application, Database, View, Flash
  Controllers/   Contrôleurs (Admin/ pour l'administration)
  Models/        Objets métier immuables (User, Trip)
  Repositories/  Accès aux données (requêtes SQL préparées)
  Validation/    Contrôles de cohérence des formulaires
  Security/      Authentification (Auth) et protection CSRF
  Middleware/    Contrôle d'accès (AuthMiddleware, AdminMiddleware)
  Views/         Vues PHP et layout
scss/            Sources Sass ; la palette de couleurs est dans scss/_variables.scss
database/        Scripts SQL (schema.sql, seed.sql)
bin/             Scripts en ligne de commande (db-install.php)
tests/           Tests PHPUnit (Unit/ et Integration/)
docs/            MCD (MCD.png) et MLD (MLD.md)
```

**Thème graphique** : la palette imposée (`#f1f8fc`, `#0074c7`, `#00497c`, `#384050`, `#cd2c2e`, `#82b864`) est
déclarée une seule fois dans `scss/_variables.scss` et affectée aux variables Bootstrap (`$primary`, `$secondary`,
`$success`, `$danger`, `$light`, `$dark`). Un site thématique n'a qu'à remplacer ces valeurs, puis lancer `npm run build`.

**Code** : toutes les classes et méthodes sont documentées en DocBlock.

## Qualité et tests

| Commande | Description |
|---|---|
| `composer lint` | Vérifie le style du code (PSR-12 : indentation, longueur de ligne, en-têtes) avec PHP_CodeSniffer |
| `composer analyse` | Analyse statique PHPStan (niveau 6) |
| `composer test` | Tests PHPUnit (unitaires + intégration) |
| `npm run build` / `npm run watch` | Compile le Sass (une fois / en continu) |

Les tests d'intégration couvrent toutes les opérations d'écriture en base (création, modification, suppression
des trajets, des agences et des utilisateurs). Ils utilisent une base dédiée `touche_pas_au_klaxon_test`
(modifiable via `DB_NAME_TEST`), créée automatiquement ; chaque test est annulé par une transaction, la base de
développement n'est donc jamais modifiée. Sans serveur MySQL joignable, ils sont ignorés.

La CI GitHub Actions (`.github/workflows/ci.yml`) exécute le contrôle de style, l'analyse statique et les tests sur MySQL, et vérifie que
le schéma et le jeu d'essais s'installent correctement. Les conventions d'indentation (4 espaces en PHP, fins de ligne LF)
sont décrites dans `.editorconfig`.

## Conception de la base de données

- [MCD](./docs/MCD.png) (modèle conceptuel de données)
- [MLD](./docs/MLD.md) (modèle logique de données, format textuel)

## Contribuer

Le workflow Git (branches, issues, pull requests) est décrit dans [CONTRIBUTING.md](./CONTRIBUTING.md).

## Auteur

JivannSo
