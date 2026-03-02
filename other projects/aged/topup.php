<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Mandatory Login Check
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

// 2. Database Connection
require_once 'db.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $user_id = $_SESSION['user_id'];
    
    if ($amount > 0) {
        // Insert as PENDING for admin verification
        $stmt = $conn->prepare("INSERT INTO topup_requests (user_id, amount, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("id", $user_id, $amount);
        
        if ($stmt->execute()) {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Up Balance | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .accent-blue { color: #3b82f6; }
        .success-glow { box-shadow: 0 0 40px rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-5">
                <h1 class="text-4xl font-black text-white uppercase tracking-tighter mb-2">Top Up <span class="accent-blue">Wallet</span></h1>
                <p class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.3em] mb-10">Add funds to your account via manual verification</p>

                <?php if($success): ?>
                    <div class="glass-panel p-6 rounded-3xl mb-8 border-blue-500/30 success-glow">
                        <div class="flex items-center gap-4 mb-3">
                            <span class="bg-blue-500/20 p-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </span>
                            <span class="text-blue-400 font-bold uppercase text-xs tracking-widest">Request Logged</span>
                        </div>
                        <p class="text-gray-400 text-xs leading-relaxed">Your top-up request is now <strong>Pending</strong>. Please follow the instructions on the right to send payment and proof.</p>
                    </div>
                <?php endif; ?>

                <form method="POST" class="glass-panel p-8 rounded-[32px] space-y-8">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-4 ml-1 tracking-widest">Amount to Recharge ($)</label>
                        <input type="number" name="amount" min="1" step="0.01" required class="w-full bg-white/5 border border-white/10 p-6 rounded-2xl text-4xl font-black text-white outline-none focus:border-blue-500 transition" placeholder="0.00">
                    </div>

                    <div class="bg-white/5 p-6 rounded-2xl border border-white/5">
                        <h4 class="text-white font-bold text-[10px] uppercase tracking-widest mb-4 italic">How it works:</h4>
                        <ul class="text-[11px] text-gray-400 space-y-3">
                            <li class="flex gap-3">
                                <span class="text-blue-500 font-bold">01.</span>
                                <span>Enter the amount you wish to add and click Submit Request.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-blue-500 font-bold">02.</span>
                                <span>Transfer the <strong>EXACT</strong> amount to any payment method shown.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-blue-500 font-bold">03.</span>
                                <span>Send your screenshot and username to our official WhatsApp.</span>
                            </li>
                        </ul>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-6 rounded-2xl font-black text-xl text-white uppercase tracking-widest shadow-xl shadow-blue-600/20 transition-all">
                        Submit Top Up Request
                    </button>
                </form>

                <div class="mt-10 p-8 glass-panel rounded-[32px] border-l-4 border-blue-500 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.4em] mb-4">Official Verification</p>
                    <p class="text-gray-400 text-xs mb-4 italic">Send payment proof to:</p>
                    <a href="https://wa.me/923001223456" class="text-3xl md:text-4xl font-black text-white hover:text-blue-400 transition-colors tracking-tighter">
                        +92 300 1223456
                    </a>
                    <div class="mt-6 flex items-center justify-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] font-bold text-green-500 uppercase tracking-widest">Online for Verification</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="glass-panel p-4 rounded-[40px] border border-white/10 sticky top-24">
                    <div class="p-4 text-center">
                        <span class="bg-blue-500/10 text-blue-400 text-[10px] font-black px-6 py-2 rounded-full border border-blue-500/20 uppercase tracking-widest">Accepted Payment Methods</span>
                    </div>
                    
                    <div class="bg-white/5 p-4 rounded-3xl mt-4 flex justify-center items-center">
                        <img 
                            src="payment.jpeg" 
                            alt="Payment Methods" 
                            class="block mx-auto rounded-2xl border border-white/5 shadow-2xl"
                            style="max-width: 100%; height: auto; object-fit: contain;"
                        >
                    </div>
                    
                    <div class="p-6">
                        <p class="text-[11px] text-gray-500 font-bold uppercase italic text-center leading-relaxed">
                            Please ensure your transaction ID and the amount are clearly visible in the screenshot before sending it to WhatsApp for approval.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="py-12 text-center text-[10px] text-gray-600 uppercase tracking-[0.3em]">
        © 2026 Powered by Agedprofile
    </footer>

</body>
</html>