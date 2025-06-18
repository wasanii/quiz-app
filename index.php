<?php
require_once __DIR__ . '/fetch_questions.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    #question, #explanation {
        white-space: pre-wrap;
    }
    </style>
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Quiz</h1>
    <div id="quiz-area">
        <p id="question" class="fw-bold mb-0"></p>
        <p id="meta" class="text-end text-muted small mb-2"></p>
        <p id="status" class="text-end text-muted small mb-2"></p>
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

const storedSolved = localStorage.getItem('solved');
const storedScore = localStorage.getItem('score');
let solvedIds = [];
let score = 0;
let answered = false;
try {
    solvedIds = storedSolved ? JSON.parse(storedSolved) : [];
    if (!Array.isArray(solvedIds)) solvedIds = [];
} catch (e) {
    solvedIds = [];
}
if (storedScore !== null) {
    score = parseInt(storedScore, 10);
    if (isNaN(score) || score < 0) score = 0;
}

let unanswered = questions.filter(q => !solvedIds.includes(q.id));
let currentQuestion = null;

const qEl = document.getElementById('question');
const metaEl = document.getElementById('meta');
const statusEl = document.getElementById('status');
const choicesEl = document.getElementById('choices');
const resultEl = document.getElementById('result');
const expEl = document.getElementById('explanation');
const submitBtn = document.getElementById('submitBtn');
const nextBtn = document.getElementById('nextBtn');

function updateStatus() {
    const total = questions.length;
    const answeredCount = solvedIds.length;
    const remaining = total - answeredCount;
    const rate = answeredCount > 0 ? Math.round((score / answeredCount) * 100) : 0;
    statusEl.textContent =
        `正解数 ${score} / ${answeredCount} 問中 正解率 ${rate}% 残り ${remaining} 問`;
}

function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function showQuestion(q) {
    answered = false;
    currentQuestion = q;
    qEl.innerHTML = escapeHtml(q.question).replace(/\n/g, '<br>');
    metaEl.textContent = q.meta || '';
    choicesEl.innerHTML = '';
    resultEl.textContent = '';
    expEl.textContent = '';
    submitBtn.style.display = '';
    nextBtn.style.display = 'none';
    updateStatus();

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

    const q = currentQuestion;
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
        score++;
        localStorage.setItem('score', score);
    } else {
        resultEl.textContent = `不正解。正解は「${correctText}」`;
    }
    if (!solvedIds.includes(q.id)) {
        solvedIds.push(q.id);
        localStorage.setItem('solved', JSON.stringify(solvedIds));
    }
    expEl.innerHTML = escapeHtml(q.explanation).replace(/\n/g, '<br>');
    submitBtn.style.display = 'none';
    nextBtn.style.display = '';
    answered = true;
    updateStatus();
}

function pickNextQuestion() {
    if (unanswered.length === 0) {
        return null;
    }
    const idx = Math.floor(Math.random() * unanswered.length);
    const q = unanswered.splice(idx, 1)[0];
    return q;
}

function nextQuestion() {
    const q = pickNextQuestion();
    if (!q) {
        qEl.textContent = 'すべての問題が終了しました';
        metaEl.textContent = '';
        choicesEl.innerHTML = '';
        resultEl.textContent = `正解数 ${score} / ${questions.length} 問`;
        expEl.textContent = '';
        submitBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        localStorage.removeItem('solved');
        localStorage.removeItem('score');
        statusEl.textContent = '';
        return;
    }
    showQuestion(q);
}

submitBtn.addEventListener('click', (e) => {
    e.preventDefault();
    checkAnswer();
});
nextBtn.addEventListener('click', (e) => {
    e.preventDefault();
    nextQuestion();
});

nextQuestion();
</script>
</body>
</html>
