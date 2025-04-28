<?php
    session_start();
    require_once '../config.php';
    require_once '../functions/db_functions.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    $userId = $_SESSION['user_id'];

    $totals = getBalanceTotals($pdo, $userId);
    $balance = ($totals['total_revenus'] ?? 0) - ($totals['total_depenses'] ?? 0);

    $currentYear = date('Y');
    $currentMonth = date('m');
    $monthTotals = getCurrentMonthTotals($pdo, $userId, $currentYear, $currentMonth);

    $categoryTotals = getCategoryTotals($pdo, $userId);

    $incomeByCategory = array_filter($categoryTotals, fn($cat) => $cat['category_type'] === 'revenu');
    $expenseByCategory = array_filter($categoryTotals, fn($cat) => $cat['category_type'] === 'depense');

    $highestIncome = getHighestTransaction($pdo, $userId, $currentYear, $currentMonth, 'revenu');
    $highestExpense = getHighestTransaction($pdo, $userId, $currentYear, $currentMonth, 'depense');

    function date_fr($format, $timestamp) {
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, date($format, $timestamp));
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        /* Reset and global styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(255, 123, 0, 0.2);
            width: 100%;
            max-width: 900px;
        }

        h1 {
            color: #ff6600;
            text-align: center;
            margin-bottom: 25px;
        }

        .summary-box {
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .summary-box h5 {
            color: #ff6600;
            margin-bottom: 10px;
        }

        .revenu-amount {
            color: #28a745;
        }

        .depense-amount {
            color: #dc3545;
        }

        .balance-positive {
            color: #28a745;
        }

        .balance-negative {
            color: #dc3545;
        }

        .list-group-item {
            background-color: transparent;
            border: none;
            padding: 10px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">💰 BudgetManager</div>
            <ul class="nav-links">
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="../transactions/transaction_manager.php">Transactions</a></li>
            </ul>
    </nav>
    <div class="dashboard-container">
        <h1>Tableau de Bord</h1>

        <!-- Balance Summary -->
        <div class="summary-box">
            <h5>Solde actuel</h5>
            <h3 class="<?= $balance >= 0 ? 'balance-positive' : 'balance-negative' ?>">
                <?= number_format($balance, 2) ?> €
            </h3>
        </div>

        <div class="summary-box">
            <h5>Total des revenus</h5>
            <h3 class="revenu-amount"><?= number_format($totals['total_revenus'] ?? 0, 2) ?> €</h3>
        </div>

        <div class="summary-box">
            <h5>Total des dépenses</h5>
            <h3 class="depense-amount"><?= number_format($totals['total_depenses'] ?? 0, 2) ?> €</h3>
        </div>

        <!-- Current Month Summary -->
        <div class="summary-box">
            <h5>Résumé du mois en cours (<?= date_fr('F Y', mktime(0, 0, 0, $currentMonth, 1)) ?>)</h5>
            <p>Revenus : <span class="revenu-amount"><?= number_format($monthTotals['month_revenus'] ?? 0, 2) ?> €</span></p>
            <p>Dépenses : <span class="depense-amount"><?= number_format($monthTotals['month_depenses'] ?? 0, 2) ?> €</span></p>
        </div>

        <!-- Highest Income and Expense -->
        <div class="summary-box">
            <h5>Revenu le plus élevé</h5>
            <?php if ($highestIncome): ?>
                <p><strong><?= htmlspecialchars($highestIncome['description']) ?></strong></p>
                <p>Catégorie: <?= htmlspecialchars($highestIncome['category_name']) ?></p>
                <p>Date: <?= date('d/m/Y', strtotime($highestIncome['date_transaction'])) ?></p>
                <p class="revenu-amount"><?= number_format($highestIncome['montant'], 2) ?> €</p>
            <?php else: ?>
                <p class="text-muted">Aucun revenu ce mois-ci.</p>
            <?php endif; ?>
        </div>

        <div class="summary-box">
            <h5>Dépense la plus élevée</h5>
            <?php if ($highestExpense): ?>
                <p><strong><?= htmlspecialchars($highestExpense['description']) ?></strong></p>
                <p>Catégorie: <?= htmlspecialchars($highestExpense['category_name']) ?></p>
                <p>Date: <?= date('d/m/Y', strtotime($highestExpense['date_transaction'])) ?></p>
                <p class="depense-amount"><?= number_format($highestExpense['montant'], 2) ?> €</p>
            <?php else: ?>
                <p class="text-muted">Aucune dépense ce mois-ci.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> BudgetManager. Tous droits réservés.</p>
    </footer>
</body>
</html>