# ウェブデザイン技能検定過去問練習アプリ

## Testing CSV parsing

Run the included script to verify that multiline cells are handled correctly:

```bash
php test_parse.php
```

Another script ensures the formatted questions produced by `quiz_utils.php`
contain an `id` field:

```bash
php test_format.php
```

The `test_review.js` script verifies the logic around the new review mode
without requiring all 200 questions:

```bash
node test_review.js
```

The quiz contains 200 questions in total (40 questions for each of the last five
years). Your progress line shows how many answers you've gotten correct out of
the number of questions you have attempted so far, along with your accuracy and
how many questions remain. For example:

```
正解数 3 / 5 問中 正解率 60% 残り 195 問
```

## Progress storage

The app stores the IDs of questions you have answered in `localStorage` under
the key `solved`. The number of correct answers is stored under `score`. IDs of
incorrectly answered questions are stored under `incorrect`. Enable "復習モード"
using the switch on the page to practice only those questions. Correct answers
in this mode remove the corresponding ID from `incorrect` so you can repeat
until all are solved. When the page loads it filters questions according to the
current mode. To reset your progress, open your browser's developer tools and
remove the `solved`, `score` and `incorrect` entries (or clear all stored
data).
