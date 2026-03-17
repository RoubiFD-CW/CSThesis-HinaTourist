<?php

namespace App\Exports;

use App\Models\VisitorLog;

class VAR2PGenerator
{
    /**
     * The fixed 13 attractions exactly as they should appear on the report.
     */
    protected $attractions = [
        ['code' => '104', 'name' => 'ENCHANTED RIVER', 'db_name' => 'Enchanted River'],
        ['code' => '104', 'name' => 'HINATUAN ADVENTURE PARK', 'db_name' => 'Hinatuan Adventure Park'],
        ['code' => '206,408', 'name' => 'LODESTONE SHORES RESORT', 'db_name' => 'Lodestone Shores Resort'],
        ['code' => '206,408', 'name' => 'BACULIN AMAZING SAND BAR', 'db_name' => 'Baculin Amazing Sand Bar'],
        ['code' => '408', 'name' => 'HARIP OCEANSIDE BEACH', 'db_name' => 'Harip Oceanside Beach'],
        ['code' => '206', 'name' => 'ROCK ISLAND RESORT', 'db_name' => 'Rock Island Resort'],
        ['code' => '206', 'name' => 'MAMAON BEACH RESORT', 'db_name' => 'Mamaon Beach Resort'],
        ['code' => '302', 'name' => 'AMPARITAS INTEGRATED NATURE FARM', 'db_name' => 'Amparitas Integrated Nature Farm'],
        ['code' => '206', 'name' => 'SIBADAN FISH CAGE AND RESORT', 'db_name' => 'Sibadan Fish Cage and Resort'],
        ['code' => '123', 'name' => 'LANDONG BAY', 'db_name' => 'Landong Bay'],
        ['code' => '206,408', 'name' => 'DAVINCE HIDDEN PARADISE', 'db_name' => 'Davince Hidden Paradise'],
        ['code' => '104', 'name' => 'TARUSAN COLD SPRING', 'db_name' => 'Tarusan Cold Spring'],
        ['code' => '206', 'name' => 'LLAMAS BEACH RESORT', 'db_name' => 'Llamas Beach Resort'],
        ['code' => '302', 'name' => 'PURO BRIGIDA’S BEACH', 'db_name' => 'Puro Brigida’s Beach'],
        ['code' => '104', 'name' => 'BUNSADAN FALLS', 'db_name' => 'Bunsadan Falls'],
    ];

    /**
     * Map origin strings to our 3 categories.
     * Within the province, Other province, Foreign country residence
     */
    protected function categorizeOrigin($origin)
    {
        $originLower = strtolower(trim($origin));
        if ($originLower === 'within the province') {
            return 'within_province';
        }
        elseif ($originLower === 'other province') {
            return 'other_province';
        }
        elseif ($originLower === 'foreign country residence') {
            return 'foreign';
        }
        // Default to within province if unknown, or maybe other?
        return 'within_province';
    }

    public function generate($year)
    {
        $data = $this->aggregateData($year);
        return $this->buildXlsx($year, $data);
    }

    protected function aggregateData($year)
    {
        // Initialize the structure
        $aggregated = [];
        foreach ($this->attractions as $attraction) {
            $aggregated[$attraction['name']] = [];
            for ($month = 1; $month <= 12; $month++) {
                $aggregated[$attraction['name']][$month] = [
                    'within_province_male' => 0,
                    'within_province_female' => 0,
                    'other_province_male' => 0,
                    'other_province_female' => 0,
                    'foreign_male' => 0,
                    'foreign_female' => 0,
                ];
            }
        }

        // Fetch logs for the year that have a dedicated_area
        $logs = VisitorLog::whereYear('visit_date', $year)
            ->whereNotNull('dedicated_area')
            ->get();

        foreach ($logs as $log) {
            $month = (int)$log->visit_date->format('n');
            $category = $this->categorizeOrigin($log->origin);

            // Find the attraction code
            $attrName = null;
            foreach ($this->attractions as $attr) {
                if (strtolower($attr['db_name']) === strtolower($log->dedicated_area)) {
                    $attrName = $attr['name'];
                    break;
                }
            }

            if ($attrName && isset($aggregated[$attrName][$month])) {
                $aggregated[$attrName][$month]["{$category}_male"] += (int)$log->male_count;
                $aggregated[$attrName][$month]["{$category}_female"] += (int)$log->female_count;
            }
        }

        return $aggregated;
    }

    protected function buildXlsx($year, $aggregated)
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $xb = new XlsxBuilder();

        $xb->addCell(1, 1, 'Tourism Attraction Visitor Record', 1, 's', 1, 7);
        $xb->addCell(1, 15, 'VAR 2P', 1);
        $xb->addCell(2, 1, '( This recording form can be used instead of just counting the visitors )', 0, 's', 1, 5);
        $xb->addCell(4, 3, 'Month/Year :', 3);
        $xb->addCell(4, 4, $year, 2, 's', 1, 7);
        $xb->addCell(5, 3, 'Name of Province :', 3);
        $xb->addCell(5, 4, 'Surigao del Sur', 2, 's', 1, 7);
        $xb->addCell(7, 1, 'Visitor Attraction', 4, 's', 1, 4);
        $xb->addCell(7, 5, '*** Place of Residence', 4, 's', 1, 9);
        $xb->addCell(7, 14, '* Grand Total Number of Visitors', 4, 's', 3, 3);
        $xb->addCell(8, 1, 'LGU', 4, 's', 3, 1);
        $xb->addCell(8, 2, 'NAME', 4, 's', 3, 1);
        $xb->addCell(8, 3, 'Attraction Code', 4, 's', 3, 1);
        $xb->addCell(8, 4, '', 4, 's', 3, 1);
        $xb->addCell(8, 5, 'Philippines', 4, 's', 1, 6);
        $xb->addCell(8, 11, 'Foreign Country Residence', 4, 's', 2, 3);
        $xb->addCell(9, 5, 'WITHIN THE PROVINCE', 4, 's', 1, 3);
        $xb->addCell(9, 8, 'OTHER PROVINCE', 4, 's', 1, 3);
        $c = 5;
        for ($i = 0; $i < 4; $i++) {
            $xb->addCell(10, $c++, 'Male', 4);
            $xb->addCell(10, $c++, 'Female', 4);
            $xb->addCell(10, $c++, 'Total', 4);
        }

        $r = 11;
        foreach ($this->attractions as $attraction) {
            $code = $attraction['code'];
            $data = $aggregated[$attraction['name']];
            $sub = ['wp_m' => 0, 'wp_f' => 0, 'wp_t' => 0, 'op_m' => 0, 'op_f' => 0, 'op_t' => 0, 'f_m' => 0, 'f_f' => 0, 'f_t' => 0, 'gt_m' => 0, 'gt_f' => 0, 'gt_t' => 0];

            for ($month = 1; $month <= 12; $month++) {
                $row = $data[$month];
                $wp_m = $row['within_province_male'];
                $wp_f = $row['within_province_female'];
                $wp_t = $wp_m + $wp_f;
                $op_m = $row['other_province_male'];
                $op_f = $row['other_province_female'];
                $op_t = $op_m + $op_f;
                $f_m = $row['foreign_male'];
                $f_f = $row['foreign_female'];
                $f_t = $f_m + $f_f;
                $gt_m = $wp_m + $op_m + $f_m;
                $gt_f = $wp_f + $op_f + $f_f;
                $gt_t = $wp_t + $op_t + $f_t;

                $sub['wp_m'] += $wp_m;
                $sub['wp_f'] += $wp_f;
                $sub['wp_t'] += $wp_t;
                $sub['op_m'] += $op_m;
                $sub['op_f'] += $op_f;
                $sub['op_t'] += $op_t;
                $sub['f_m'] += $f_m;
                $sub['f_f'] += $f_f;
                $sub['f_t'] += $f_t;
                $sub['gt_m'] += $gt_m;
                $sub['gt_f'] += $gt_f;
                $sub['gt_t'] += $gt_t;

                if ($month === 1) {
                    $xb->addCell($r, 1, 'HINATUAN', 5, 's', 12, 1);
                    $xb->addCell($r, 2, $attraction['name'], 6, 's', 12, 1);
                    $xb->addCell($r, 3, $code, 5, 's', 12, 1);
                    $xb->addCell($r, 4, $months[$month], 5, 's', 1, 1);
                }
                else {
                    $xb->addCell($r, 4, $months[$month], 5);
                }

                $xb->addCell($r, 5, $wp_m, 5, 'n');
                $xb->addCell($r, 6, $wp_f, 5, 'n');
                $xb->addCell($r, 7, $wp_t, 6, 'n');
                $xb->addCell($r, 8, $op_m, 5, 'n');
                $xb->addCell($r, 9, $op_f, 5, 'n');
                $xb->addCell($r, 10, $op_t, 6, 'n');
                $xb->addCell($r, 11, $f_m, 5, 'n');
                $xb->addCell($r, 12, $f_f, 5, 'n');
                $xb->addCell($r, 13, $f_t, 6, 'n');
                $xb->addCell($r, 14, $gt_m, 5, 'n');
                $xb->addCell($r, 15, $gt_f, 5, 'n');
                $xb->addCell($r, 16, $gt_t, 6, 'n');
                $r++;
            }
            $xb->addCell($r, 1, 'Sub - Total', 2, 's', 1, 4);
            $xb->addCell($r, 5, $sub['wp_m'], 7, 'n');
            $xb->addCell($r, 6, $sub['wp_f'], 7, 'n');
            $xb->addCell($r, 7, $sub['wp_t'], 7, 'n');
            $xb->addCell($r, 8, $sub['op_m'], 7, 'n');
            $xb->addCell($r, 9, $sub['op_f'], 7, 'n');
            $xb->addCell($r, 10, $sub['op_t'], 7, 'n');
            $xb->addCell($r, 11, $sub['f_m'], 7, 'n');
            $xb->addCell($r, 12, $sub['f_f'], 7, 'n');
            $xb->addCell($r, 13, $sub['f_t'], 7, 'n');
            $xb->addCell($r, 14, $sub['gt_m'], 7, 'n');
            $xb->addCell($r, 15, $sub['gt_f'], 7, 'n');
            $xb->addCell($r, 16, $sub['gt_t'], 7, 'n');
            $r++;
        }
        return $xb->build();
    }
}

class XlsxBuilder
{
    private $cells = [];
    private $merges = [];
    private $maxRow = 0;

    public function addCell($r, $c, $val, $style, $type = 's', $rowspan = 1, $colspan = 1)
    {
        for ($i = 0; $i < $rowspan; $i++) {
            for ($j = 0; $j < $colspan; $j++) {
                if ($i == 0 && $j == 0)
                    $this->cells[$r + $i][$c + $j] = ['val' => $val, 'style' => $style, 'type' => $type];
                else
                    $this->cells[$r + $i][$c + $j] = ['val' => '', 'style' => $style, 'type' => 's'];
            }
        }
        if ($rowspan > 1 || $colspan > 1)
            $this->merges[] = $this->colLetter($c) . $r . ':' . $this->colLetter($c + $colspan - 1) . ($r + $rowspan - 1);
        $endRow = $r + $rowspan - 1;
        if ($endRow > $this->maxRow)
            $this->maxRow = $endRow;
    }

    private function colLetter($c)
    {
        if ($c <= 26)
            return chr(64 + $c);
        return chr(64 + floor(($c - 1) / 26)) . chr(65 + (($c - 1) % 26));
    }

    public function build()
    {
        $zip = new \ZipArchive();
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip->open($tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="VAR_2P" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="5"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="10"/><name val="Arial"/></font><font><sz val="10"/><name val="Arial"/></font><font><sz val="9"/><name val="Arial"/></font><font><b/><sz val="9"/><name val="Arial"/></font></fonts><fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFFF00"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFCD5B4"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="3"><border><left/><right/><top/><bottom/><diagonal/></border><border><left/><right/><top/><bottom style="thin"><color auto="1"/></bottom><diagonal/></border><border><left style="medium"><color auto="1"/></left><right style="thin"><color auto="1"/></right><top style="thin"><color auto="1"/></top><bottom style="thin"><color auto="1"/></bottom><diagonal/></border></borders><cellXfs count="8"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/><xf numFmtId="0" fontId="1" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf><xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="3" fillId="0" borderId="2" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf><xf numFmtId="0" fontId="4" fillId="0" borderId="2" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf><xf numFmtId="0" fontId="1" fillId="3" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf></cellXfs></styleSheet>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->getSheet());
        $zip->close();
        $content = file_get_contents($tmp);
        unlink($tmp);
        return $content;
    }

    private function getSheet()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $xml .= '<cols><col min="1" max="1" width="12" customWidth="1"/><col min="2" max="2" width="38" customWidth="1"/><col min="3" max="3" width="16" customWidth="1"/><col min="4" max="4" width="12" customWidth="1"/><col min="5" max="16" width="9" customWidth="1"/></cols>';
        $xml .= '<sheetData>' . "\n";
        for ($r = 1; $r <= $this->maxRow; $r++) {
            if (!isset($this->cells[$r]))
                continue;
            $xml .= '<row r="' . $r . '">';
            ksort($this->cells[$r]);
            foreach ($this->cells[$r] as $c => $cell) {
                $ref = $this->colLetter($c) . $r;
                $s = $cell['style'] ? ' s="' . $cell['style'] . '"' : '';
                if ($cell['val'] === '')
                    $xml .= '<c r="' . $ref . '"' . $s . '/>';
                elseif ($cell['type'] === 'n')
                    $xml .= '<c r="' . $ref . '"' . $s . '><v>' . $cell['val'] . '</v></c>';
                else
                    $xml .= '<c r="' . $ref . '"' . $s . ' t="inlineStr"><is><t>' . htmlspecialchars($cell['val']) . '</t></is></c>';
            }
            $xml .= '</row>' . "\n";
        }
        $xml .= '</sheetData>';
        if (!empty($this->merges)) {
            $xml .= '<mergeCells count="' . count($this->merges) . '">';
            foreach ($this->merges as $merge)
                $xml .= '<mergeCell ref="' . $merge . '"/>';
            $xml .= '</mergeCells>';
        }
        $xml .= '</worksheet>';
        return $xml;
    }
}
