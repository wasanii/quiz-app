<?php
require_once __DIR__ . '/fetch_questions.php';

// Convert raw CSV rows into an array with 'question' and 'choices' keys
$formatted = [];
foreach ($questions as $i => $row) {
    // Skip header row returned from the CSV
    if ($i === 0) {
        continue;
    }

    // CSV columns: 0:年度, 1:出題方式, 2:番号, 3:問題文, 4-7:選択肢1-4
    $mode        = $row[1] ?? '';
    $questionText = $row[3] ?? '';
    if ($questionText === '') {
        continue;
    }

    if ($mode === '○×') {
        $choiceCols = array_slice($row, 4, 2); // 選択肢1-2
    } else {
        $choiceCols = array_slice($row, 4, 4); // 選択肢1-4 (不足分は除外)
    }
    $choices = array_values(array_filter($choiceCols, fn($c) => $c !== ''));

    $formatted[] = [
        'question' => $questionText,
        'choices'  => $choices,
    ];
}

$questions = $formatted;
?>
<!DOCTYPE html>
<html lang="ja">
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
                    <?php echo nl2br(htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8')); ?>
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
