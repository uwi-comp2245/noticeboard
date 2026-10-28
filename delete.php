<?php
declare(strict_types=1);
require __DIR__ . '/app.php';

$me = current_user();
if (!$me) {
    header('Location: index.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

// TODO (security): this deletes on a GET request with no CSRF token, and it
// deletes by id without checking who owns the notice. A link or image on
// another page could delete a logged-in user's notices.
db()->prepare('DELETE FROM notices WHERE id = ?')->execute([$id]);

header('Location: index.php');
exit;
