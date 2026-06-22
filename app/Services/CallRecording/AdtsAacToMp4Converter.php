<?php

namespace App\Services\CallRecording;

/**
 * Remux raw ADTS AAC (.aac) into an MP4/M4A container for HTML5 audio playback.
 * Browsers play AAC in MP4 (audio/mp4) but not raw ADTS streams (audio/aac).
 */
class AdtsAacToMp4Converter
{
    private const SAMPLE_RATES = [
        96000, 88200, 64000, 48000, 44100, 32000,
        24000, 22050, 16000, 12000, 11025, 8000, 7350,
    ];

    public function convert(string $inputPath, string $outputPath): bool
    {
        if (!is_readable($inputPath)) {
            return false;
        }

        $parsed = $this->parseAdtsFile($inputPath);
        if ($parsed === null) {
            return false;
        }

        $mp4 = $this->buildMp4(
            $parsed['frames'],
            $parsed['profile'],
            $parsed['sample_rate_index'],
            $parsed['channel_config']
        );

        if ($mp4 === '') {
            return false;
        }

        $directory = dirname($outputPath);
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return false;
        }

        return file_put_contents($outputPath, $mp4) !== false;
    }

    /**
     * @return array{frames: list<string>, profile: int, sample_rate_index: int, channel_config: int}|null
     */
    private function parseAdtsFile(string $inputPath): ?array
    {
        $handle = fopen($inputPath, 'rb');
        if ($handle === false) {
            return null;
        }

        $frames = [];
        $profile = null;
        $sampleRateIndex = null;
        $channelConfig = null;

        while (!feof($handle)) {
            $header = fread($handle, 7);
            if ($header === false || strlen($header) < 7) {
                break;
            }

            if ((ord($header[0]) !== 0xFF) || ((ord($header[1]) & 0xF0) !== 0xF0)) {
                break;
            }

            $protectionAbsent = (ord($header[1]) & 0x01) === 1;
            $headerSize = $protectionAbsent ? 7 : 9;

            if ($headerSize === 9) {
                $extra = fread($handle, 2);
                if ($extra === false || strlen($extra) < 2) {
                    break;
                }
                $header .= $extra;
            }

            $frameLength = ((ord($header[3]) & 0x03) << 11)
                | (ord($header[4]) << 3)
                | ((ord($header[5]) & 0xE0) >> 5);

            if ($frameLength <= $headerSize) {
                break;
            }

            $payloadLength = $frameLength - $headerSize;
            $payload = fread($handle, $payloadLength);
            if ($payload === false || strlen($payload) !== $payloadLength) {
                break;
            }

            if ($profile === null) {
                $profile = (ord($header[2]) & 0xC0) >> 6;
                $sampleRateIndex = (ord($header[2]) & 0x3C) >> 2;
                $channelConfig = ((ord($header[2]) & 0x01) << 2) | ((ord($header[3]) & 0xC0) >> 6);
            }

            $frames[] = $payload;
        }

        fclose($handle);

        if ($frames === [] || $profile === null || $sampleRateIndex === null || $channelConfig === null) {
            return null;
        }

        if (!isset(self::SAMPLE_RATES[$sampleRateIndex])) {
            return null;
        }

        return [
            'frames' => $frames,
            'profile' => $profile,
            'sample_rate_index' => $sampleRateIndex,
            'channel_config' => $channelConfig,
        ];
    }

    /**
     * @param list<string> $frames
     */
    private function buildMp4(array $frames, int $profile, int $sampleRateIndex, int $channelConfig): string
    {
        $sampleRate = self::SAMPLE_RATES[$sampleRateIndex];
        $audioObjectType = $profile + 1;
        $samplesPerFrame = 1024;
        $frameCount = count($frames);
        $sampleCount = $frameCount * $samplesPerFrame;
        $mdatPayload = implode('', $frames);

        $asc = $this->buildAudioSpecificConfig($audioObjectType, $sampleRateIndex, $channelConfig);
        $esds = $this->buildEsdsBox($asc);

        $mp4a = $this->box('mp4a', $this->uint16(0) . $this->uint16(0) . $this->uint32(0)
            . $this->uint16(0) . $this->uint16(0) . $this->uint16($channelConfig)
            . $this->uint16(16) . $this->uint16(0) . $this->uint16(0)
            . $this->sint16(0) . $esds);

        $stsd = $this->fullBox('stsd', 0, 0, $this->uint32(1) . $mp4a);
        $stts = $this->fullBox('stts', 0, 0, $this->uint32(1) . $this->uint32($frameCount) . $this->uint32($samplesPerFrame));
        $stsc = $this->fullBox('stsc', 0, 0, $this->uint32(1) . $this->uint32(1) . $this->uint32($frameCount) . $this->uint32(1));
        $stsz = $this->fullBox('stsz', 0, 0, $this->uint32(0) . $this->uint32($frameCount) . $this->buildSampleSizes($frames));

        $smhd = $this->fullBox('smhd', 0, 0, $this->uint32(0) . $this->uint32(0));
        $url = $this->box('url ', $this->uint32(1));
        $dref = $this->fullBox('dref', 0, 0, $this->uint32(1) . $url);
        $dinf = $this->box('dinf', $dref);

        $hdlr = $this->fullBox('hdlr', 0, 0, $this->uint32(0) . 'soun' . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . "SoundHandler\x00");
        $mdhd = $this->fullBox('mdhd', 0, 0, $this->uint32(0) . $this->uint32(0) . $this->uint32($sampleRate) . $this->uint32($sampleCount) . $this->uint32(0x55C40000) . "\x00\x00");
        $tkhd = $this->fullBox('tkhd', 0, 0, $this->uint32(1) . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . $this->uint32(0) . $this->uint16(0) . $this->uint16(0) . $this->uint16(256) . $this->uint16(0) . $this->sint16(0) . $this->sint16(0) . $this->uint32(0) . $this->uint32(0));
        $mvhd = $this->fullBox('mvhd', 0, 0, $this->uint32(0) . $this->uint32(0) . $this->uint32($sampleRate) . $this->uint32($sampleCount) . $this->uint32(0x00010000) . $this->uint32(0x00010000) . $this->uint32(0) . $this->uint32(0) . $this->matrix() . $this->uint32(0) . $this->uint32(2));
        $ftyp = $this->box('ftyp', 'isom' . $this->uint32(512) . 'isom' . 'iso2' . 'mp41');

        $stcoPlaceholder = $this->fullBox('stco', 0, 0, $this->uint32(1) . $this->uint32(0));
        $stbl = $this->box('stbl', $stsd . $stts . $stsc . $stsz . $stcoPlaceholder);
        $minf = $this->box('minf', $smhd . $dinf . $stbl);
        $mdia = $this->box('mdia', $mdhd . $hdlr . $minf);
        $trak = $this->box('trak', $tkhd . $mdia);
        $moov = $this->box('moov', $mvhd . $trak);

        $mdatOffset = strlen($ftyp) + strlen($moov) + 8;
        $stco = $this->fullBox('stco', 0, 0, $this->uint32(1) . $this->uint32($mdatOffset));
        $stbl = $this->box('stbl', $stsd . $stts . $stsc . $stsz . $stco);
        $minf = $this->box('minf', $smhd . $dinf . $stbl);
        $mdia = $this->box('mdia', $mdhd . $hdlr . $minf);
        $trak = $this->box('trak', $tkhd . $mdia);
        $moov = $this->box('moov', $mvhd . $trak);
        $mdat = $this->box('mdat', $mdatPayload);

        return $ftyp . $moov . $mdat;
    }

    private function buildAudioSpecificConfig(int $audioObjectType, int $sampleRateIndex, int $channelConfig): string
    {
        $byte1 = ($audioObjectType << 3) | ($sampleRateIndex >> 1);
        $byte2 = (($sampleRateIndex & 1) << 7) | ($channelConfig << 3);

        return chr($byte1) . chr($byte2);
    }

    private function buildEsdsBox(string $asc): string
    {
        $decoderConfig = chr(0x04)
            . chr(0x40)
            . $this->uint24(0)
            . chr(0x15)
            . chr(strlen($asc)) . $asc
            . chr(0x06)
            . chr(0x01)
            . chr(0x02);

        $esd = chr(0x03)
            . $this->uint8Length(strlen($decoderConfig) + 2)
            . chr(0x00)
            . chr(0x01)
            . $decoderConfig;

        return $this->fullBox('esds', 0, 0, $esd);
    }

    /**
     * @param list<string> $frames
     */
    private function buildSampleSizes(array $frames): string
    {
        $sizes = '';
        foreach ($frames as $frame) {
            $sizes .= $this->uint32(strlen($frame));
        }

        return $sizes;
    }

    private function matrix(): string
    {
        return $this->uint32(0x00010000)
            . $this->uint32(0)
            . $this->uint32(0)
            . $this->uint32(0)
            . $this->uint32(0x00010000)
            . $this->uint32(0)
            . $this->uint32(0)
            . $this->uint32(0)
            . $this->uint32(0x40000000);
    }

    private function box(string $type, string $data): string
    {
        return $this->uint32(8 + strlen($data)) . $this->padType($type) . $data;
    }

    private function fullBox(string $type, int $version, int $flags, string $data): string
    {
        return $this->box($type, chr($version) . $this->uint24($flags) . $data);
    }

    private function padType(string $type): string
    {
        return str_pad(substr($type, 0, 4), 4, "\0");
    }

    private function uint8Length(int $length): string
    {
        return chr($length);
    }

    private function uint16(int $value): string
    {
        return pack('n', $value);
    }

    private function sint16(int $value): string
    {
        return pack('n', $value & 0xFFFF);
    }

    private function uint24(int $value): string
    {
        return pack('N', $value)[1] . pack('N', $value)[2] . pack('N', $value)[3];
    }

    private function uint32(int $value): string
    {
        return pack('N', $value);
    }
}
