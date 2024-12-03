<?php

include_once('db.php');      // Подключение к базе данных
include_once('model.php');   // Функции для работы с базой данных
include_once('test.php');    // Тестирование
$conn = get_connect();       // Получение соединения с базой данных

// Месяца для отображения
$month_names = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December',
];

?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User transactions information</title>
  <link rel="stylesheet" href="style.css">
  <script>
    // Передача названий месяцев из PHP в JavaScript
    const monthNames = <?= json_encode($month_names) ?>;
  </script>
</head>
<body>
  <h1>User transactions information</h1>
  
  <!-- Форма выбора пользователя -->
  <form id="userForm" method="get" action="">
    <label for="user">Select user:</label>
    <select name="user" id="user" onchange="loadData()">
    <?php
    $users = get_users_with_transactions($conn);
    foreach ($users as $id => $name) {
        echo "<option value=\"$id\">" . htmlspecialchars($name) . "</option>";
    }
    ?>
    </select>
    <input id="submit" type="submit" value="Show">
  </form>

  <!-- Динамическая таблица данных -->
  <div id="data">
      <h2>Transactions of <span id="userName">User name</span></h2>
      <table>
          <thead>
              <tr>
                  <th>Month</th>
                  <th>Amount</th>
              </tr>
          </thead>
          <tbody id="transactionData">
              <tr>
                  <td colspan="2">Select a user to display data</td>
              </tr>
          </tbody>
      </table>
  </div>

  <script>
    async function loadData() {
        const userId = document.getElementById('user').value;
        if (!userId) return;

        // Запрос на сервер для получения данных
        const response = await fetch(`data.php?user_id=${userId}`);
        const data = await response.json();

        // Обновление данных на странице
        const userNameElement = document.getElementById('userName');
        const transactionDataElement = document.getElementById('transactionData');

        userNameElement.textContent = data.name || 'Unknown User';
        transactionDataElement.innerHTML = '';

        if (data.balances && data.balances.length > 0) {
            data.balances.forEach(balance => {
                const row = document.createElement('tr');
                const monthCell = document.createElement('td');
                const balanceCell = document.createElement('td');

                monthCell.textContent = formattedDate;
                balanceCell.textContent = balance.balance;

                row.appendChild(monthCell);
                row.appendChild(balanceCell);
                transactionDataElement.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.textContent = 'No transactions found';
            cell.colSpan = 2;
            row.appendChild(cell);
            transactionDataElement.appendChild(row);
        }
    }
  </script>
  <script src="script.js"></script>
</body>
</html>
