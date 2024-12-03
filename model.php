<?php

/**
 * Возвращает список всех пользователей с транзакциями.
 */
function get_users_with_transactions($conn)
{
    $query = "
        SELECT DISTINCT users.id, users.name
        FROM users
        JOIN user_accounts ON users.id = user_accounts.user_id
        JOIN transactions ON user_accounts.id IN (transactions.account_from, transactions.account_to)
    ";
    $stmt = $conn->query($query);
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[$row['id']] = $row['name'];
    }
    return $users;
}

/**
 * Возвращает список всех пользователей (упрощённый пример).
 */
function get_users($conn)
{
    $query = "SELECT id, name FROM users";
    $stmt = $conn->query($query);
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[$row['id']] = $row['name'];
    }
    return $users;
}

/**
 * Возвращает месячные балансы транзакций для конкретного пользователя.
 */
function get_user_transactions_balances($user_id, $conn)
{
    $query = "
        SELECT 
            strftime('%Y-%m', transactions.trdate) AS month,
            SUM(CASE WHEN transactions.account_to IN (
                SELECT id FROM user_accounts WHERE user_id = :user_id
            ) THEN transactions.amount ELSE 0 END) -
            SUM(CASE WHEN transactions.account_from IN (
                SELECT id FROM user_accounts WHERE user_id = :user_id
            ) THEN transactions.amount ELSE 0 END) AS balance
        FROM transactions
        WHERE transactions.account_from IN (
                SELECT id FROM user_accounts WHERE user_id = :user_id
            )
            OR transactions.account_to IN (
                SELECT id FROM user_accounts WHERE user_id = :user_id
            )
        GROUP BY month
        ORDER BY month;
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
