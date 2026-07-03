<?php

namespace Tests\Unit\Services\CallRecording;

use App\Services\CallRecording\RecordingFileTypeDetector;
use PHPUnit\Framework\TestCase;

class RecordingFileTypeDetectorTest extends TestCase
{
    private RecordingFileTypeDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new RecordingFileTypeDetector();
    }

    public function test_it_detects_adts_aac_from_header(): void
    {
        $samplePath = dirname(__DIR__, 4) . '/tests/fixtures/sample-adts.aac';
        if (!is_readable($samplePath)) {
            $this->markTestSkipped('Sample ADTS fixture is not available.');
        }

        $detected = $this->detector->detectFromPath($samplePath);

        $this->assertNotNull($detected);
        $this->assertSame('aac', $detected['extension']);
        $this->assertSame('audio/aac', $detected['mime']);
    }

    public function test_it_maps_known_extensions_and_mimes(): void
    {
        $this->assertSame('audio/aac', $this->detector->mimeFromExtension('aac'));
        $this->assertSame('audio/mp4', $this->detector->mimeFromExtension('m4a'));
        $this->assertSame('aac', $this->detector->extensionFromMime('audio/aac'));
        $this->assertSame('m4a', $this->detector->extensionFromMime('audio/x-m4a'));
    }
}
