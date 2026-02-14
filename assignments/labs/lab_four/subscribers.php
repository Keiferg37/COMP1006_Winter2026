<?php
require "includes/connect.php";

/*
  TODO:
  1. Write a SELECT query to get all subscribers
  2. Add ORDER BY subscribed_at DESC
  3. Prepare the statement
  4. Execute the statement
  5. Fetch all results into $subscribers
*/

$STMT = $DBH->prepare("
    SELECT id, first_name, last_name, email, subscribed_at
    FROM subscribers
    ORDER BY subscribed_at DESC
");

$STMT->execute();
$subscribers = $STMT->fetchAll();
?>

<main class="container mt-4">
  <h1>Subscribers</h1>

  <?php if (count($subscribers) === 0): ?>
    <p>No subscribers yet.</p>
  <?php else: ?>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Subscribed</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($subscribers as $S): ?>
          <tr>
            <td><?= htmlspecialchars($S["id"]) ?></td>
            <td><?= htmlspecialchars($S["first_name"]) ?></td>
            <td><?= htmlspecialchars($S["last_name"]) ?></td>
            <td><?= htmlspecialchars($S["email"]) ?></td>
            <td><?= htmlspecialchars($S["subscribed_at"]) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p class="mt-3">
    <a href="index.php">Back to Subscribe Form</a>
  </p>
</main>

<?php require "includes/footer.php"; ?>
