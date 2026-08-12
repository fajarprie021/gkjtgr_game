<?php
/**
 * Generate SpreadsheetML 2003 XML — opens in Excel/LibreOffice/Google Sheets.
 * Multi-sheet, warna per status/severity, formatted cells.
 */

require_once __DIR__ . '/_audit_data.php';

$d = require __DIR__ . '/_audit_data.php';
$outDir = __DIR__ . '/../docs/audit';
if (!is_dir($outDir)) { mkdir($outDir, 0775, true); }
$out = $outDir . '/bible-adventure-audit.xml';

function esc($s) {
    return htmlspecialchars((string)$s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function statusStyle($s) {
    $map = ['Lulus'=>'lulus','Partial'=>'partial','Gagal'=>'gagal','Belum Dicek'=>'belum','N/A'=>'na'];
    return $map[$s] ?? 'wrap';
}
function sevStyle($s) {
    $map = ['Major'=>'major','Medium'=>'medium','Minor'=>'minor'];
    return $map[$s] ?? 'wrap';
}

$rows = $d['audit'];
$total = count($rows);
$lulus = count(array_filter($rows, fn($r) => $r[5] === 'Lulus'));
$partial = count(array_filter($rows, fn($r) => $r[5] === 'Partial'));
$gagal = count(array_filter($rows, fn($r) => $r[5] === 'Gagal'));
$belum = count(array_filter($rows, fn($r) => $r[5] === 'Belum Dicek'));
$na = count(array_filter($rows, fn($r) => $r[5] === 'N/A'));

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
$xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
      . 'xmlns:o="urn:schemas-microsoft-com:office:office" '
      . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
      . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" '
      . 'xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

$xml .= '<Styles>'
      . '<Style ss:ID="title"><Font ss:Bold="1" ss:Size="14"/></Style>'
      . '<Style ss:ID="hdr"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#305496" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="subhdr"><Font ss:Bold="1"/><Interior ss:Color="#D9E1F2" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="wrap"><Alignment ss:Vertical="Top" ss:WrapText="1"/></Style>'
      . '<Style ss:ID="lulus"><Alignment ss:Vertical="Top" ss:WrapText="1"/><Interior ss:Color="#C6EFCE" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="partial"><Alignment ss:Vertical="Top" ss:WrapText="1"/><Interior ss:Color="#FFEB9C" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="gagal"><Alignment ss:Vertical="Top" ss:WrapText="1"/><Interior ss:Color="#FFC7CE" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="belum"><Alignment ss:Vertical="Top" ss:WrapText="1"/><Interior ss:Color="#DDDDDD" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="na"><Alignment ss:Vertical="Top" ss:WrapText="1"/><Interior ss:Color="#E0E0E0" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="major"><Alignment ss:Vertical="Top" ss:WrapText="1" ss:Horizontal="Center"/><Font ss:Bold="1"/><Interior ss:Color="#FFC7CE" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="medium"><Alignment ss:Vertical="Top" ss:WrapText="1" ss:Horizontal="Center"/><Interior ss:Color="#FFEB9C" ss:Pattern="Solid"/></Style>'
      . '<Style ss:ID="minor"><Alignment ss:Vertical="Top" ss:WrapText="1" ss:Horizontal="Center"/><Interior ss:Color="#C6EFCE" ss:Pattern="Solid"/></Style>'
      . '</Styles>' . "\n";

// === Sheet 1: Summary ===
$xml .= '<Worksheet ss:Name="Summary"><Table>' . "\n";
$xml .= '<Row><Cell ss:StyleID="title"><Data ss:Type="String">Bible Adventure v1 — Audit Hasil</Data></Cell></Row>' . "\n";
$xml .= '<Row><Cell><Data ss:Type="String">Tanggal</Data></Cell><Cell><Data ss:Type="String">' . date('Y-m-d H:i:s') . '</Data></Cell></Row>' . "\n";
$xml .= '<Row><Cell><Data ss:Type="String">Mesin</Data></Cell><Cell><Data ss:Type="String">127.0.0.1:8000 (PHP built-in)</Data></Cell></Row>' . "\n";
$xml .= '<Row/>' . "\n";
$xml .= '<Row><Cell ss:StyleID="hdr"><Data ss:Type="String">Metrik</Data></Cell><Cell ss:StyleID="hdr"><Data ss:Type="Number">Jumlah</Data></Cell></Row>' . "\n";
foreach ([
    ['Total item diaudit', $total],
    ['Lulus', $lulus],
    ['Partial', $partial],
    ['Gagal', $gagal],
    ['Belum Dicek', $belum],
    ['N/A', $na],
] as $s) {
    $xml .= '<Row><Cell><Data ss:Type="String">' . esc($s[0]) . '</Data></Cell><Cell><Data ss:Type="Number">' . (int)$s[1] . '</Data></Cell></Row>' . "\n";
}
$xml .= '</Table></Worksheet>' . "\n";

// === Sheet 2: Audit 30 Item ===
$headers = $d['headers'];
$colWidths = [60, 180, 220, 240, 480, 90, 80, 320, 280, 380, 140, 280];
$xml .= '<Worksheet ss:Name="Audit 30 Item"><Table>' . "\n";
foreach ($colWidths as $i => $w) {
    $xml .= '<Column ss:Index="' . ($i + 1) . '" ss:Width="' . $w . '"/>' . "\n";
}
$xml .= '<Row>';
foreach ($headers as $h) {
    $xml .= '<Cell ss:StyleID="hdr"><Data ss:Type="String">' . esc($h) . '</Data></Cell>';
}
$xml .= '</Row>' . "\n";
foreach ($rows as $r) {
    $xml .= '<Row>';
    foreach ($r as $idx => $v) {
        $style = 'wrap';
        if ($idx === 5) $style = statusStyle($v);
        elseif ($idx === 6) $style = sevStyle($v);
        $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . esc($v) . '</Data></Cell>';
    }
    $xml .= '</Row>' . "\n";
}
$xml .= '</Table></Worksheet>' . "\n";

// === Sheet 3: Placeholder Pages ===
$xml .= '<Worksheet ss:Name="Placeholder Pages"><Table>' . "\n";
$xml .= '<Column ss:Index="1" ss:Width="60"/><Column ss:Index="2" ss:Width="280"/>'
      . '<Column ss:Index="3" ss:Width="320"/><Column ss:Index="4" ss:Width="80"/>'
      . '<Column ss:Index="5" ss:Width="200"/>' . "\n";
$xml .= '<Row>';
foreach ($d['placeholder_headers'] as $h) {
    $xml .= '<Cell ss:StyleID="hdr"><Data ss:Type="String">' . esc($h) . '</Data></Cell>';
}
$xml .= '</Row>' . "\n";
foreach ($d['placeholders'] as $p) {
    $xml .= '<Row>';
    foreach ($p as $idx => $v) {
        $style = ($idx === 3) ? sevStyle($v) : 'wrap';
        $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . esc($v) . '</Data></Cell>';
    }
    $xml .= '</Row>' . "\n";
}
$xml .= '</Table></Worksheet>' . "\n";

// === Sheet 4: Ringkasan A-030 ===
$xml .= '<Worksheet ss:Name="Ringkasan A-030"><Table>' . "\n";
$xml .= '<Column ss:Index="1" ss:Width="280"/><Column ss:Index="2" ss:Width="160"/><Column ss:Index="3" ss:Width="380"/>' . "\n";
$xml .= '<Row>';
foreach (['Modul','Status','Catatan'] as $h) {
    $xml .= '<Cell ss:StyleID="hdr"><Data ss:Type="String">' . esc($h) . '</Data></Cell>';
}
$xml .= '</Row>' . "\n";
foreach ($d['summaries'] as $s) {
    $xml .= '<Row><Cell ss:StyleID="wrap"><Data ss:Type="String">' . esc($s[0]) . '</Data></Cell>'
          . '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . esc($s[1]) . '</Data></Cell>'
          . '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . esc($s[2]) . '</Data></Cell></Row>' . "\n";
}
$xml .= '</Table></Worksheet>' . "\n";

$xml .= '</Workbook>' . "\n";

file_put_contents($out, $xml);
echo "XML written: {$out} (" . number_format(filesize($out)) . " bytes)\n";
