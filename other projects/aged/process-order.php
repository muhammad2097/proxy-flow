<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Database Connection
require_once 'db.php';

// 2. Auth & Session Information Retrieval
// Ensuring the user is logged in and capturing session data
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized Access: Please login to place an order.");
}

$user_id     = $_SESSION['user_id']; // Captured from session
$full_name   = $_POST['full_name'] ?? 'Guest';
$mobile      = $_POST['mobile_number'] ?? 'N/A';
$item_name   = $_POST['item_name'] ?? 'Aged YouTube Profile';
$qty         = intval($_POST['quantity'] ?? 1);
$total_price = floatval($_POST['total_price'] ?? 0.00);
$order_id    = 'AP-' . strtoupper(uniqid()); 
$status      = 'pending';

$success = false;
$error_message = "";

// 3. --- TRANSACTION-BASED CALCULATION & PROCESSING ---
// This atomic transaction ensures balance deduction and order saving happen together
$conn->begin_transaction();

try {
    // A. Detect Balance from DB (using FOR UPDATE to lock the row)
    $stmt = $conn->prepare("SELECT balance FROM user_balances WHERE user_id = ? FOR UPDATE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $current_balance = $res['balance'] ?? 0.00;

    // B. Balance Validation
    if ($current_balance < $total_price) {
        throw new Exception("Insufficient balance. Your wallet has $" . number_format($current_balance, 2) . ". Please top up.");
    }

    // C. Deduct Balance from DB
    $new_balance = $current_balance - $total_price;
    $update_bal = $conn->prepare("UPDATE user_balances SET balance = ? WHERE user_id = ?");
    $update_bal->bind_param("di", $new_balance, $user_id);
    $update_bal->execute();

// D. Save Order to 'aged_order' Table
// Columns: order_id, userId, full_name, mobile, item_name, total_price, quantity, status
$order_stmt = $conn->prepare("INSERT INTO aged_order (order_id, userId, full_name, mobile, item_name, total_price, quantity, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

/**
 * CORRECTED MAPPING:
 * 1. order_id    -> s
 * 2. userId      -> i
 * 3. full_name   -> s
 * 4. mobile      -> s
 * 5. item_name   -> s 
 * 6. total_price -> d 
 * 7. quantity    -> i (Changed 'l' to 'i')
 * 8. status      -> s
 */
$order_types = "sisssids"; 

$order_stmt->bind_param(
    $order_types, 
    $order_id,    // 1 (s)
    $user_id,     // 2 (i)
    $full_name,   // 3 (s)
    $mobile,      // 4 (s)
    $item_name,   // 5 (s)
    $total_price, // 6 (d)
    $qty,         // 7 (i)
    $status       // 8 (s)
);

if (!$order_stmt->execute()) {
    throw new Exception("Order storage failed: " . $order_stmt->error);
}

    // E. Log Transaction with Order ID
    $log_stmt = $conn->prepare("INSERT INTO transactions (user_id, order_id, amount, type, description) VALUES (?, ?, ?, 'debit', ?)");
    $description = "Purchased: " . $item_name;
    $log_stmt->bind_param("isds", $user_id, $order_id, $total_price, $description);
    $log_stmt->execute();

    // F. Finalize
    $conn->commit();
    $success = true;
    
    // Update the session balance so it updates in the header display
    $_SESSION['balance'] = $new_balance;

} catch (Exception $e) {
    // If anything fails, rollback the database to its previous state
    $conn->rollback();
    $success = false;
    $error_message = $e->getMessage();
}

$display_total = number_format($total_price, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .success-glow { box-shadow: 0 0 50px rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.2); }
        .accent-green { color: #22c55e; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="max-w-7xl w-full mx-auto p-6 md:p-10">
        <?php if ($success): ?>
            
            <div class="text-center mb-12">
                <div class="mb-6 inline-flex items-center justify-center w-24 h-24 bg-green-500/10 rounded-full border border-green-500/20 success-glow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h1 class="text-5xl font-extrabold text-white mb-2 tracking-tighter uppercase">Order <span class="accent-green">Received!</span></h1>
                <p class="text-gray-400 max-w-2xl mx-auto uppercase text-[10px] tracking-[0.3em] font-bold">Follow the payment steps below to finalize your order</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                <div class="lg:col-span-5 space-y-6">
                    <div class="glass-panel p-8 rounded-[32px]">
                        <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6 border-b border-white/5 pb-4">Invoice Details</h3>
                        
                        <div class="space-y-5">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Order ID:</span>
                                <span class="font-mono font-bold text-white text-lg"><?php echo $order_id; ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Product:</span>
                                <span class="text-white font-semibold text-right"><?php echo $item_name; ?></span>
                            </div>
                            <div class="pt-4 border-t border-white/5 flex justify-between items-center">
                                <span class="text-gray-400 font-extrabold uppercase text-xs">Total Payable:</span>
                                <span class="text-3xl font-black text-green-400">$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-600/5 border border-blue-500/20 p-6 rounded-2xl">
                        <h4 class="text-white font-bold mb-3 uppercase text-[10px] tracking-widest italic">Important Instruction</h4>
                        <p class="text-xs text-gray-400 leading-relaxed italic">
                            Choose <span class="text-white font-bold">ANY</span> payment method from the list. After sending <span class="text-white font-bold">$<?php echo number_format($total_price, 2); ?></span>, take a screenshot of this receipt and the transaction to WhatsApp.
                        </p>
                    </div>

                    <a href="https://wa.me/923482727141?text=Order%20Verification%20<?php echo $order_id; ?>%20For%20Youtube%20Aged%20Profile" class="flex items-center justify-center gap-3 w-full bg-white text-black py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-gray-200 transition-all shadow-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Verify via WhatsApp
                    </a>
                </div>

                <div class="lg:col-span-7">
                    <div class="glass-panel p-3 rounded-[40px] border border-white/10">
                        <div class="p-4 text-center">
                            <span class="bg-blue-500/10 text-blue-400 text-[10px] font-black px-4 py-1.5 rounded-full border border-blue-500/20 uppercase tracking-widest">Available Payment Methods</span>
                        </div>
                        
                        <div class="bg-white/5 p-4 rounded-3xl mb-4 flex justify-center items-start">
                            <img 
                                src="payment.jpeg" 
                                alt="Payment Methods" 
                                class="block mx-auto rounded-2xl border border-white/5 shadow-2xl"
                                style="max-width: 100%; height: auto; object-fit: contain;"
                            >
                        </div>
                        
                        <div class="p-4 text-center">
                            <p class="text-[10px] text-gray-500 font-bold uppercase italic">Please ensure all details are clearly visible in your screenshot</p>
                        </div>
                    </div>
                </div>

            </div>

        <?php else: ?>
            <div class="text-center py-20">
                <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-red-500/10 rounded-full border border-red-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
                <h1 class="text-white font-bold text-2xl uppercase">Transaction Failed</h1>
                <p class="text-gray-400 mt-2"><?php echo $error_message ?: "Could not save order. Please check your connection and try again."; ?></p>
                <div class="mt-8">
                    <a href="topup.php" class="bg-blue-600 px-8 py-3 rounded-xl font-bold uppercase tracking-widest hover:bg-blue-700 transition">Recharge Wallet</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>