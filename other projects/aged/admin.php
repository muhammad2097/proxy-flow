<?php
session_start();

// 1. Security & Rights Check
$secret_key = "admin|HelloAgedProfileAdmin|Orders";
$provided_key = isset($_GET['key']) ? base64_decode($_GET['key']) : '';

if ($provided_key !== $secret_key) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

// 3. Authorization: Database Role Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$user_check = $conn->query("SELECT role FROM users WHERE id = $current_user_id");
$user_data = $user_check->fetch_assoc();

if (!$user_data || strtolower(trim($user_data['role'])) !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit;
}

// 4. Handle Status Updates (Irreversible Logic)
if (isset($_POST['action']) && isset($_POST['id']) && isset($_POST['type'])) {
    $id = intval($_POST['id']);
    $new_status = strtolower(trim($_POST['action'])); // Force lowercase
    $type = strtolower(trim($_POST['type']));

    if ($type === 'profile') {
        $stmt = $conn->prepare("UPDATE aged_order SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        $stmt->execute();
    } 
    elseif ($type === 'balance') {
        $conn->begin_transaction();
        try {
            // Check current status using strtolower
            $req_query = $conn->query("SELECT user_id, amount, status FROM topup_requests WHERE id = $id");
            $req = $req_query->fetch_assoc();
            
            if ($req && strtolower(trim($req['status'])) === 'pending') {
                $stmt = $conn->prepare("UPDATE topup_requests SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $new_status, $id);
                $stmt->execute();

                if ($new_status === 'approved') {
                    $upd = $conn->prepare("UPDATE user_balances SET balance = balance + ? WHERE user_id = ?");
                    $upd->bind_param("di", $req['amount'], $req['user_id']);
                    $upd->execute();

                    $log = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'credit', 'Wallet Top-up Approved')");
                    $log->bind_param("id", $req['user_id'], $req['amount']);
                    $log->execute();
                }
                $conn->commit();
            }
        } catch (Exception $e) { $conn->rollback(); }
    }
    header("Location: admin.php?key=" . base64_encode($secret_key));
    exit;
}

// 5. Fetch Data
$profile_orders = $conn->query("SELECT * FROM aged_order ORDER BY order_date DESC");
$balance_requests = $conn->query("SELECT t.*, u.name as user_name FROM topup_requests t JOIN users u ON t.user_id = u.id ORDER BY request_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .tab-active { border-bottom: 2px solid #3b82f6; color: white; }
        .status-pending { color: #f59e0b; background: rgba(245, 158, 11, 0.1); }
        .status-approved { color: #10b981; background: rgba(16, 185, 129, 0.1); }
        .status-declined { color: #ef4444; background: rgba(239, 68, 68, 0.1); }
    </style>
</head>
<body class="p-8">

    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">System <span class="text-blue-500">Master</span></h1>
                <p class="text-gray-500 text-sm">Reviewing Profile Orders & Top-up Requests</p>
            </div>
            <div class="glass-panel px-6 py-2 rounded-full text-xs font-bold text-green-400 border-green-500/20">
                Admin Session Active
            </div>
        </header>

        <div class="flex gap-8 mb-8 border-b border-white/5 text-xs font-bold uppercase tracking-widest text-gray-500">
            <button onclick="switchTab('profile-tab')" id="btn-profile" class="pb-4 tab-active transition-all">Aged Profile Orders</button>
            <button onclick="switchTab('balance-tab')" id="btn-balance" class="pb-4 transition-all">Balance Request Orders</button>
        </div>

        <div id="profile-tab" class="tab-content">
            <div class="glass-panel rounded-[32px] overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-400">
                            <th class="p-6">Order ID</th>
                            <th class="p-6">Customer</th>
                            <th class="p-6">Item</th>
                            <th class="p-6">Amount</th>
                            <th class="p-6">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php while($row = $profile_orders->fetch_assoc()): 
                            $stat = strtolower(trim($row['status'])); ?>
                        <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                            <td class="p-6 font-mono font-bold text-blue-400"><?php echo $row['order_id']; ?></td>
                            <td class="p-6">
                                <div class="font-bold text-white"><?php echo $row['full_name']; ?></div>
                                <div class="text-[10px] text-gray-500"><?php echo $row['mobile']; ?></div>
                            </td>
                            <td class="p-6 text-gray-400"><?php echo $row['item_name']; ?></td>
                            <td class="p-6 font-black text-white">$<?php echo $row['total_price']; ?></td>
                            <td class="p-6">
                                <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase status-<?php echo $stat; ?>">
                                    <?php echo $stat; ?>
                                </span>
                            </td>
                            <td class="p-6 text-right">
                                <?php if($stat === 'pending'): ?>
                                    <button onclick="confirmAction(<?php echo $row['id']; ?>, 'approved', 'profile')" class="text-green-500 hover:text-white transition mr-4 font-bold text-[10px] uppercase">Approve</button>
                                    <button onclick="confirmAction(<?php echo $row['id']; ?>, 'declined', 'profile')" class="text-red-500 hover:text-white transition font-bold text-[10px] uppercase">Decline</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="balance-tab" class="tab-content hidden">
            <div class="glass-panel rounded-[32px] overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[10px] uppercase tracking-widest text-gray-400">
                            <th class="p-6">User</th>
                            <th class="p-6">Amount</th>
                            <th class="p-6">Date</th>
                            <th class="p-6">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php while($row = $balance_requests->fetch_assoc()): 
                            $stat = strtolower(trim($row['status'])); ?>
                        <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                            <td class="p-6 font-bold text-white"><?php echo $row['user_name']; ?></td>
                            <td class="p-6 font-black text-blue-400">$<?php echo number_format($row['amount'], 2); ?></td>
                            <td class="p-6 text-gray-500 text-xs"><?php echo $row['request_date']; ?></td>
                            <td class="p-6">
                                <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase status-<?php echo $stat; ?>">
                                    <?php echo $stat; ?>
                                </span>
                            </td>
                            <td class="p-6 text-right">
                                <?php if($stat === 'pending'): ?>
                                    <button onclick="confirmAction(<?php echo $row['id']; ?>, 'approved', 'balance')" class="text-green-500 hover:text-white transition mr-4 font-bold text-[10px] uppercase">Approve</button>
                                    <button onclick="confirmAction(<?php echo $row['id']; ?>, 'declined', 'balance')" class="text-red-500 hover:text-white transition font-bold text-[10px] uppercase">Decline</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form id="action-form" method="POST" class="hidden">
        <input type="hidden" name="id" id="form-id">
        <input type="hidden" name="action" id="form-action">
        <input type="hidden" name="type" id="form-type">
    </form>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('button[id^="btn-"]').forEach(el => el.classList.remove('tab-active'));
            document.getElementById(tabId).classList.remove('hidden');
            if(tabId === 'profile-tab') document.getElementById('btn-profile').classList.add('tab-active');
            else document.getElementById('btn-balance').classList.add('tab-active');
        }

        function confirmAction(id, action, type) {
            if (confirm(`Are you sure you want to ${action.toUpperCase()} this request? This cannot be undone.`)) {
                document.getElementById('form-id').value = id;
                document.getElementById('form-action').value = action;
                document.getElementById('form-type').value = type;
                document.getElementById('action-form').submit();
            }
        }
    </script>
</body>
</html>