<?php
$d = require __DIR__ . '/_test_scenarios_data.php';
$outDir = __DIR__ . '/../docs/audit';
if (!is_dir($outDir)) { mkdir($outDir, 0775, true); }

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csvRow(array $cols): string {
    $out = [];
    foreach ($cols as $c) {
        $c = (string)$c;
        if (strpbrk($c, ";\n\"") !== false) $c = '"' . str_replace('"', '""', $c) . '"';
        $out[] = $c;
    }
    return implode(';', $out);
}

$headers = $d['headers'];
$rows = $d['scenarios'];

// CSV
$csv = "\xEF\xBB\xBF";
$csv .= csvRow(['Bible Adventure v1 - Test Scenarios']) . "\n";
$csv .= csvRow(['Generated', date('Y-m-d H:i:s')]) . "\n\n";
$csv .= csvRow($headers) . "\n";
foreach ($rows as $r) { $csv .= csvRow($r) . "\n"; }
$csv .= "\n";
$csv .= csvRow(['Summary', 'Count']) . "\n";
foreach ($d['summary'] as $s) { $csv .= csvRow($s) . "\n"; }
file_put_contents($outDir . '/bible-adventure-test-scenarios.csv', $csv);

// HTML/XLS
$html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"><style>body{font-family:Calibri,Arial,sans-serif;font-size:11pt}table{border-collapse:collapse;margin-bottom:20px}th,td{border:1px solid #888;padding:6px 8px;vertical-align:top}th{background:#305496;color:#fff}h1{font-size:16pt}.meta{color:#555;margin-bottom:12px}.sheet{page-break-after:always}.summary{background:#D9E1F2;font-weight:bold}</style></head><body>';
$html .= '<h1>Bible Adventure v1 - Test Scenarios</h1><div class="meta">Generated: ' . e(date('Y-m-d H:i:s')) . '</div>';
$html .= '<div class="sheet"><table><tr><th>Summary</th><th>Count</th></tr>';
foreach ($d['summary'] as $s) { $html .= '<tr><td class="summary">' . e($s[0]) . '</td><td>' . e($s[1]) . '</td></tr>'; }
$html .= '</table></div>';
$html .= '<div class="sheet"><table><tr>';
foreach ($headers as $h) $html .= '<th>' . e($h) . '</th>';
$html .= '</tr>';
foreach ($rows as $r) { $html .= '<tr>'; foreach ($r as $c) $html .= '<td>' . e($c) . '</td>'; $html .= '</tr>'; }
$html .= '</table></div></body></html>';
file_put_contents($outDir . '/bible-adventure-test-scenarios.xls', $html);

// XML SpreadsheetML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
$xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
$xml .= '<Worksheet ss:Name="Summary"><Table>';
$xml .= '<Row><Cell><Data ss:Type="String">Summary</Data></Cell><Cell><Data ss:Type="String">Count</Data></Cell></Row>';
foreach ($d['summary'] as $s) { $xml .= '<Row><Cell><Data ss:Type="String">' . e($s[0]) . '</Data></Cell><Cell><Data ss:Type="Number">' . (int)$s[1] . '</Data></Cell></Row>'; }
$xml .= '</Table></Worksheet>';
$xml .= '<Worksheet ss:Name="Test Scenarios"><Table>';
$xml .= '<Row>'; foreach ($headers as $h) { $xml .= '<Cell><Data ss:Type="String">' . e($h) . '</Data></Cell>'; } $xml .= '</Row>';
foreach ($rows as $r) { $xml .= '<Row>'; foreach ($r as $c) { $xml .= '<Cell><Data ss:Type="String">' . e($c) . '</Data></Cell>'; } $xml .= '</Row>'; }
$xml .= '</Table></Worksheet></Workbook>';
file_put_contents($outDir . '/bible-adventure-test-scenarios.xml', $xml);

echo "Test scenario exports written.\n";
