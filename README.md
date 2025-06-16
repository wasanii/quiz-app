# ウェブデザイン技能検定過去問道場

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

The quiz contains 200 questions in total (40 questions for each of the last five
years). Your progress line shows how many answers you've gotten correct out of
the number of questions you have attempted so far, along with your accuracy and
how many questions remain. For example:

```
正解数 3 / 5 問中 正解率 60% 残り 195 問
```

## Progress storage

Your current question index and the number of correct answers are stored in the
browser using `localStorage` under the keys `current` and `score`. When the
page is reloaded it resumes from that index with your score intact. To reset
your progress, open your browser's developer tools, locate the `localStorage`
entry for the site and remove both items (or clear all stored data).
