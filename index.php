<?php
require_once __DIR__ . '/fetch_questions.php';

// Convert raw CSV rows into an array with 'question' and 'choices' keys
$formatted = [];
foreach ($questions as $i => $row) {
    // Skip header row if it contains text like "Question" or "\xE5\x95\x8F\xE9\xA1\x8C"
    if ($i === 0 && isset($row[0]) && preg_match('/(question|\xE5\x95\x8F)/i', $row[0])) {
        continue;
    }

    $questionText = $row[0] ?? '';
    if ($questionText === '') {
        continue;
    }
    $choices = array_slice($row, 1);
    $choices = array_values(array_filter($choices, fn($c) => $c !== ''));

    $formatted[] = [
        'question' => $questionText,
        'choices'  => $choices,
    ];
}

$questions = $formatted;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Quiz</h1>
    <form>
        <?php foreach ($questions as $index => $q): ?>
            <div class="mb-4">
                <p class="fw-bold">
                    <?php echo htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <?php foreach ($q['choices'] as $choiceIndex => $choice): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="question_<?php echo $index; ?>" id="q<?php echo $index; ?>_<?php echo $choiceIndex; ?>" value="<?php echo $choiceIndex; ?>">
                        <label class="form-check-label" for="q<?php echo $index; ?>_<?php echo $choiceIndex; ?>">
                            <?php echo htmlspecialchars($choice, ENT_QUOTES, 'UTF-8'); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
