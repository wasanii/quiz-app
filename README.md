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

## Progress storage

Your current question index and the number of correct answers are stored in the
browser using `localStorage` under the keys `current` and `score`. When the
page is reloaded it resumes from that index with your score intact. To reset
your progress, open your browser's developer tools, locate the `localStorage`
entry for the site and remove both items (or clear all stored data).
