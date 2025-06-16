<?php
// fetch_questions.php - Provide $questions array by fetching and formatting CSV data

require_once __DIR__ . '/quiz_utils.php';

$csvUrl = 'https://docs.google.com/spreadsheets/d/1KO_XLlThBT9naPvP0tXRDEJI4iegW4b8Y21pr-h-qJk/gviz/tq?tqx=out:csv';

$questions = loadQuestions($csvUrl);
?>
