const submitBtn = document.getElementById("submit");
const dataDiv = document.getElementById("data");

submitBtn.onclick = function (e) {
  e.preventDefault();

  const userId = document.getElementById("user").value;

  fetch(`data.php?user_id=${userId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        dataDiv.innerHTML = `<h2>Error: ${data.error}</h2>`;
        dataDiv.style.display = "block";
        return;
      }

      // Показываем div#data
      dataDiv.style.display = "block";

      // Обновляем содержимое
      const tableRows = data.balances
        .map((balance) => {
          const [year, month] = balance.month.split("-"); // Разделяем год и месяц
          const monthName = monthNames[month] || "Unknown Month"; // Получаем название месяца
          const formattedDate = `${year} - ${monthName}`;
          return `<tr><td>${formattedDate}</td><td>${balance.balance}</td></tr>`;
        })
        .join("");

      dataDiv.innerHTML = `
        <h2>Transactions of ${data.name}</h2>
        <table>
          <thead>
            <tr><th>Month</th><th>Balance</th></tr>
          </thead>
          <tbody>
            ${
              tableRows || '<tr><td colspan="2">No transactions found</td></tr>'
            }
          </tbody>
        </table>
      `;
    })
    .catch((error) => {
      console.error("Error:", error);
      dataDiv.innerHTML = `<h2>Error fetching data. Please try again later.</h2>`;
      dataDiv.style.display = "block";
    });
};
//te
