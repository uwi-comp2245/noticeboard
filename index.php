<?php
declare(strict_types=1);
require __DIR__ . '/app.php';

$pdo = db();
$loginError = '';

// --- Login -----------------------------------------------------------------
if (($_POST['do'] ?? '') === 'login') {
    $u = (string) ($_POST['username'] ?? '');
    $p = (string) ($_POST['password'] ?? '');

    // TODO (security): this builds the query by gluing in the input, and it
    // compares the password in plain text. Both are bugs to fix.
    $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";
    $user = $pdo->query($sql)->fetch();

    if ($user) {
        $_SESSION['uid'] = (int) $user['id'];
        header('Location: index.php');
        exit;
    }
    $loginError = 'Login failed.';
}

$me = current_user();

// --- Post a notice ---------------------------------------------------------
if ($me && ($_POST['do'] ?? '') === 'post') {
    // TODO (security): nothing checks that this POST came from our own form.
    $stmt = $pdo->prepare('INSERT INTO notices (user_id, title, body, private) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $me['id'],
        (string) ($_POST['title'] ?? ''),
        (string) ($_POST['body'] ?? ''),
        isset($_POST['private']) ? 1 : 0,
    ]);
    header('Location: index.php');
    exit;
}

// Public notices, plus your own if logged in.
$notices = $pdo->query('SELECT n.id, n.title, u.username
    FROM notices n JOIN users u ON u.id = n.user_id
    WHERE n.private = 0 ORDER BY n.id DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Noticeboard</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <main>
      <h1>Noticeboard</h1>

      <?php if ($me): ?>
        <p>Logged in as <strong><?= $me['username'] ?></strong>.
          <a href="logout.php">Log out</a></p>

        <form method="post" class="card">
          <input type="hidden" name="do" value="post">
          <h2>Post a notice</h2>
          <label>Title <input name="title"></label>
          <label>Body <textarea name="body" rows="3"></textarea></label>
          <label class="inline"><input type="checkbox" name="private"> Private</label>
          <button type="submit">Post</button>
        </form>
      <?php else: ?>
        <form method="post" class="card">
          <input type="hidden" name="do" value="login">
          <h2>Log in</h2>
          <?php if ($loginError): ?><p class="error"><?= $loginError ?></p><?php endif; ?>
          <label>Username <input name="username"></label>
          <label>Password <input type="password" name="password"></label>
          <button type="submit">Log in</button>
        </form>
        <p class="hint">Try <code>ama</code> / <code>sunflower</code>.</p>
      <?php endif; ?>

      <h2>Public notices</h2>
      <ul class="notices">
        <?php foreach ($notices as $n): ?>
          <!-- TODO (security): title is printed without escaping -->
          <li><a href="notice.php?id=<?= (int) $n['id'] ?>"><?= $n['title'] ?></a>
            <span class="by">by <?= $n['username'] ?></span></li>
        <?php endforeach; ?>
      </ul>
    </main>
  </body>
</html>
