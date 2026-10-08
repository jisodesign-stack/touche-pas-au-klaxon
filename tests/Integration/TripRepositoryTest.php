<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repositories\TripRepository;
use DateTimeImmutable;
use PDOException;

final class TripRepositoryTest extends DatabaseTestCase
{
    /**
     * @return array{depart_id: int, arrivee_id: int, date_depart: DateTimeImmutable, date_arrivee: DateTimeImmutable, places_total: int, places_disponibles: int}
     */
    private function data(): array
    {
        return [
            'depart_id' => $this->ids['depart'],
            'arrivee_id' => $this->ids['arrivee'],
            'date_depart' => new DateTimeImmutable('+5 days 08:00'),
            'date_arrivee' => new DateTimeImmutable('+5 days 12:00'),
            'places_total' => 4,
            'places_disponibles' => 3,
        ];
    }

    public function testCreatePersistsTheTripWithItsAuthor(): void
    {
        $repository = new TripRepository();

        $id = $repository->create($this->data(), $this->ids['auteur']);
        $trip = $repository->findById($id);

        $this->assertNotNull($trip);
        $this->assertSame($this->ids['auteur'], $trip->auteurId);
        $this->assertSame('Test Départ', $trip->agenceDepart);
        $this->assertSame('Test Arrivée', $trip->agenceArrivee);
        $this->assertSame(4, $trip->placesTotal);
        $this->assertSame(3, $trip->placesDisponibles);
    }

    public function testUpdateChangesTheTripWhenCalledByItsAuthor(): void
    {
        $repository = new TripRepository();
        $id = $repository->create($this->data(), $this->ids['auteur']);

        $changes = ['arrivee_id' => $this->ids['autre'], 'places_total' => 6, 'places_disponibles' => 1] + $this->data();

        $this->assertTrue($repository->update($id, $this->ids['auteur'], $changes));

        $trip = $repository->findById($id);
        $this->assertNotNull($trip);
        $this->assertSame('Test Autre', $trip->agenceArrivee);
        $this->assertSame(6, $trip->placesTotal);
        $this->assertSame(1, $trip->placesDisponibles);
    }

    public function testUpdateIsRefusedForSomeoneElse(): void
    {
        $repository = new TripRepository();
        $id = $repository->create($this->data(), $this->ids['auteur']);

        $changes = ['places_total' => 8, 'places_disponibles' => 8] + $this->data();

        $this->assertFalse($repository->update($id, $this->ids['intrus'], $changes));
        $this->assertSame(4, $repository->findById($id)?->placesTotal);
    }

    public function testDeleteRemovesTheTripForItsAuthorOnly(): void
    {
        $repository = new TripRepository();
        $id = $repository->create($this->data(), $this->ids['auteur']);

        $this->assertFalse($repository->delete($id, $this->ids['intrus']));
        $this->assertNotNull($repository->findById($id));

        $this->assertTrue($repository->delete($id, $this->ids['auteur']));
        $this->assertNull($repository->findById($id));
    }

    public function testDeleteByIdRemovesAnyTrip(): void
    {
        $repository = new TripRepository();
        $id = $repository->create($this->data(), $this->ids['auteur']);

        $repository->deleteById($id);

        $this->assertNull($repository->findById($id));
    }

    public function testDatabaseRejectsIncoherentTrips(): void
    {
        $repository = new TripRepository();

        $this->expectException(PDOException::class);

        $repository->create(['arrivee_id' => $this->ids['depart']] + $this->data(), $this->ids['auteur']);
    }

    public function testUpcomingAvailableExcludesPastAndFullTripsAndIsSortedByDeparture(): void
    {
        $later = $this->insertTrip('+4 days');
        $sooner = $this->insertTrip('+2 days');
        $this->insertTrip('-2 days');
        $this->insertTrip('+3 days', 0);

        $ids = array_map(
            static fn ($trip): int => $trip->id,
            array_filter(
                (new TripRepository())->findUpcomingAvailable(),
                fn ($trip): bool => $trip->agenceDepart === 'Test Départ',
            ),
        );

        $this->assertSame([$sooner, $later], array_values($ids));
    }
}
