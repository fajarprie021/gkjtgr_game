<?php
/**
 * Generate HTML-based .xls file — opens with prompt in Excel/LibreOffice.
 * Uses CSS classes for colors, page-break-after between sheets.
 */

$d = require __DIR__ . '/_audit_data.php';

$outDir = __DIR__ . '/../docs/audit';
if (!is_dir($outDir)) { mkdir($outDir, 0775, true); }
$out = $outDir . '/bible-adventure-audit.xls';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function clsStatus($s) {
    $map = ['Lulus'=>'clsLulus','Partial'=>'clsPartial','Gagal'=>'clsGagal','Belum Dicek'=>'clsBelum','N/A'=>'clsNA'];
    return $map[$s] ?? 'clsWrap';
}
function clsSev($s) {
    $map = ['Major'=>'clsMajor','Medium'=>'clsMedium','Minor'=>'clsMinor'];
    return $map[$s] ?? 'clsWrap';
}

$rows = $d['audit'];
$total = count($rows);
$lulus = count(array_filter($rows, fn($r) => $r[5] === 'Lulus'));
$partial = count(array_filter($rows, fn($r) => $r[5] === 'Partial'));
$gagal = count(array_filter($rows, fn($r) => $r[5] === 'Gagal'));
$belum = count(array_filter($rows, fn($r) => $r[5] === 'Belum Dicek'));
$na = count(array_filter($rows, fn($r) => $r[5] === 'N/A'));

$now = date('Y-m-d H:i:s');

$css = '<style>'
      . 'body{font-family:Calibri,Arial,sans-serif;font-size:11pt;}'
      . 'table{border-collapse:collapse;margin-bottom:20px;}'
      . 'th,td{border:1px solid #888;padding:6px 8px;vertical-align:top;}'
      . 'th{background:#305496;color:#fff;font-weight:bold;text-align:left;}'
      . '.title{font-size:16pt;font-weight:bold;margin-bottom:8px;}'
      . '.meta{color:#555;margin-bottom:12px;}'
      . '.clsLulus{background:#C6EFCE;}'
      . '.clsPartial{background:#FFEB9C;}'
      . '.clsGagal{background:#FFC7CE;}'
      . '.clsBelum{background:#DDDDDD;}'
      . '.clsNA{background:#E0E0E0;}'
      . '.clsMajor{background:#FFC7CE;font-weight:bold;text-align:center;}'
      . '.clsMedium{background:#FFEB9C;text-align:center;}'
      . '.clsMinor{background:#C6EFCE;text-align:center;}'
      . '.sheetBreak{page-break-after:always;}'
      . '.summaryLabel{font-weight:bold;background:#D9E1F2;}'
      . '</style>';

$html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" '
       . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
       . 'xmlns="http://www.w3.org/TR/REC-html40">'
       . '<head><meta charset="UTF-8"><title>Bible Adventure Audit</title>'
       . $css . '</head><body>' . "\n";

// === Sheet 1: Summary ===
$html .= '<div class="sheetBreak">';
$html .= '<div class="title">Bible Adventure v1 — Audit Hasil</div>';
$html .= '<div class="meta">Tanggal: ' . h($now) . ' &nbsp;|&nbsp; Mesin: 127.0.0.1:8000 (PHP built-in) &nbsp;|&nbsp; Total 30 item diaudit</div>';
$html .= '<table><tr><th>Metrik</th><th>Jumlah</th></tr>';
foreach ([
    ['Total item diaudit', $total],
    ['Lulus', $lulus],
    ['Partial', $partial],
    ['Gagal', $gagal],
    ['Belum Dicek', $belum],
    ['N/A', $na],
] as $s) {
    $html .= '<tr><td class="summaryLabel">' . h($s[0]) . '</td><td>' . (int)$s[1] . '</td></tr>';
}
$html .= '</table></div>' . "\n";

// === Sheet 2: Audit 30 Item ===
$colWidths = [60, 180, 220, 240, 480, 90, 80, 320, 280, 380, 140, 280];
$html .= '<div class="sheetBreak"><table>';
$html .= '<colgroup>';
foreach ($colWidths as $w) { $html .= '<col style="width:' . $w . 'px">'; }
$html .= '</colgroup><thead><tr>';
foreach ($d['headers'] as $h) { $html .= '<th>' . h($h) . '</th>'; }
$html .= '</tr></thead><tbody>';
foreach ($rows as $r) {
    $html .= '<tr>';
    foreach ($r as $idx => $v) {
        $cls = ($idx === 5) ? clsStatus($v) : (($idx === 6) ? clsSev($v) : 'clsWrap');
        $html .= '<td class="' . $cls . '">' . h($v) . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</tbody></table></div>' . "\n";

// === Sheet 3: Placeholder Pages ===
$html .= '<div class="sheetBreak"><table>';
$html .= '<colgroup><col style="width:60px"><col style="width:280px"><col style="width:320px"><col style="width:80px"><col style="width:200px"></colgroup>';
$html .= '<thead><tr>';
foreach ($d['placeholder_headers'] as $h) { $html .= '<th>' . h($h) . '</th>'; }
$html .= '</tr></thead><tbody>';
foreach ($d['placeholders'] as $p) {
    $html .= '<tr>';
    foreach ($p as $idx => $v) {
        $cls = ($idx === 3) ? clsSev($v) : 'clsWrap';
        $html .= '<td class="' . $cls . '">' . h($v) . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</tbody></table></div>' . "\n";

// === Sheet 4: Ringkasan A-030 ===
$html .= '<table>';
$html .= '<colgroup><col style="width:280px"><col style="width:160px"><col style="width:380px"></colgroup>';
$html .= '<thead><tr><th>Modul</th><th>Status</th><th>Catatan</th></tr></thead><tbody>';
foreach ($d['summaries'] as $s) {
    $html .= '<tr><td class="clsWrap">' . h($s[0]) . '</td><td class="clsWrap">' . h($s[1]) . '</td><td class="clsWrap">' . h($s[2]) . '</td></tr>';
}
$html .= '</tbody></table>' . "\n";

$html .= '</body></html>' . "\n";

file_put_contents($out, $html);
echo "XLS written: {$out} (" . number_format(filesize($out)) . " bytes)\n";

$belum = count(array_filter($rows, fn($r) => $r[5] === 'Belum Dicek'));
$na = count(array_filter($rows, fn($r) => $r[5] === 'N/A'));

$now = date('Y-m-d H:i:s');
