<?php
require_once __DIR__ . '/fetch_questions.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ウェブデザイン技能検定 過去問練習アプリ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    #question, #explanation {
        white-space: pre-wrap;
    }
    </style>
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">ウェブデザイン技能検定 学科 過去問</h1>
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="reviewToggle">
        <label class="form-check-label" for="reviewToggle">復習モード</label>
    </div>
    <div id="quiz-area">
        <p id="question" class="fw-bold mb-0"></p>
        <p id="meta" class="text-end text-muted small mb-2"></p>
        <p id="status" class="text-end text-muted small mb-2"></p>
        <form id="choices"></form>
        <div id="result" class="mt-3 fw-bold"></div>
        <div id="explanation" class="mt-2"></div>
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
const storedIncorrect = localStorage.getItem('incorrect');
const storedReview = localStorage.getItem('reviewMode');
let solvedIds = [];
let incorrectIds = [];
let score = 0;
let answered = false;
try {
    solvedIds = storedSolved ? JSON.parse(storedSolved) : [];
    if (!Array.isArray(solvedIds)) solvedIds = [];
} catch (e) {
    solvedIds = [];
}
try {
    incorrectIds = storedIncorrect ? JSON.parse(storedIncorrect) : [];
    if (!Array.isArray(incorrectIds)) incorrectIds = [];
} catch (e) {
    incorrectIds = [];
}
let reviewMode = storedReview === '1';
if (storedScore !== null) {
    score = parseInt(storedScore, 10);
    if (isNaN(score) || score < 0) score = 0;
}

let unanswered = [];
let initialIncorrectCount = 0;

function initQuestionPool() {
    if (reviewMode) {
        unanswered = questions.filter(q => incorrectIds.includes(q.id));
        initialIncorrectCount = unanswered.length;
    } else {
        unanswered = questions.filter(q => !solvedIds.includes(q.id));
    }
}

initQuestionPool();
let currentQuestion = null;

const qEl = document.getElementById('question');
const metaEl = document.getElementById('meta');
const statusEl = document.getElementById('status');
const choicesEl = document.getElementById('choices');
const resultEl = document.getElementById('result');
const expEl = document.getElementById('explanation');
const nextBtn = document.getElementById("nextBtn");
const reviewToggle = document.getElementById("reviewToggle");

reviewToggle.checked = reviewMode;

function updateStatus() {
    if (reviewMode) {
        const total = initialIncorrectCount;
        const remaining = unanswered.length;
        const answeredCount = total - remaining;
        const reviewScore = total - incorrectIds.length;
        const rate = answeredCount > 0 ? Math.round((reviewScore / answeredCount) * 100) : 0;
        statusEl.textContent =
            `復習 正解数 ${reviewScore} / ${answeredCount} 問中 正解率 ${rate}% 残り ${remaining} 問`;
    } else {
        const total = questions.length;
        const answeredCount = solvedIds.length;
        const remaining = total - answeredCount;
        const rate = answeredCount > 0 ? Math.round((score / answeredCount) * 100) : 0;
        statusEl.textContent =
            `正解数 ${score} / ${answeredCount} 問中 正解率 ${rate}% 残り ${remaining} 問`;
    }
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
    nextBtn.style.display = 'none';
    updateStatus();

    q.choices.forEach((choice, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-primary d-block mb-2';
        btn.textContent = choice;
        btn.dataset.value = idx + 1;
        btn.addEventListener('click', () => checkAnswer(idx + 1));
        choicesEl.appendChild(btn);
    });
}

function checkAnswer(selectedVal) {

    const q = currentQuestion;
    const correct = q.answer.trim();
    let isCorrect = false;
    let correctText = '';

    if (/^\d+$/.test(correct)) {
        const idx = parseInt(correct, 10) - 1;
        correctText = q.choices[idx] || '';
        isCorrect = parseInt(selectedVal, 10) === parseInt(correct, 10);
    } else {
        const choiceText = q.choices[selectedVal - 1];
        correctText = correct;
        isCorrect = choiceText === correct;
    }

    if (isCorrect) {
        resultEl.textContent = '正解！';
        score++;
        localStorage.setItem('score', score);
        const idx = incorrectIds.indexOf(q.id);
        if (idx !== -1) {
            incorrectIds.splice(idx, 1);
            localStorage.setItem('incorrect', JSON.stringify(incorrectIds));
        }
    } else {
        resultEl.textContent = `不正解。正解は「${correctText}」`;
        if (!incorrectIds.includes(q.id)) {
            incorrectIds.push(q.id);
            localStorage.setItem('incorrect', JSON.stringify(incorrectIds));
        }
    }
    if (!solvedIds.includes(q.id)) {
        solvedIds.push(q.id);
        localStorage.setItem('solved', JSON.stringify(solvedIds));
    }
    expEl.innerHTML = escapeHtml(q.explanation).replace(/\n/g, '<br>');
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
        if (reviewMode) {
            qEl.textContent = '復習する問題はありません';
        } else {
            qEl.textContent = 'すべての問題が終了しました';
        }
        metaEl.textContent = '';
        choicesEl.innerHTML = '';
        resultEl.textContent = reviewMode ? '' : `正解数 ${score} / ${questions.length} 問`;
        expEl.textContent = '';
        nextBtn.style.display = 'none';
        if (!reviewMode) {
            localStorage.removeItem('solved');
            localStorage.removeItem('score');
        }
        if (incorrectIds.length === 0) {
            localStorage.removeItem('incorrect');
        }
        statusEl.textContent = '';
        return;
    }
    showQuestion(q);
}

nextBtn.addEventListener('click', (e) => {
    e.preventDefault();
    nextQuestion();
});
reviewToggle.addEventListener('change', () => {
    reviewMode = reviewToggle.checked;
    localStorage.setItem('reviewMode', reviewMode ? '1' : '0');
    initQuestionPool();
    nextQuestion();
});

nextQuestion();
</script>
</body>
</html>
