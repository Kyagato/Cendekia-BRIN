<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_removes_script_tags_and_their_contents(): void
    {
        $dirty = '<p>Halo dunia! <script>alert("XSS Attack!");</script> Selamat datang.</p>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('alert', $clean);
        $this->assertStringContainsString('Halo dunia!', $clean);
        $this->assertStringContainsString('Selamat datang.', $clean);
    }

    public function test_it_removes_inline_event_handlers(): void
    {
        $dirty = '<p onmouseover="alert(\'hover\')"><b onclick="alert(\'click\')">Teks Tebal</b></p>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringContainsString('<b>Teks Tebal</b>', $clean);
    }

    public function test_it_removes_javascript_in_href(): void
    {
        $dirty = '<a href="javascript:alert(1)">Klik Di Sini</a>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('href', $clean);
        $this->assertStringContainsString('Klik Di Sini', $clean);
    }

    public function test_it_removes_iframe_and_object_tags(): void
    {
        $dirty = '<div><iframe src="https://evil.com"></iframe>Konten Asli<object data="evil.swf"></object></div>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringNotContainsString('<object', $clean);
        $this->assertStringContainsString('Konten Asli', $clean);
    }

    public function test_it_preserves_allowed_formatting_and_styles(): void
    {
        $rich = '<p>Paragraf <strong>tebal</strong> dan <em>miring</em>.</p>' .
                '<ol style="list-style-type: lower-alpha;"><li>Poin a</li><li>Poin b</li></ol>' .
                '<font size="4">Ukuran font 18</font>';

        $clean = HtmlSanitizer::clean($rich);

        $this->assertStringContainsString('<strong>tebal</strong>', $clean);
        $this->assertStringContainsString('<em>miring</em>', $clean);
        $this->assertStringContainsString('list-style-type: lower-alpha;', $clean);
        $this->assertStringContainsString('size="4"', $clean);
    }

    public function test_it_handles_null_and_empty_strings(): void
    {
        $this->assertNull(HtmlSanitizer::clean(null));
        $this->assertSame('', HtmlSanitizer::clean(''));
        $this->assertSame('', HtmlSanitizer::clean('   '));
    }
}
