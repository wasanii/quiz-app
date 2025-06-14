<?php
// fetch_questions.php - Fetch CSV data from a public Google Sheets URL into $questions array

$csvUrl = 'YOUR_PUBLIC_GOOGLE_SHEETS_CSV_URL'; // e.g. https://docs.google.com/spreadsheets/d/.../export?format=csv

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
