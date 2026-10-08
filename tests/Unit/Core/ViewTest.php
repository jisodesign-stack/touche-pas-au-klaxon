<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\View;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Tests du moteur de vues (échappement, layout, champ CSRF).
 */
final class ViewTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        View::configure(dirname(__DIR__, 2) . '/Support/views', 'Mon Appli');
    }

    public function testEscapeNeutralizesHtml(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;',
            View::escape('<script>alert("x")</script>'),
        );
        $this->assertSame('', View::escape(null));
    }

    public function testRenderWrapsViewInLayout(): void
    {
        $html = View::render('hello', ['name' => '<Bob>']);

        $this->assertStringContainsString('<main>Bonjour &lt;Bob&gt;', $html);
        $this->assertStringContainsString('<title>Mon Appli</title>', $html);
    }

    public function testMissingViewThrows(): void
    {
        $this->expectException(RuntimeException::class);

        View::render('inexistante');
    }

    public function testCsrfFieldContainsSessionToken(): void
    {
        $field = View::csrfField();

        $this->assertStringContainsString('name="_csrf"', $field);
        $this->assertStringContainsString((string) $_SESSION['_csrf_token'], $field);
    }
}
