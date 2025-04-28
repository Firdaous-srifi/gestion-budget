<?php
require_once '../functions/transaction_model.php';
require_once '../config.php';

// récupérer les filtres
$year = isset($_GET['year']) ? intval($_GET['year']) : null;
$month = isset($_GET['month']) ? intval($_GET['month']) : null;

if ($year || $month) {
    $transactions = listTransactionsByMonth($connection, $year, $month);
} else {
    $transactions = listTransactions($connection);
}

// ثم دير afficher النتائج
?>
