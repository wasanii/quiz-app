<?php
// fetch_questions.php - Fetch CSV data from a public Google Sheets URL into $questions array

$csvUrl = 'https://docs.google.com/spreadsheets/d/1KO_XLlThBT9naPvP0tXRDEJI4iegW4b8Y21pr-h-qJk/export?format=csv'; // e.g. https://docs.google.com/spreadsheets/d/.../export?format=csv

function fetchCsvData(string $url): array {
    $csvData = @file_get_contents($url);
    if ($csvData === false) {
        return [];
    }

    $lines = array_filter(array_map('trim', explode("\n", $csvData)));
    $rows = [];
    foreach ($lines as $line) {
        $rows[] = str_getcsv($line);
    }
    return $rows;
}

$questions = fetchCsvData($csvUrl);

// Example: output count of questions
// echo 'Loaded ' . count($questions) . " questions\n";
?>
