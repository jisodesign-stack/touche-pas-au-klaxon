<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Security\Csrf;
use PHPUnit\Framework\TestCase;

/**
 * Tests de la protection CSRF (génération et vérification du jeton).
 */
final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testTokenIsStableWithinASession(): void
    {
        $this->assertSame(Csrf::token(), Csrf::token());
        $this->assertSame(64, strlen(Csrf::token()));
    }

    public function testValidTokenIsAccepted(): void
    {
        $this->assertTrue(Csrf::isValid(Csrf::token()));
    }

    public function testInvalidValuesAreRejected(): void
    {
        Csrf::token();

        $this->assertFalse(Csrf::isValid('mauvais'));
        $this->assertFalse(Csrf::isValid(''));
        $this->assertFalse(Csrf::isValid(null));
        $this->assertFalse(Csrf::isValid(['array']));
    }

    public function testNothingIsValidWithoutAToken(): void
    {
        $this->assertFalse(Csrf::isValid('abc'));
    }
}
