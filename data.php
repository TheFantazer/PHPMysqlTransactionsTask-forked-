<?php
include_once('db.php');
include_once('model.php');

// Получаем ID пользователя из запроса
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

if ($user_id) {
    $conn = get_connect();

    // Получаем пользователей с транзакциями
    $users = get_users_with_transactions($conn);

    if (!isset($users[$user_id])) {
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    // Получаем месячные балансы
    $balances = get_user_transactions_balances($user_id, $conn);

    // Формируем ответ
    echo json_encode([
        'name' => $users[$user_id],
        'balances' => $balances
    ]);
    exit;
} else {
    echo json_encode(['error' => 'User ID is required']);
    exit;
}
