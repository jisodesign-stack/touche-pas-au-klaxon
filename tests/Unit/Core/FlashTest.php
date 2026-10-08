<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Flash;
use PHPUnit\Framework\TestCase;

/**
 * Tests des messages flash (lecture unique).
 */
final class FlashTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testMessageIsReadOnlyOnce(): void
    {
        Flash::set('success', 'Fait.');

        $this->assertSame(['type' => 'success', 'message' => 'Fait.'], Flash::pull());
        $this->assertNull(Flash::pull());
    }

    public function testPullWithoutMessageReturnsNull(): void
    {
        $this->assertNull(Flash::pull());
    }
}
