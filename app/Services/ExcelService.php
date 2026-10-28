<?php
/**
 * Excel Service - Native OpenXML (.xlsx) & CSV Generator & Parser
 * NAVASENA MANAGEMENT — SMKN 1 PANYINGKIRAN
 * Pure Vanilla PHP (Zero Third-Party Dependencies)
 * 100% ECMA-376 OpenXML Compliant
 */

namespace App\Services;

use ZipArchive;
use Exception;

class ExcelService
{
    /**
     * Header & Kolom Baku Template Impor Siswa
     */
    public static function getTemplateHeaders(): array
    {
        return [
            'nis',
            'nama_lengkap',
            'kelas',
            'jenis_kelamin',
            'telepon',
            'email'
        ];
    }

    /**
     * Contoh Data Dummy Realistis untuk Panduan Pengisian
     */
    public static function getSampleRows(): array
    {
        return [
            ['20261001', 'Ahmad Fauzi', 'XII RPL 1', 'L', '081234567801', 'ahmad.fauzi@smkn1panyingkiran.sch.id'],
            ['20261002', 'Siti Nurhaliza', 'XII RPL 1', 'P', '081234567802', 'siti.nurhaliza@smkn1panyingkiran.sch.id'],
            ['20261003', 'Dimas Anggara', 'XII TKJ 1', 'L', '081234567803', 'dimas.anggara@smkn1panyingkiran.sch.id'],
            ['20261004', 'Dewi Lestari', 'XII AKL 1', 'P', '081234567804', 'dewi.lestari@smkn1panyingkiran.sch.id'],
            ['20261005', 'Rizky Pratama', 'XII TBSM 1', 'L', '081234567805', 'rizky.pratama@smkn1panyingkiran.sch.id']
        ];
    }

    /**
     * Membuat berkas template .xlsx murni (OpenXML Spreadsheet)
     * Format 100% kompatibel dengan MS Excel, WPS Office, Google Sheets, & LibreOffice
     */
    public static function createTemplateXlsx(string $filePath): bool
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $headers = self::getTemplateHeaders();
        $sampleData = self::getSampleRows();

        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $strings = [];
        $stringIndex = [];

        $getStringId = function($str) use (&$strings, &$stringIndex) {
            $str = (string)$str;
            if (!isset($stringIndex[$str])) {
                $idx = count($strings);
                $strings[] = $str;
                $stringIndex[$str] = $idx;
            }
            return $stringIndex[$str];
        };

        $allRows = array_merge([$headers], $sampleData);
        foreach ($allRows as $r) {
            foreach ($r as $val) {
                $getStringId($val);
            }
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedString+xml"/>' .
            '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>' .
            '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<bookViews><workbookView xWindow="0" yWindow="0" windowWidth="22000" windowHeight="12000"/></bookViews>' .
            '<sheets><sheet name="Data Siswa" sheetId="1" r:id="rId1"/></sheets>' .
            '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml - 100% ECMA-376 Compliant
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<fonts count="2">' .
            '<font><sz val="11"/><color rgb="FF0F172A"/><name val="Calibri"/><family val="2"/></font>' .
            '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>' .
            '</fonts>' .
            '<fills count="3">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill><patternFill patternType="solid"><fgColor rgb="FF0B101D"/><bgColor indexed="64"/></patternFill></fill>' .
            '</fills>' .
            '<borders count="2">' .
            '<border><left/><right/><top/><bottom/><diagonal/></border>' .
            '<border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="3">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' .
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>' .
            '</cellXfs>' .
            '<cellStyles count="1">' .
            '<cellStyle name="Normal" xfId="0" builtinId="0"/>' .
            '</cellStyles>' .
            '<dxfs count="0"/>' .
            '<tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/>' .
            '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/sharedStrings.xml
        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
        foreach ($strings as $s) {
            $ssXml .= '<si><t xml:space="preserve">' . htmlspecialchars($s, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        $ssXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);

        // 7. xl/worksheets/sheet1.xml
        $rowCount = count($allRows);
        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<dimension ref="A1:F' . $rowCount . '"/>' .
            '<sheetViews><sheetView tabSelected="1" workbookViewId="0"/></sheetViews>' .
            '<sheetFormatPr defaultRowHeight="18"/>' .
            '<cols>' .
            '<col min="1" max="1" width="18" customWidth="1"/>' .
            '<col min="2" max="2" width="30" customWidth="1"/>' .
            '<col min="3" max="3" width="18" customWidth="1"/>' .
            '<col min="4" max="4" width="16" customWidth="1"/>' .
            '<col min="5" max="5" width="22" customWidth="1"/>' .
            '<col min="6" max="6" width="36" customWidth="1"/>' .
            '</cols>' .
            '<sheetData>';

        $colLetters = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($allRows as $rowIdx => $row) {
            $rowNum = $rowIdx + 1;
            $styleId = ($rowIdx === 0) ? '1' : '2';
            $sheetXml .= '<row r="' . $rowNum . '" ht="20" customHeight="1">';
            foreach ($row as $colIdx => $val) {
                $cellRef = $colLetters[$colIdx] . $rowNum;
                $sId = $getStringId($val);
                $sheetXml .= '<c r="' . $cellRef . '" t="s" s="' . $styleId . '"><v>' . $sId . '</v></c>';
            }
            $sheetXml .= '</row>';
        }

        $sheetXml .= '</sheetData>' .
            '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>' .
            '</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
        return true;
    }

    /**
     * Membuat berkas template .csv dengan UTF-8 BOM
     */
    public static function createTemplateCsv(string $filePath): bool
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $handle = fopen($filePath, 'w');
        if (!$handle) {
            return false;
        }

        // Tulis UTF-8 BOM agar Excel membukanya langsung secara rapi
        fwrite($handle, "\xEF\xBB\xBF");

        $headers = self::getTemplateHeaders();
        fputcsv($handle, $headers, ',');

        foreach (self::getSampleRows() as $row) {
            fputcsv($handle, $row, ',');
        }

        fclose($handle);
        return true;
    }

    /**
     * Helper untuk mengonversi huruf kolom Excel ('A', 'B', 'AA', dll.) ke indeks 0-based
     */
    public static function colLetterToIndex(string $letters): int
    {
        $letters = strtoupper(preg_replace('/[^A-Z]/', '', $letters));
        $len = strlen($letters);
        $num = 0;
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($letters[$i]) - 64);
        }
        return max(0, $num - 1);
    }

    /**
     * Membaca isi berkas .xlsx murni dan mengembalikan array 2D baris
     * Tangguh terhadap perbedaan case, cell coordinate gaps, inline strings, & rich-text
     */
    public static function readXlsx(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Cari file sharedStrings.xml (case-insensitive)
        $sharedStrings = [];
        $ssIndex = -1;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (strcasecmp($entryName, 'xl/sharedStrings.xml') === 0) {
                $ssIndex = $i;
                break;
            }
        }

        if ($ssIndex !== -1) {
            $ssContent = $zip->getFromIndex($ssIndex);
            if ($ssContent) {
                $xml = @simplexml_load_string($ssContent);
                if ($xml && isset($xml->si)) {
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string)$si->t;
                        } elseif (isset($si->r)) {
                            $text = '';
                            foreach ($si->r as $r) {
                                $text .= (string)$r->t;
                            }
                            $sharedStrings[] = $text;
                        } else {
                            $sharedStrings[] = '';
                        }
                    }
                }
            }
        }

        // 2. Cari file worksheet pertama (xl/worksheets/sheet1.xml atau sheet*.xml)
        $sheetContent = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (preg_match('#^xl/worksheets/sheet[0-9]+\.xml$#i', $entryName)) {
                $sheetContent = $zip->getFromIndex($i);
                break;
            }
        }

        $rows = [];
        if ($sheetContent) {
            $xml = @simplexml_load_string($sheetContent);
            if ($xml && isset($xml->sheetData->row)) {
                foreach ($xml->sheetData->row as $row) {
                    $rowCells = [];
                    $maxColIdx = -1;

                    foreach ($row->c as $c) {
                        $cellRef = (string)$c['r'];
                        // Ekstrak huruf kolom (misal 'A' dari 'A2')
                        $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                        $colIdx = self::colLetterToIndex($colLetters);
                        if ($colIdx > $maxColIdx) {
                            $maxColIdx = $colIdx;
                        }

                        $type = (string)$c['t'];
                        $val = (string)$c->v;

                        if ($type === 's' && isset($sharedStrings[(int)$val])) {
                            $val = $sharedStrings[(int)$val];
                        } elseif ($type === 'inlineStr') {
                            if (isset($c->is->t)) {
                                $val = (string)$c->is->t;
                            } elseif (isset($c->is->r)) {
                                $text = '';
                                foreach ($c->is->r as $r) {
                                    $text .= (string)$r->t;
                                }
                                $val = $text;
                            }
                        }

                        $rowCells[$colIdx] = trim($val);
                    }

                    // Susun array terurut dari kolom 0 s/d maxColIdx
                    $rowVals = [];
                    for ($k = 0; $k <= $maxColIdx; $k++) {
                        $rowVals[$k] = $rowCells[$k] ?? '';
                    }

                    // Lewati baris yang seluruhnya kosong
                    if (!empty(array_filter($rowVals, fn($v) => $v !== ''))) {
                        $rows[] = $rowVals;
                    }
                }
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Mengonversi berkas .xlsx ke berkas .csv sementara untuk diolah oleh ImportService
     */
    public static function convertXlsxToCsv(string $xlsxPath, ?string $targetCsvPath = null)
    {
        $rows = self::readXlsx($xlsxPath);
        if (empty($rows)) {
            return false;
        }

        $isAutoPath = false;
        if ($targetCsvPath === null || $targetCsvPath === '') {
            $targetCsvPath = sys_get_temp_dir() . '/navasena_xlsx_' . uniqid() . '.csv';
            $isAutoPath = true;
        }

        $handle = fopen($targetCsvPath, 'w');
        if (!$handle) {
            return false;
        }

        fwrite($handle, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($handle, $row, ',');
        }

        fclose($handle);
        return $isAutoPath ? $targetCsvPath : true;
    }
}
