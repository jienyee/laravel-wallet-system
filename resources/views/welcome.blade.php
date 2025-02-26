<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center mb-4">💼 Wallet Management System</h2>
        <div id="alertContainer"></div>
        <div class="row">
            <div class="col-md-6">
                <form id="depositForm" class="card p-3 shadow">
                    <h5>Deposit Funds</h5>
                    <input type="number" step="0.01" id="depositAmount" class="form-control mb-2" placeholder="Enter amount" required>
                    <button type="submit" class="btn btn-success w-100">Deposit</button>
                </form>
                <form id="withdrawForm" class="card p-3 shadow mt-3">
                    <h5>Withdraw Funds</h5>
                    <input type="number" step="0.01" id="withdrawAmount" class="form-control mb-2" placeholder="Enter amount" required>
                    <button type="submit" class="btn btn-danger w-100">Withdraw</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="card p-3 shadow">
                    <h5>💰 Wallet Balance</h5>
                    <h3 id="walletBalance" step="0.01">$0.00</h3>
                    <h5 class="mt-4">📜 Transaction History</h5>
                    <ul id="transactionList" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const userId = 1;
        const baseUrl = '/wallet';

        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }

        function loadBalance() {
            axios.get(`${baseUrl}/balance/${userId}`).then(response => {
                document.getElementById('walletBalance').textContent = `$${(+response.data.balance).toFixed(2)}`;
            });
        }

        function loadTransactions() {
            axios.get(`${baseUrl}/transactions/${userId}`).then(response => {
                const list = document.getElementById('transactionList');
                list.innerHTML = '';
                response.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                response.data.forEach(t => {
                    const item = document.createElement('li');
                    item.className = 'list-group-item';

                    if (t.type === 'withdrawal') {
                        item.classList.add('text-danger', 'fw-bold');
                    } else if (t.type === 'deposit') {
                        item.classList.add('text-success', 'fw-bold');
                    } else if (t.type === 'rebate') {
                        item.classList.add('text-primary');
                    }

                    item.textContent = `${t.type.toUpperCase()} - $${t.amount} (${new Date(t.created_at).toLocaleString()})`;
                    list.appendChild(item);
                });
            });
        }

        function refreshAfterDeposit() {
            loadBalance();
            loadTransactions();
            setTimeout(() => {
                loadBalance();
                loadTransactions();
            }, 3000);
        }

        document.getElementById('depositForm').addEventListener('submit', e => {
            e.preventDefault();
            const amount = document.getElementById('depositAmount').value;
            axios.post(`${baseUrl}/deposit/${userId}`, { amount }).then(() => {
                refreshAfterDeposit();
                showAlert('✅ Deposit successful with rebate applied!', 'success');
            }).catch(() => showAlert('❌ Deposit failed. Please try again.'));
        });

        document.getElementById('withdrawForm').addEventListener('submit', e => {
            e.preventDefault();
            const amount = document.getElementById('withdrawAmount').value;
            axios.post(`${baseUrl}/withdraw/${userId}`, { amount }).then(() => {
                loadBalance();
                loadTransactions();
                showAlert('✅ Withdrawal successful!', 'success');
            }).catch(error => {
                if (error.response && error.response.status === 400) {
                    showAlert('⚠️ Insufficient balance for withdrawal.');
                } else {
                    showAlert('❌ Withdrawal failed. Please try again.');
                }
            });
        });

        window.onload = () => {
            loadBalance();
            loadTransactions();
        };
    </script>
</body>
</html>