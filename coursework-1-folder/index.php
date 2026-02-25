<?php
declare(strict_types=1);
// Inventory
$books = [
  [
    'title' => 'Dune',
    'author' => 'Frank Herbert',
    'genre' => 'Science Fiction',
    'price' => 29.99,
    'original_price' => 29.99
  ],
  [
    'title' => 'The Hobbit',
    'author' => 'J.R.R. Tolkien',
    'genre' => 'Fantasy',
    'price' => 19.50,
    'original_price' => 19.50
  ],
  [
    'title' => '1984',
    'author' => 'George Orwell',
    'genre' => 'Dystopian',
    'price' => 14.25,
    'original_price' => 14.25
  ],
];
function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function money(float $n): string {
  return '$' . number_format($n, 2, '.', '');
}
// Discount logic
function applyDiscounts(array &$books): void {

  foreach ($books as &$book) {
    // Ensure original price exists
    if (!isset($book['original_price'])) {
      $book['original_price'] = $book['price'];
    }
    // RESET price before discount
    $book['price'] = $book['original_price'];
    if ($book['genre'] === "Science Fiction") {
      $book['price'] = round($book['price'] * 0.90, 2);
    }
  }

  unset($book);
}
// Logging
function logBookAdded(string $title, string $genre, float $price, string $ip, string $ua): void {
  $time = date('Y-m-d H:i:s');

  $line = sprintf(
    "[%s] IP: %s | UA: %s | Added book: \"%s\" (%s, %.2f)\n",
    $time,
    $ip,
    $ua,
    $title,
    $genre,
    $price
  );

  $fp = fopen(__DIR__ . '/bookstore_log.txt', 'a');
  if ($fp) {
    fwrite($fp, $line);
    fclose($fp);
  }
}
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $title  = trim($_POST['title'] ?? '');
  $author = trim($_POST['author'] ?? '');
  $genre  = trim($_POST['genre'] ?? '');
  $priceRaw = trim($_POST['price'] ?? '');

  if ($title === '') $errors[] = "Title is required.";
  if ($author === '') $errors[] = "Author is required.";
  if ($genre === '') $errors[] = "Genre is required.";
  if ($priceRaw === '' || !is_numeric($priceRaw)) {
    $errors[] = "Price must be numeric.";
  }

  if (!$errors) {

    $price = (float)$priceRaw;

    $books[] = [
      'title' => $title,
      'author' => $author,
      'genre' => $genre,
      'price' => $price,
      'original_price' => $price
    ];

    applyDiscounts($books);

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    logBookAdded($title, $genre, $price, $ip, $ua);

    $success = "Book added successfully!";
  }
}

applyDiscounts($books);

$total = 0;
foreach ($books as $b) {
  $total += $b['price'];
}
$now = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

$logContent = '';
if (file_exists('bookstore_log.txt')) {
  $logContent = file_get_contents('bookstore_log.txt');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Bookstore</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="wrap">

<h1>Online Bookstore</h1>

<div class="meta">
  <strong>Request time:</strong> <?= h($now) ?><br>
  <strong>IP:</strong> <?= h($ip) ?><br>
  <strong>User agent:</strong> <?= h($ua) ?>
</div>

<div class="panel">
  <h2>Add Book</h2>

  <?php if ($errors): ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= h($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?= h($success) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Title
      <input type="text" name="title" required>
    </label>

    <label>Author
      <input type="text" name="author" required>
    </label>

    <label>Genre
      <input type="text" name="genre" required>
    </label>

    <label>Price
      <input type="text" name="price" required>
    </label>

    <button type="submit">Add Book</button>
  </form>
</div>

<h2>Inventory</h2>

<table>
<thead>
<tr>
  <th>Title</th>
  <th>Author</th>
  <th>Genre</th>
  <th>Original Price</th>
  <th>Price After Discount</th>
</tr>
</thead>

<tbody>
<?php foreach ($books as $b): ?>
<tr>
  <td><?= h($b['title']) ?></td>
  <td><?= h($b['author']) ?></td>
  <td><?= h($b['genre']) ?></td>
  <td><?= money($b['original_price']) ?></td>
  <td>
    <?= money($b['price']) ?>
    <?php if ($b['genre'] === "Science Fiction"): ?>
      <span class="small">(10% off)</span>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="total">
  <strong>Total price after discounts:</strong> <?= money($total) ?>
</div>

<div class="panel">
<h2>Log</h2>

<?php if (trim($logContent) === ''): ?>
<p class="small">Log is empty.</p>
<?php else: ?>
<pre><?= h($logContent) ?></pre>
<?php endif; ?>

</div>

</div>
</body>
</html>