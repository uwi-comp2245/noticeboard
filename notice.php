<?php
declare(strict_types=1);
require __DIR__ . '/app.php';

$me = current_user();
$id = (int) ($_GET['id'] ?? 0);

// TODO (security): this fetches a notice by id only. There is no check that
// the notice is public or that it belongs to the logged-in user, so anyone
// can read a private notice by guessing its id.
$stmt = db()->prepare('SELECT n.*, u.username FROM notices n JOIN users u ON u.id = n.user_id WHERE n.id = ?');
$stmt->execute([$id]);
$notice = $stmt->fetch();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notice</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <main>
      <p><a href="index.php">← Noticeboard</a></p>
      <?php if (!$notice): ?>
        <p>No such notice.</p>
      <?php else: ?>
        <!-- TODO (security): title and body are printed without escaping -->
        <h1><?= $notice['title'] ?></h1>
        <p class="by">by <?= $notice['username'] ?><?= $notice['private'] ? ' · private' : '' ?></p>
        <div class="body"><?= $notice['body'] ?></div>

        <?php if ($me && (int) $notice['user_id'] === (int) $me['id']): ?>
          <!-- TODO (security): delete happens on a GET with no token -->
          <p><a class="danger" href="delete.php?id=<?= (int) $notice['id'] ?>">Delete this notice</a></p>
        <?php endif; ?>
      <?php endif; ?>
    </main>
  </body>
</html>
