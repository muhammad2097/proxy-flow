<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Mandatory Login Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Database Connection
require_once 'db.php';

// 3. Refresh balance from DB to ensure accuracy during checkout
$uid = $_SESSION['user_id'];
$bal_query = $conn->query("SELECT balance FROM user_balances WHERE user_id = $uid");
$bal_data = $bal_query->fetch_assoc();
$current_balance = $bal_data['balance'] ?? 0.00;
$_SESSION['balance'] = $current_balance;

// 4. Get data from URL (passed from details.php)
$initial_qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
if ($initial_qty < 1) $initial_qty = 1;

$encoded_item = isset($_GET['item']) ? $_GET['item'] : '';
$item_name = $encoded_item ? base64_decode($encoded_item) : 'Aged YouTube Profile';
$unit_price = isset($_GET['price']) ? floatval($_GET['price']) : 14.00;

$initial_total = $initial_qty * $unit_price;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; }
        .sticky-nav { position: sticky; top: 0; z-index: 1000; background: rgba(5, 7, 10, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; color: white; width: 100%; outline: none; transition: 0.3s; }
        .input-field:focus { border-color: #3b82f6; background: rgba(255,255,255,0.08); }
        .accent-text { color: #3b82f6; }
        button:disabled { opacity: 0.5; cursor: not-allowed; filter: grayscale(1); }
    </style>
</head>
<body>

    <header class="sticky-nav">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="index.php" class="text-xl font-extrabold tracking-tighter text-white">AGED<span class="text-blue-500">PROFILE</span></a>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4 bg-white/5 border border-white/10 px-4 py-1.5 rounded-xl">
                    <div class="flex flex-col items-end border-r border-white/10 pr-4">
                        <span class="text-[8px] uppercase tracking-widest text-gray-500 font-bold leading-none mb-1">Wallet</span>
                        <span class="text-sm font-black text-blue-400 leading-none">$<?php echo number_format($current_balance, 2); ?></span>
                    </div>
                    <a href="topup.php" class="bg-blue-600 hover:bg-blue-700 p-1 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>
                <div class="hidden lg:flex flex-col items-end">
                    <span class="text-[8px] uppercase tracking-widest text-gray-500 font-bold leading-none">User</span>
                    <span class="text-xs font-bold text-white leading-none mt-1"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                </div>
                <a href="logout.php" class="text-gray-500 hover:text-red-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-16">
        <h1 class="text-4xl font-extrabold text-white mb-2 uppercase tracking-tighter">Secure <span class="accent-text">Checkout</span></h1>
        
        <form action="process-order.php" method="POST" class="space-y-8 mt-10">
            <div class="glass-panel p-8 rounded-[32px]">
                <h3 class="text-white font-bold mb-6 text-sm uppercase tracking-widest border-b border-white/5 pb-4">Order Summary</h3>
                
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-400 whitespace-nowrap">Item Name:</span>
                        <div class="text-right">
                            <span class="text-white font-bold block"><?php echo $item_name; ?></span>
                            <!-- <input type="hidden" name="item_name" value="<?php echo $item_name; ?>"> -->
                             <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item_name); ?>">
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Unit Price:</span>
                        <span class="text-white font-bold">$<?php echo number_format($unit_price, 2); ?></span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Selected Quantity:</span>
                        <input type="number" id="qty-input" name="quantity" value="<?php echo $initial_qty; ?>" min="1" required class="w-20 bg-white/10 rounded-lg p-2 text-center font-bold text-white focus:ring-1 ring-blue-500">
                    </div>

                    <div class="pt-6 border-t border-white/10 flex justify-between items-center">
                        <span class="text-white font-extrabold uppercase tracking-widest text-xs">Final Total Price:</span>
                        <span class="text-3xl font-black accent-text">$<span id="total-display"><?php echo number_format($initial_total, 2); ?></span></span>
                        <input type="hidden" name="total_price" id="total-hidden" value="<?php echo $initial_total; ?>">
                    </div>
                </div>
            </div>

            <div id="balance-alert" class="hidden glass-panel border-red-500/50 p-4 rounded-2xl flex items-center gap-4">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="text-red-400 font-bold text-xs uppercase tracking-widest">Insufficient Balance</p>
                    <p class="text-gray-400 text-[10px]">Your wallet has $<?php echo number_format($current_balance, 2); ?>. Please top up to complete this order.</p>
                </div>
                <a href="topup.php" class="ml-auto bg-red-500/20 text-red-400 px-4 py-2 rounded-lg text-[10px] font-bold border border-red-500/30 uppercase">Top Up Now</a>
            </div>

            <div class="glass-panel p-8 rounded-[32px]">
                <h3 class="text-white font-bold mb-6 text-sm uppercase tracking-widest border-b border-white/5 pb-4">Required Details</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Full Name *</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required class="input-field">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Mobile Number (WhatsApp Preferred) *</label>
                        <input type="tel" name="mobile_number" placeholder="e.g. +1 555 000 0000" required class="input-field">
                    </div>
                </div>
            </div>

            <button type="submit" id="submit-btn" class="w-full bg-blue-600 hover:bg-blue-700 py-6 rounded-2xl font-black text-xl text-white uppercase tracking-widest shadow-xl shadow-blue-600/20 transition-all">
                Confirm and Save Order
            </button>
        </form>
    </main>

    <footer class="py-12 text-center text-[10px] text-gray-600 uppercase tracking-[0.3em]">
        © 2026 Powered by Agedprofile
    </footer>

    <script>
        const qtyInput = document.getElementById('qty-input');
        const unitPrice = <?php echo $unit_price; ?>;
        const userBalance = <?php echo $current_balance; ?>;
        
        const totalDisplay = document.getElementById('total-display');
        const totalHidden = document.getElementById('total-hidden');
        const submitBtn = document.getElementById('submit-btn');
        const balanceAlert = document.getElementById('balance-alert');

        function checkBalance(total) {
            if (total > userBalance) {
                submitBtn.disabled = true;
                submitBtn.innerText = "Insufficient Wallet Balance";
                balanceAlert.classList.remove('hidden');
            } else {
                submitBtn.disabled = false;
                submitBtn.innerText = "Confirm and Save Order";
                balanceAlert.classList.add('hidden');
            }
        }

        // Initial Check
        checkBalance(<?php echo $initial_total; ?>);

        qtyInput.addEventListener('input', () => {
            const qty = parseInt(qtyInput.value) || 0;
            const total = (qty * unitPrice).toFixed(2);
            totalDisplay.innerText = total;
            totalHidden.value = total;
            checkBalance(parseFloat(total));
        });
    </script>
</body>
</html>