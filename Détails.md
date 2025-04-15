# Application web pour une meilleure gestion budgétaire

## Travail à faire

Développer une application PHP permettant à un utilisateur de :

- Gérer ses revenus et dépenses  
- Suivre l’évolution de son solde  
- Visualiser un historique et un résumé budgétaire  

## Critères de travail

### Fonctionnalités attendues

- **Page d'authentification**
- **Page d'inscription**
- **Gestion des transactions** :
  - Ajouter une transaction :  
    Une transaction est définie par :  
    `type` (dépense ou revenu), `montant`, `catégorie`, `description`, `date de transaction`
  - Supprimer une transaction  
  - Modifier une transaction  
  - Afficher l'historique des transactions triées par date, avec un filtre sur les années et les mois

- **Tableau de bord** :
  - Afficher le solde actuel  
  - Résumé du mois en cours : total des revenus / dépenses  
  - Somme totale des dépenses/revenus par catégorie  
  - Afficher la dépense la plus haute ainsi que le revenu le plus grand du mois en cours  

## NB :

- Pour assurer une meilleure sécurité de vos données, veillez à **hacher les mots de passe**
- Utiliser un fichier `config.php` pour la configuration de la **connexion à la base de données**
- Tous les **formulaires doivent être validés** avec un affichage personnalisé des messages d'erreurs
- **Nettoyer les données** avant de les insérer dans la base de données
- Utiliser des fonctions pour mieux structurer votre code et les classer dans des fichiers :
  - `user.php`
  - `transactions.php`
  - `dashboard.php`

### Fonctions recommandées

```php
addUser($user, $connection)
log($email, $password, $connection)
addTransaction($transaction, $connection)
deleteTransaction($idTransaction, $connection)
editTransaction($idTransaction, $newTransaction, $connection)
listTransactions($connection)
listTransactionsbyMonth($connection, $year, $month)
soldUser($connection)
detailsUser($connection)
totalIncomesByCategory($category, $connection)
totalExpensesByCategory($category, $connection)
