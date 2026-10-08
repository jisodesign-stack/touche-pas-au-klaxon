<?php

declare(strict_types=1);

namespace Tests\Unit\Validation;

use App\Validation\TripValidator;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests des contrôles de cohérence d'un trajet (agences, dates, places).
 */
final class TripValidatorTest extends TestCase
{
    private const AGENCIES = [1, 2, 3];

    /** @return array<string, string> */
    private function validInput(): array
    {
        return [
            'agence_depart_id' => '1',
            'agence_arrivee_id' => '2',
            'date_depart' => (new DateTimeImmutable('+10 days'))->format('Y-m-d\T08:00'),
            'date_arrivee' => (new DateTimeImmutable('+10 days'))->format('Y-m-d\T12:00'),
            'places_total' => '4',
            'places_disponibles' => '3',
        ];
    }

    public function testValidInputIsAccepted(): void
    {
        $result = (new TripValidator())->validate($this->validInput(), self::AGENCIES);

        $this->assertSame([], $result['errors']);
        $this->assertNotNull($result['data']);
        $this->assertSame(1, $result['data']['depart_id']);
        $this->assertSame(2, $result['data']['arrivee_id']);
        $this->assertSame(4, $result['data']['places_total']);
        $this->assertSame(3, $result['data']['places_disponibles']);
    }

    /** @return iterable<string, array{array<string, string>, string}> */
    public static function invalidInputs(): iterable
    {
        yield 'agence de départ inconnue' => [['agence_depart_id' => '99'], 'agence_depart_id'];
        yield "agence d'arrivée absente" => [['agence_arrivee_id' => ''], 'agence_arrivee_id'];
        yield 'mêmes agences' => [['agence_arrivee_id' => '1'], 'agence_arrivee_id'];
        yield 'date de départ invalide' => [['date_depart' => 'demain'], 'date_depart'];
        yield 'date de départ passée' => [
            ['date_depart' => '2020-01-01T08:00', 'date_arrivee' => '2020-01-01T12:00'],
            'date_depart',
        ];
        yield 'arrivée avant le départ' => [['date_arrivee' => '2000-01-01T00:00'], 'date_arrivee'];
        yield 'zéro place' => [['places_total' => '0', 'places_disponibles' => '0'], 'places_total'];
        yield 'trop de places' => [['places_total' => '10'], 'places_total'];
        yield 'places non numériques' => [['places_total' => 'abc'], 'places_total'];
        yield 'places disponibles négatives' => [['places_disponibles' => '-1'], 'places_disponibles'];
        yield 'plus de places disponibles que le total' => [['places_disponibles' => '5'], 'places_disponibles'];
    }

    /**
     * @param array<string, string> $override
     */
    #[DataProvider('invalidInputs')]
    public function testInvalidInputIsRejected(array $override, string $expectedErrorKey): void
    {
        $result = (new TripValidator())->validate($override + $this->validInput(), self::AGENCIES);

        $this->assertNull($result['data']);
        $this->assertArrayHasKey($expectedErrorKey, $result['errors']);
    }

    public function testMissingFieldsAreRejected(): void
    {
        $result = (new TripValidator())->validate([], self::AGENCIES);

        $this->assertNull($result['data']);
        $this->assertCount(6, $result['errors']);
    }

    public function testPastDepartureIsAllowedWhenUnchanged(): void
    {
        $past = new DateTimeImmutable('2020-01-01 08:00');
        $input = [
            'date_depart' => $past->format('Y-m-d\TH:i'),
            'date_arrivee' => '2020-01-01T12:00',
        ] + $this->validInput();

        $result = (new TripValidator())->validate($input, self::AGENCIES, $past);

        $this->assertSame([], $result['errors']);
    }
}
