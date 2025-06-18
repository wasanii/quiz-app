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

The quiz contains 200 questions in total (40 questions for each of the last five
years). Your progress line shows how many answers you've gotten correct out of
the number of questions you have attempted so far, along with your accuracy and
how many questions remain. For example:

```
正解数 3 / 5 問中 正解率 60% 残り 195 問
```

## Progress storage

The app stores the IDs of questions you have answered in `localStorage` under
the key `solved`. The number of correct answers is stored under `score`. When
the page loads it filters out any solved questions and serves one of the
remaining questions at random. To reset your progress, open your browser's
developer tools and remove both the `solved` and `score` entries (or clear all
stored data).
