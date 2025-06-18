const questions = [
  {id: 1, answer: '1', choices: ['a', 'b']},
  {id: 2, answer: '2', choices: ['c', 'd']}
];

let incorrectIds = [];
let solvedIds = [];
let score = 0;

function processAnswer(q, selected) {
  const correct = q.answer.trim();
  const isCorrect = String(selected) === correct;
  if (isCorrect) {
    score++;
    const idx = incorrectIds.indexOf(q.id);
    if (idx !== -1) incorrectIds.splice(idx, 1);
  } else {
    if (!incorrectIds.includes(q.id)) incorrectIds.push(q.id);
  }
  if (!solvedIds.includes(q.id)) solvedIds.push(q.id);
  return isCorrect;
}

// answer first question incorrectly
processAnswer(questions[0], '2');
if (incorrectIds.length !== 1 || incorrectIds[0] !== 1) {
  console.error('Incorrect question not recorded');
  process.exit(1);
}

// review mode should serve only question 1
const reviewQs = questions.filter(q => incorrectIds.includes(q.id));
if (reviewQs.length !== 1 || reviewQs[0].id !== 1) {
  console.error('Review mode did not select incorrect question');
  process.exit(1);
}

// answer it correctly in review
processAnswer(reviewQs[0], '1');
if (incorrectIds.length !== 0) {
  console.error('Correct answer was not removed from incorrect list');
  process.exit(1);
}

console.log('Review logic ok');
