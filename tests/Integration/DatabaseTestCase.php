<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Database;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Base des tests d'intégration : base MySQL dédiée aux tests (DB_NAME_TEST),
 * schéma créé à la volée, chaque test s'exécute dans une transaction annulée à la fin.
 *
 * Les tests sont ignorés (et non en échec) si aucun serveur MySQL n'est joignable.
 */
abstract class DatabaseTestCase extends TestCase
{
    private static bool $available = false;

    protected PDO $pdo;

    /** @var array{depart: int, arrivee: int, autre: int, auteur: int, intrus: int} */
    protected array $ids;

    public static function setUpBeforeClass(): void
    {
        try {
            $name = (string) $_ENV['DB_NAME'];
            $server = Database::connect(false);
            $server->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $server->exec("USE `$name`");
            $server->exec((string) file_get_contents(dirname(__DIR__, 2) . '/database/schema.sql'));
            self::$available = true;
        } catch (PDOException) {
            self::$available = false;
        }
    }

    protected function setUp(): void
    {
        if (!self::$available) {
            $this->markTestSkipped('Aucun serveur MySQL disponible pour les tests d\'intégration.');
        }

        $this->pdo = Database::connection();
        $this->pdo->beginTransaction();

        $this->ids = [
            'depart' => $this->insertAgency('Test Départ'),
            'arrivee' => $this->insertAgency('Test Arrivée'),
            'autre' => $this->insertAgency('Test Autre'),
            'auteur' => $this->insertUser('auteur@test.local'),
            'intrus' => $this->insertUser('intrus@test.local'),
        ];
    }

    protected function tearDown(): void
    {
        if (isset($this->pdo) && $this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    protected function insertAgency(string $name): int
    {
        $this->pdo->prepare('INSERT INTO agences (nom) VALUES (:nom)')->execute(['nom' => $name]);

        return (int) $this->pdo->lastInsertId();
    }

    protected function insertUser(string $email): int
    {
        $this->pdo->prepare(
            "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role)
             VALUES ('Nom', 'Prénom', :email, '0600000000', 'hash', 'utilisateur')",
        )->execute(['email' => $email]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Insère un trajet directement en SQL (hors dépôt testé).
     *
     * @param string $depart Expression de date PHP du départ (ex. « +2 days »)
     */
    protected function insertTrip(string $depart, int $availablePlaces = 2, ?int $authorId = null): int
    {
        $start = new \DateTimeImmutable($depart);

        $this->pdo->prepare(
            'INSERT INTO trajets (
                 agence_depart_id, agence_arrivee_id, date_depart, date_arrivee,
                 places_total, places_disponibles, auteur_id
             ) VALUES (:depart, :arrivee, :date_depart, :date_arrivee, 4, :dispo, :auteur)',
        )->execute([
            'depart' => $this->ids['depart'],
            'arrivee' => $this->ids['arrivee'],
            'date_depart' => $start->format('Y-m-d H:i:s'),
            'date_arrivee' => $start->modify('+3 hours')->format('Y-m-d H:i:s'),
            'dispo' => $availablePlaces,
            'auteur' => $authorId ?? $this->ids['auteur'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
