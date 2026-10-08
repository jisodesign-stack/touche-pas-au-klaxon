<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repositories\AgencyRepository;
use PDOException;

/**
 * Tests des écritures sur la table agences (création, renommage, suppression, contraintes).
 */
final class AgencyRepositoryTest extends DatabaseTestCase
{
    public function testCreateAddsAnAgency(): void
    {
        $repository = new AgencyRepository();

        $repository->create('Dijon');

        $this->assertTrue($repository->nameExists('Dijon'));
        $this->assertContains('Dijon', array_column($repository->all(), 'nom'));
    }

    public function testUpdateRenamesAnAgency(): void
    {
        $repository = new AgencyRepository();

        $repository->update($this->ids['autre'], 'Renommée');

        $this->assertSame('Renommée', $repository->find($this->ids['autre'])['nom'] ?? null);
        $this->assertFalse($repository->nameExists('Test Autre'));
    }

    public function testDeleteRemovesAnUnusedAgency(): void
    {
        $repository = new AgencyRepository();

        $repository->delete($this->ids['autre']);

        $this->assertNull($repository->find($this->ids['autre']));
    }

    public function testDeleteIsRejectedByTheDatabaseWhenTheAgencyIsUsed(): void
    {
        $this->insertTrip('+2 days');
        $repository = new AgencyRepository();

        $this->assertSame(1, $repository->tripCount($this->ids['depart']));

        $this->expectException(PDOException::class);
        $repository->delete($this->ids['depart']);
    }

    public function testNameExistsCanIgnoreTheAgencyBeingEdited(): void
    {
        $repository = new AgencyRepository();

        $this->assertTrue($repository->nameExists('Test Autre'));
        $this->assertFalse($repository->nameExists('Test Autre', $this->ids['autre']));
    }

    public function testDuplicateNamesAreRejectedByTheDatabase(): void
    {
        $this->expectException(PDOException::class);

        (new AgencyRepository())->create('Test Autre');
    }
}
