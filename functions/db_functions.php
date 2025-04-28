<?php
    require_once '../config.php';

    function getBalanceTotals($pdo, $userId) {
        $sql = "SELECT 
            SUM(CASE WHEN c.type = 'revenu' THEN t.montant ELSE 0 END) as total_revenus,
            SUM(CASE WHEN c.type = 'depense' THEN t.montant ELSE 0 END) as total_depenses
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getCurrentMonthTotals($pdo, $userId, $year, $month) {
        $sql = "SELECT 
            SUM(CASE WHEN c.type = 'revenu' THEN t.montant ELSE 0 END) as month_revenus,
            SUM(CASE WHEN c.type = 'depense' THEN t.montant ELSE 0 END) as month_depenses
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = :user_id 
            AND YEAR(t.date_transaction) = :year 
            AND MONTH(t.date_transaction) = :month";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getCategoryTotals($pdo, $userId) {
        $sql = "SELECT c.nom as category_name, c.type as category_type, SUM(t.montant) as total
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = :user_id 
            GROUP BY c.id, c.nom, c.type 
            ORDER BY c.type, c.nom";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getHighestTransaction($pdo, $userId, $year, $month, $type) {
        $sql = "SELECT t.*, c.nom as category_name, c.type as category_type
            FROM transactions t 
            JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = :user_id 
            AND YEAR(t.date_transaction) = :year 
            AND MONTH(t.date_transaction) = :month 
            AND c.type = :type 
            ORDER BY t.montant DESC 
            LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->bindValue(':type', $type);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
