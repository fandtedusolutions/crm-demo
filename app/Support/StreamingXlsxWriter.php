<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Writes an .xlsx file row by row so large exports do not stay in memory.
 */
class StreamingXlsxWriter
{
    private $sheetHandle = null;

    private string $sheetPath = '';

    private string $destinationPath;

    private int $rowIndex = 0;

    private bool $closed = false;

    public function __construct(string $destinationPath)
    {
        $this->destinationPath = $destinationPath;
    }

    public function open(): void
    {
        $directory = dirname($this->destinationPath);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create export directory.');
        }

        $this->sheetPath = tempnam(sys_get_temp_dir(), 'xlsx-sheet-');
        if ($this->sheetPath === false) {
            throw new RuntimeException('Unable to create a temporary worksheet.');
        }

        $handle = fopen($this->sheetPath, 'wb');
        if ($handle === false) {
            throw new RuntimeException('Unable to open a temporary worksheet.');
        }

        $this->sheetHandle = $handle;
        fwrite($this->sheetHandle, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>');
        fwrite($this->sheetHandle, '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>');
    }

    /**
     * @param  array<int, string|int|float|null>  $cells
     */
    public function addRow(array $cells, bool $header = false): void
    {
        if (! is_resource($this->sheetHandle)) {
            throw new RuntimeException('The workbook is not open.');
        }

        $this->rowIndex++;
        $rowNumber = $this->rowIndex;
        $style = $header ? ' s="1"' : '';
        $xml = '<row r="'.$rowNumber.'">';

        foreach (array_values($cells) as $index => $value) {
            $cellRef = $this->columnLetter($index + 1).$rowNumber;
            $text = $this->xmlText($value);
            $xml .= '<c r="'.$cellRef.'" t="inlineStr"'.$style.'><is><t xml:space="preserve">'.$text.'</t></is></c>';
        }

        $xml .= '</row>';
        fwrite($this->sheetHandle, $xml);
    }

    public function close(): void
    {
        if ($this->closed) {
            return;
        }

        if (! is_resource($this->sheetHandle)) {
            throw new RuntimeException('The workbook is not open.');
        }

        fwrite($this->sheetHandle, '</sheetData></worksheet>');
        fclose($this->sheetHandle);
        $this->sheetHandle = null;

        $partialPath = $this->destinationPath.'.partial';
        if (is_file($partialPath)) {
            unlink($partialPath);
        }

        $zip = new ZipArchive();
        if ($zip->open($partialPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create the Excel file.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFile($this->sheetPath, 'xl/worksheets/sheet1.xml');
        $zip->close();

        if (is_file($this->sheetPath)) {
            unlink($this->sheetPath);
        }

        if (is_file($this->destinationPath) && ! unlink($this->destinationPath)) {
            throw new RuntimeException('Unable to replace the previous Excel file.');
        }

        if (! rename($partialPath, $this->destinationPath)) {
            throw new RuntimeException('Unable to store the Excel file.');
        }

        $this->closed = true;
    }

    public function rowCount(): int
    {
        return $this->rowIndex;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private function xmlText(mixed $value): string
    {
        $text = $value === null ? '' : (string) $value;
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text) ?? '';

        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Report" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="2">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            .'</cellXfs>'
            .'</styleSheet>';
    }
}
