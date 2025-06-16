<?php
require_once __DIR__ . '/fetch_questions.php';

function formatMeta(string $yearRound, string $number): string {
    if (preg_match('/R(\d+)-(\d+)/', $yearRound, $m)) {
        $yearRound = '令和' . intval($m[1]) . '年度 第' . intval($m[2]) . '回';
    }
    $num = $number !== '' ? ' 第' . intval($number) . '問' : '';
    return trim($yearRound . $num);
}

// Convert raw CSV rows into an array with question, choices, answer and explanation
$formatted = [];
foreach ($questions as $i => $row) {
    // Skip header row returned from the CSV
    if ($i === 0) {
        continue;
    }

    // CSV columns: 0:年度, 1:出題方式, 2:番号, 3:問題文, 4-7:選択肢1-4, 8:正答, 9:解説
    $mode          = $row[1] ?? '';
    $questionText  = $row[3] ?? '';
    if ($questionText === '') {
        continue;
    }

    $meta = formatMeta($row[0] ?? '', $row[2] ?? '');

    if ($mode === '○×') {
        $choiceCols = array_slice($row, 4, 2); // 選択肢1-2
    } else {
        $choiceCols = array_slice($row, 4, 4); // 選択肢1-4 (不足分は除外)
    }
    $choices = array_values(array_filter($choiceCols, fn($c) => $c !== ''));
    $answer      = $row[8] ?? '';
    $explanation = $row[9] ?? '';

    $formatted[] = [
        'question'    => $questionText,
        'choices'     => $choices,
        'answer'      => $answer,
        'explanation' => $explanation,
        'meta'        => $meta,
    ];
}

// Randomize question order
shuffle($formatted);
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
    <div id="quiz-area">
        <p id="question" class="fw-bold mb-0"></p>
        <p id="meta" class="text-end text-muted small mb-2"></p>
        <form id="choices"></form>
        <div id="result" class="mt-3 fw-bold"></div>
        <div id="explanation" class="mt-2"></div>
        <button id="submitBtn" class="btn btn-primary mt-3">回答する</button>
        <button id="nextBtn" class="btn btn-secondary mt-3" style="display:none;">次の問題へ</button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const questions = <?php echo json_encode($questions,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
?>;
let current = 0;

const qEl = document.getElementById('question');
const metaEl = document.getElementById('meta');
const choicesEl = document.getElementById('choices');
const resultEl = document.getElementById('result');
const expEl = document.getElementById('explanation');
const submitBtn = document.getElementById('submitBtn');
const nextBtn = document.getElementById('nextBtn');

function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function showQuestion() {
    const q = questions[current];
    qEl.innerHTML = escapeHtml(q.question).replace(/\n/g, '<br>');
    metaEl.textContent = q.meta || '';
    choicesEl.innerHTML = '';
    resultEl.textContent = '';
    expEl.textContent = '';
    submitBtn.style.display = '';
    nextBtn.style.display = 'none';

    q.choices.forEach((choice, idx) => {
        const div = document.createElement('div');
        div.className = 'form-check';

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = 'choice';
        input.value = idx + 1;
        input.id = `choice_${idx}`;
        input.className = 'form-check-input';

        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = input.id;
        label.textContent = choice;

        div.appendChild(input);
        div.appendChild(label);
        choicesEl.appendChild(div);
    });
}

function checkAnswer() {
    const selected = choicesEl.querySelector('input[name="choice"]:checked');
    if (!selected) {
        alert('回答を選択してください');
        return;
    }

    const q = questions[current];
    const correct = q.answer.trim();
    let isCorrect = false;
    let correctText = '';

    if (/^\d+$/.test(correct)) {
        const idx = parseInt(correct, 10) - 1;
        correctText = q.choices[idx] || '';
        isCorrect = parseInt(selected.value, 10) === parseInt(correct, 10);
    } else {
        const choiceText = selected.nextSibling.textContent.trim();
        correctText = correct;
        isCorrect = choiceText === correct;
    }

    if (isCorrect) {
        resultEl.textContent = '正解！';
    } else {
        resultEl.textContent = `不正解。正解は「${correctText}」`;
    }
    expEl.innerHTML = escapeHtml(q.explanation).replace(/\n/g, '<br>');
    submitBtn.style.display = 'none';
    nextBtn.style.display = '';
}

function nextQuestion() {
    current++;
    if (current >= questions.length) {
        qEl.textContent = 'すべての問題が終了しました';
        metaEl.textContent = '';
        choicesEl.innerHTML = '';
        resultEl.textContent = '';
        expEl.textContent = '';
        submitBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        return;
    }
    showQuestion();
}

submitBtn.addEventListener('click', (e) => {
    e.preventDefault();
    checkAnswer();
});
nextBtn.addEventListener('click', (e) => {
    e.preventDefault();
    nextQuestion();
});

showQuestion();
</script>
</body>
</html>
