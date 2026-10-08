# MLD - Touche pas au klaxon

Modèle logique de données (format textuel). Les clés primaires sont repérées par `#`,
les clés étrangères par `→`. Le MCD correspondant est dans [MCD.png](./MCD.png) ; le script de
création est [database/schema.sql](../database/schema.sql).

```
AGENCE (#id_agence, nom)
    - nom : unique

UTILISATEUR (#id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role)
    - email : unique (identifiant de connexion)
    - mot_de_passe : empreinte password_hash
    - role : « utilisateur » ou « admin »

TRAJET (#id_trajet, → id_agence_depart, → id_agence_arrivee, date_depart, date_arrivee,
        places_total, places_disponibles, → id_auteur)
    - id_agence_depart  référence AGENCE (id_agence)
    - id_agence_arrivee référence AGENCE (id_agence)
    - id_auteur         référence UTILISATEUR (id_utilisateur), ON DELETE CASCADE
```

## Contraintes d'intégrité

| Contrainte | Table | Règle |
|---|---|---|
| `chk_trajets_agences` | `trajets` | l'agence de départ est différente de l'agence d'arrivée |
| `chk_trajets_dates` | `trajets` | la date d'arrivée est postérieure à la date de départ |
| `chk_trajets_places` | `trajets` | les places disponibles ne dépassent pas le nombre total de places |
| `uq_agences_nom` | `agences` | le nom d'une agence est unique |
| `uq_utilisateurs_email` | `utilisateurs` | l'adresse email est unique |

## Correspondance avec les tables SQL

| Entité du MLD | Table SQL |
|---|---|
| AGENCE | `agences` |
| UTILISATEUR | `utilisateurs` |
| TRAJET | `trajets` |

Les associations du MCD (PARTIR DE, ARRIVER A, PROPOSER) deviennent des clés étrangères de `trajets`
car chaque trajet a exactement une agence de départ, une agence d'arrivée et un auteur (cardinalités 1,1).
