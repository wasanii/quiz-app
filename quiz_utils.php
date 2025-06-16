<?php
function fetchCsvData(string $url): array {
    $csvData = @file_get_contents($url);
    if ($csvData === false) {
        return [];
    }

    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $csvData);
    rewind($handle);

    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
        $rows[] = $row;
    }
    fclose($handle);

    return $rows;
}

function formatMeta(string $yearRound, string $number): string {
    if (preg_match('/R(\d+)-(\d+)/', $yearRound, $m)) {
        $yearRound = '令和' . intval($m[1]) . '年度 第' . intval($m[2]) . '回';
    }
    $num = $number !== '' ? ' 第' . intval($number) . '問' : '';
    return trim($yearRound . $num);
}

function formatQuestions(array $rows): array {
    $formatted = [];
    $id = 1;
    foreach ($rows as $i => $row) {
        if ($i === 0) {
            continue; // header
        }

        $mode = $row[1] ?? '';
        $questionText = $row[3] ?? '';
        if ($questionText === '') {
            continue;
        }

        $meta = formatMeta($row[0] ?? '', $row[2] ?? '');

        if ($mode === '○×') {
            $choiceCols = array_slice($row, 4, 2);
        } else {
            $choiceCols = array_slice($row, 4, 4);
        }
        $choices = array_values(array_filter($choiceCols, fn($c) => $c !== ''));
        $answer = $row[8] ?? '';
        $explanation = $row[9] ?? '';

        $formatted[] = [
            'id'          => $id++,
            'question'    => $questionText,
            'choices'     => $choices,
            'answer'      => $answer,
            'explanation' => $explanation,
            'meta'        => $meta,
        ];
    }

    shuffle($formatted);
    return $formatted;
}

function loadQuestions(string $url): array {
    $rows = fetchCsvData($url);
    return formatQuestions($rows);
}
?>
