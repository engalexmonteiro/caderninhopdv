<?php

namespace App\Services;

use RuntimeException;

class XlsxReader
{
    /**
     * @return array<int, array<int, string>>
     */
    public static function readRows(string $path): array
    {
        $entries = self::zipEntries($path);
        $sharedStrings = self::sharedStrings($entries);
        $sheetPath = self::firstSheetPath($entries);
        $sheetXml = $entries[$sheetPath] ?? false;
        if ($sheetXml === false) {
            throw new RuntimeException('A primeira aba da planilha não foi encontrada.');
        }

        $sheet = simplexml_load_string($sheetXml);
        if (!$sheet) {
            throw new RuntimeException('A primeira aba da planilha está inválida.');
        }

        $sheet->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];

        foreach ($sheet->xpath('//m:sheetData/m:row') ?: [] as $row) {
            $line = [];
            $max = 0;
            $row->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($row->xpath('m:c') ?: [] as $cell) {
                $ref = (string) $cell['r'];
                $index = self::columnIndex($ref);
                $max = max($max, $index);
                $line[$index - 1] = self::cellValue($cell, $sharedStrings);
            }

            if ($max > 0) {
                for ($i = 0; $i < $max; $i++) {
                    $line[$i] ??= '';
                }
                ksort($line);
                $rows[] = array_values($line);
            }
        }

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    private static function sharedStrings(array $entries): array
    {
        $xml = $entries['xl/sharedStrings.xml'] ?? false;
        if ($xml === false) {
            return [];
        }

        $shared = simplexml_load_string($xml);
        if (!$shared) {
            return [];
        }

        $shared->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $values = [];
        foreach ($shared->xpath('//m:si') ?: [] as $item) {
            $parts = [];
            $item->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($item->xpath('.//m:t') ?: [] as $text) {
                $parts[] = (string) $text;
            }
            $values[] = implode('', $parts);
        }

        return $values;
    }

    private static function firstSheetPath(array $entries): string
    {
        $workbookXml = $entries['xl/workbook.xml'] ?? false;
        $relsXml = $entries['xl/_rels/workbook.xml.rels'] ?? false;
        if ($workbookXml === false || $relsXml === false) {
            return 'xl/worksheets/sheet1.xml';
        }

        $workbook = simplexml_load_string($workbookXml);
        $rels = simplexml_load_string($relsXml);
        if (!$workbook || !$rels) {
            return 'xl/worksheets/sheet1.xml';
        }

        $workbook->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $workbook->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $sheet = ($workbook->xpath('//m:sheets/m:sheet') ?: [])[0] ?? null;
        if (!$sheet) {
            return 'xl/worksheets/sheet1.xml';
        }

        $attrs = $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $relId = (string) ($attrs['id'] ?? '');
        if ($relId === '') {
            return 'xl/worksheets/sheet1.xml';
        }

        foreach ($rels->Relationship as $rel) {
            if ((string) $rel['Id'] === $relId) {
                $target = (string) $rel['Target'];
                return str_starts_with($target, 'worksheets/')
                    ? 'xl/' . $target
                    : 'xl/worksheets/' . basename($target);
            }
        }

        return 'xl/worksheets/sheet1.xml';
    }

    /**
     * @return array<string, string>
     */
    private static function zipEntries(string $path): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('Arquivo .xlsx não encontrado.');
        }

        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) {
                throw new RuntimeException('Não foi possível abrir o arquivo .xlsx.');
            }

            try {
                $entries = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if ($name !== false) {
                        $content = $zip->getFromIndex($i);
                        if ($content !== false) {
                            $entries[$name] = $content;
                        }
                    }
                }
                return $entries;
            } finally {
                $zip->close();
            }
        }

        $data = file_get_contents($path);
        if ($data === false) {
            throw new RuntimeException('Não foi possível ler o arquivo .xlsx.');
        }

        $eocd = strrpos($data, "\x50\x4b\x05\x06");
        if ($eocd === false) {
            throw new RuntimeException('Arquivo .xlsx inválido.');
        }

        $cdOffset = self::u32($data, $eocd + 16);
        $entries = [];
        $offset = $cdOffset;

        while (substr($data, $offset, 4) === "\x50\x4b\x01\x02") {
            $flags = self::u16($data, $offset + 8);
            $method = self::u16($data, $offset + 10);
            $compressedSize = self::u32($data, $offset + 20);
            $nameLength = self::u16($data, $offset + 28);
            $extraLength = self::u16($data, $offset + 30);
            $commentLength = self::u16($data, $offset + 32);
            $localOffset = self::u32($data, $offset + 42);
            $name = substr($data, $offset + 46, $nameLength);

            if (($flags & 1) === 1) {
                throw new RuntimeException('Planilhas .xlsx protegidas por senha não são suportadas.');
            }

            $localNameLength = self::u16($data, $localOffset + 26);
            $localExtraLength = self::u16($data, $localOffset + 28);
            $contentOffset = $localOffset + 30 + $localNameLength + $localExtraLength;
            $compressed = substr($data, $contentOffset, $compressedSize);

            if ($method === 0) {
                $entries[$name] = $compressed;
            } elseif ($method === 8) {
                $content = gzinflate($compressed);
                if ($content === false) {
                    throw new RuntimeException('Não foi possível descompactar o arquivo .xlsx.');
                }
                $entries[$name] = $content;
            }

            $offset += 46 + $nameLength + $extraLength + $commentLength;
        }

        return $entries;
    }

    private static function u16(string $data, int $offset): int
    {
        return unpack('v', substr($data, $offset, 2))[1];
    }

    private static function u32(string $data, int $offset): int
    {
        return unpack('V', substr($data, $offset, 4))[1];
    }

    private static function cellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) $cell['t'];
        $raw = isset($cell->v) ? (string) $cell->v : '';

        if ($type === 's' && $raw !== '') {
            return $sharedStrings[(int) $raw] ?? '';
        }

        if ($type === 'inlineStr') {
            $cell->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = [];
            foreach ($cell->xpath('.//m:t') ?: [] as $text) {
                $parts[] = (string) $text;
            }
            return implode('', $parts);
        }

        return $raw;
    }

    private static function columnIndex(string $cellRef): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($cellRef));
        $index = 0;
        foreach (str_split($letters ?: 'A') as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }
        return $index;
    }
}
