<?php
$csv = <<<CSV
"試験年度・回数","出題方式","問題番号","問題文","選択肢1","選択肢2","選択肢3","選択肢4","正答","解説"
"R5-4","○×","1","次の HTML において、ロゴ画像の代替テキストは適切である。
<p>
<img src=""logo.png"" alt=""インターネットスキル認定普及協会"">
インターネットスキル認定普及協会
</p>","正しい","正しくない","","","2","ロゴであることも簡潔に盛り込むべき"
"R5-4","○×","2","title 要素はメタデータコンテンツである。","正しい","正しくない","","","1",""
CSV;

$handle = fopen('php://temp', 'r+');
fwrite($handle, $csv);
rewind($handle);

$i = 0;
while (($row = fgetcsv($handle)) !== false) {
    if ($i++ === 0) continue;
    echo "---\n";
    echo 'Question: ' . $row[3] . "\n";
    echo 'Explanation: ' . $row[9] . "\n";
}

fclose($handle);
?>
