<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repositories\TripRepository;
use App\Repositories\UserRepository;

final class UserRepositoryTest extends DatabaseTestCase
{
    public function testFindByEmailAndById(): void
    {
        $repository = new UserRepository();

        $byEmail = $repository->findByEmail('auteur@test.local');

        $this->assertNotNull($byEmail);
        $this->assertSame($this->ids['auteur'], $byEmail->id);
        $this->assertSame('auteur@test.local', $repository->findById($this->ids['auteur'])?->email);
        $this->assertNull($repository->findByEmail('inconnu@test.local'));
    }

    public function testDeleteRemovesTheUserAndTheirTrips(): void
    {
        $tripId = $this->insertTrip('+2 days');
        $repository = new UserRepository();

        $repository->delete($this->ids['auteur']);

        $this->assertNull($repository->findById($this->ids['auteur']));
        $this->assertNull((new TripRepository())->findById($tripId));
    }
}
