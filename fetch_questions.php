<?php
// fetch_questions.php - Fetch CSV data from a public Google Sheets URL into $questions array

$csvUrl = 'https://docs.google.com/spreadsheets/d/1KO_XLlThBT9naPvP0tXRDEJI4iegW4b8Y21pr-h-qJk/gviz/tq?tqx=out:csv'; // Google Sheets CSV export

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

$questions = fetchCsvData($csvUrl);

// Example: output count of questions
// echo 'Loaded ' . count($questions) . " questions\n";
?>
