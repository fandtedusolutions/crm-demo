<?php

namespace Tests\Unit;

use App\Support\CourseMailBodyFormatter;
use PHPUnit\Framework\TestCase;

class CourseMailBodyFormatterTest extends TestCase
{
    public function test_plain_text_without_line_breaks_is_split_into_paragraphs(): void
    {
        $plain = 'NATDEMY ACADEMY OFFICE Subject: Support for Natdemy Mobile Application '
            .'This notice is issued by the Academy Office to all students. '
            .'📞 Support Contact Number: 92076 66615 '
            .'https://wa.me/message/YJFZKZOWNYPRD1 '
            .'Upon receiving your request, our Support Team will contact you. '
            .'Issued by: Academic Office Natdemy';

        $html = CourseMailBodyFormatter::toHtml($plain);

        $this->assertStringContainsString('<p style=', $html);
        $this->assertStringContainsString('NATDEMY ACADEMY OFFICE', $html);
        $this->assertStringContainsString('Subject: Support for Natdemy Mobile Application', $html);
        $this->assertStringContainsString('href="https://wa.me/message/YJFZKZOWNYPRD1"', $html);
        $this->assertStringContainsString('Issued by: Academic Office Natdemy', $html);
        $this->assertGreaterThan(3, substr_count($html, '<p style='));
    }

    public function test_existing_html_is_wrapped_not_double_escaped(): void
    {
        $html = '<p>Hello</p><br><a href="https://example.com">Link</a>';

        $result = CourseMailBodyFormatter::toHtml($html);

        $this->assertStringContainsString('<p>Hello</p>', $result);
        $this->assertStringContainsString('https://example.com', $result);
    }
}
