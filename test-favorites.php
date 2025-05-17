<?php
// Redirect to fix-favorites.php since that now handles the same functionality
// in a more complete and integrated way.

$bookId = isset($_GET['book']) ? (int)$_GET['book'] : 0;

if ($bookId > 0) {
    // If a book ID was provided, redirect to fix-favorites.php with action=toggle
    header("Location: fix-favorites.php?action=toggle&book_id=$bookId");
} else {
    // Otherwise just redirect to fix-favorites.php
    header("Location: fix-favorites.php");
}
exit;
?> 