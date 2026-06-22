<?php

namespace Tests\Unit\Services\CallRecording;

use App\Services\CallRecording\AdtsAacToMp4Converter;
use PHPUnit\Framework\TestCase;

class AdtsAacToMp4ConverterTest extends TestCase
{
    public function test_it_converts_adts_aac_into_mp4_container(): void
    {
        $samplePath = dirname(__DIR__, 4) . '/tests/fixtures/sample-adts.aac';
        if (!is_readable($samplePath)) {
            $this->markTestSkipped('Sample ADTS fixture is not available.');
        }

        $converter = new AdtsAacToMp4Converter();
        $outputPath = sys_get_temp_dir() . '/sample-playback-' . uniqid('', true) . '.m4a';

        try {
            $this->assertTrue($converter->convert($samplePath, $outputPath));
            $this->assertFileExists($outputPath);
            $this->assertGreaterThan(0, filesize($outputPath));

            $header = file_get_contents($outputPath, false, null, 0, 12);
            $this->assertStringContainsString('ftyp', $header);
        } finally {
            if (is_file($outputPath)) {
                unlink($outputPath);
            }
        }
    }
}
