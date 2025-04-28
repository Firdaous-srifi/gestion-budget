<?php
require_once '../config.php';

function addTransaction($pdo, $userId, $categoryId, $montant, $description, $dateTransaction) {
    $sql = "INSERT INTO transactions (user_id, category_id, montant, description, date_transaction) 
            VALUES (:user_id, :category_id, :montant, :description, :date_transaction)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':user_id' => $userId,
        ':category_id' => $categoryId,
        ':montant' => $montant,
        ':description' => htmlspecialchars($description),
        ':date_transaction' => $dateTransaction
    ]);
}

function updateTransaction($pdo, $userId, $transactionId, $categoryId, $montant, $description, $dateTransaction) {
    $sql = "UPDATE transactions 
            SET category_id = :category_id, montant = :montant, description = :description, date_transaction = :date_transaction 
            WHERE id = :transaction_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':category_id' => $categoryId,
        ':montant' => $montant,
        ':description' => htmlspecialchars($description),
        ':date_transaction' => $dateTransaction,
        ':transaction_id' => $transactionId,
        ':user_id' => $userId
    ]);
}

function deleteTransaction($pdo, $userId, $transactionId) {
    $sql = "DELETE FROM transactions WHERE id = :transaction_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':transaction_id' => $transactionId,
        ':user_id' => $userId
    ]);
}

function getTransactions($pdo, $userId, $year = 'all', $month = 'all') {
    $sql = "SELECT t.*, c.nom as category_name, c.type as category_type 
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = :user_id ";
    $params = [':user_id' => $userId];

    if ($year !== 'all') {
        $sql .= "AND YEAR(t.date_transaction) = :year ";
        $params[':year'] = $year;
    }
    if ($month !== 'all') {
        $sql .= "AND MONTH(t.date_transaction) = :month ";
        $params[':month'] = $month;
    }

    $sql .= "ORDER BY t.date_transaction DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBalance($pdo, $userId) {
    $sql = "SELECT 
            SUM(CASE WHEN c.type = 'revenu' THEN t.montant ELSE 0 END) as total_revenus,
            SUM(CASE WHEN c.type = 'depense' THEN t.montant ELSE 0 END) as total_depenses
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);
    return [
        'total_revenus' => $totals['total_revenus'] ?? 0,
        'total_depenses' => $totals['total_depenses'] ?? 0,
        'balance' => ($totals['total_revenus'] ?? 0) - ($totals['total_depenses'] ?? 0)
    ];
}

function getTransactionYears($pdo, $userId) {
    $sql = "SELECT DISTINCT YEAR(date_transaction) as year FROM transactions WHERE user_id = :user_id ORDER BY year DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getCategories($pdo) {
    $sql = "SELECT * FROM categories ORDER BY type, nom";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
