<?php
require_once __DIR__ . '/quiz_utils.php';

$csv = <<<CSV
"試験年度・回数","出題方式","問題番号","問題文","選択肢1","選択肢2","選択肢3","選択肢4","正答","解説"
"R5-4","○×","1","次の HTML において、ロゴ画像の代替テキストは適切である。\n<p>\n<img src=\"logo.png\" alt=\"インターネットスキル認定普及協会\">\nインターネットスキル認定普及協会\n</p>","正しい","正しくない","","","2","ロゴであることも簡潔に盛り込むべき"
"R5-4","○×","2","title 要素はメタデータコンテンツである。","正しい","正しくない","","","1",""
CSV;

$rows = [];
$handle = fopen('php://temp', 'r+');
fwrite($handle, $csv);
rewind($handle);
while (($row = fgetcsv($handle)) !== false) {
    $rows[] = $row;
}
fclose($handle);

$questions = formatQuestions($rows);
$missingId = false;
foreach ($questions as $q) {
    if (!isset($q['id'])) {
        $missingId = true;
        break;
    }
}
if ($missingId) {
    echo "id field missing\n";
    exit(1);
}

echo "All questions include id field\n";
?>
