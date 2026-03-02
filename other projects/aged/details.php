<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Database Connection (Standard for your app)
require_once 'db.php';

// 2. Refresh balance from DB for the session if logged in
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $bal_query = $conn->query("SELECT balance FROM user_balances WHERE user_id = $uid");
    $bal_data = $bal_query->fetch_assoc();
    $_SESSION['balance'] = $bal_data['balance'] ?? 0.00;
}

// 3. Decode the single chunk: "Title|Price|Type"
$raw_data = isset($_GET['data']) ? base64_decode($_GET['data']) : '2006 – 2009|14|With Videos';
$parts = explode('|', $raw_data);

$item_name  = $parts[0] ?? '2006 – 2009';
$item_price = $parts[1] ?? '14';
$item_type  = $parts[2] ?? 'With Videos';

// 4. Logic for conditional text based on the "Type"
$has_videos = (strpos(strtolower($item_type), 'without') === false);
$video_desc = $has_videos ? 'Includes Uploaded Videos 1-5' : 'No Uploaded Videos (Clean History)';
$video_stat = $has_videos ? 'Yes 1-5' : 'None';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aged YouTube Profiles (<?php echo $item_name; ?>) | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; line-height: 1.6; }
        .sticky-nav { position: sticky; top: 0; z-index: 1000; background: rgba(5, 7, 10, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .buy-card { background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(2, 6, 23, 0.95)); border: 1px solid rgba(59, 130, 246, 0.3); }
        .warning-glow { border-left: 4px solid #ef4444; background: linear-gradient(90deg, rgba(239, 68, 68, 0.1), transparent); }
        .accent-text { color: #3b82f6; }
        .inventory-card:hover { border-color: #3b82f6; transform: translateY(-5px); transition: 0.3s; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body>

    <header class="sticky-nav">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="index.php" class="text-xl font-extrabold tracking-tighter text-white">AGED<span class="text-blue-500">PROFILE</span></a>
                <nav class="hidden md:flex gap-6 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    <a href="index.php" class="hover:text-blue-500 transition">Home</a>
                    <a href="#" class="text-white border-b border-blue-500">Shop</a>
                    <a href="#" class="hover:text-blue-500 transition">About</a>
                    <a href="#" class="hover:text-blue-500 transition">Contact</a>
                </nav>
            </div>
            
            <div class="flex items-center gap-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-4 bg-white/5 border border-white/10 px-4 py-1.5 rounded-xl">
                        <div class="flex flex-col items-end border-r border-white/10 pr-4">
                            <span class="text-[8px] uppercase tracking-widest text-gray-500 font-bold leading-none mb-1">Wallet</span>
                            <span class="text-sm font-black text-blue-400 leading-none">$<?php echo number_format($_SESSION['balance'], 2); ?></span>
                        </div>
                        <a href="topup.php" class="bg-blue-600 hover:bg-blue-700 p-1 rounded-lg transition" title="Add Funds">
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
                <?php else: ?>
                    <div class="flex items-center gap-4">
                        <a href="login.php" class="text-gray-400 hover:text-white text-xs font-bold uppercase tracking-widest">Login</a>
                        <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition uppercase tracking-widest">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pt-12">
        <div class="flex flex-col lg:flex-row gap-12 mb-20">
            <div class="flex-1">
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.3em] mb-4">Aged YouTube Profiles / <?php echo $item_name; ?></div>
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 tracking-tight">
                    Aged YouTube Profiles (<?php echo $item_name; ?>) <br><span class="accent-text"><?php echo $item_type; ?> | AgedProfile</span>
                </h1>
                
                <div class="flex items-center gap-6 mb-10">
                    <span class="text-4xl font-bold text-white">$<?php echo $item_price; ?></span>
                    <span class="bg-blue-500/10 text-blue-400 px-4 py-1 rounded-full text-xs font-bold border border-blue-500/20 tracking-widest uppercase">+ FREE SHIPPING</span>
                </div>

                <div class="space-y-6 text-gray-400 text-lg mb-12">
                    <p class="text-white font-semibold">Aged YouTube Profiles Originally Created <?php echo $item_name; ?>, <?php echo $item_type; ?> History and Natural Platform Activity.</p>
                    <p>These Profiles are Suitable for Buyers who Want Maximum Trust Signals, Older Creation Dates, and Reduced Early-Stage Platform Restrictions.</p>
                </div>

                <div class="glass-panel p-8 rounded-3xl mb-12">
                    <h3 class="accent-text font-bold mb-6 uppercase text-xs tracking-widest">What You Get</h3>
                    <ul class="text-sm space-y-4 text-gray-300">
                        <li>• YouTube Profiles Created Between <?php echo $item_name; ?></li>
                        <li>• <?php echo $video_desc; ?></li>
                        <li>• Older Creation Year for Higher Trust Signals</li>
                        <li>• Random Niche Distribution</li>
                        <li>• Tested Before Delivery for Login Access</li>
                    </ul>
                </div>

                <div class="mb-12">
                    <h3 class="text-white font-bold mb-6 uppercase text-xs tracking-widest">Account Overview</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5"><span class="block text-gray-500 mb-1">Age</span><span class="font-bold"><?php echo $item_name; ?></span></div>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5"><span class="block text-gray-500 mb-1">Videos</span><span class="font-bold"><?php echo $video_stat; ?></span></div>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5"><span class="block text-gray-500 mb-1">Subscribers</span><span class="font-bold">Random</span></div>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5"><span class="block text-gray-500 mb-1">Views</span><span class="font-bold">Random Organic</span></div>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl mb-12">
                    <h3 class="text-white font-bold mb-4">Delivery Information</h3>
                    <ul class="text-sm text-gray-400 space-y-2">
                        <li>• Manual Delivery Within 1 to 12 Hours After Payment Confirmation</li>
                        <li>• Account Details Shared via Email or WhatsApp</li>
                        <li class="italic text-gray-500">• If Not Received, Please Check Spam or Promotions folders</li>
                    </ul>
                </div>

                <div class="warning-glow p-8 rounded-2xl mb-12">
                    <h2 class="text-red-500 font-black text-xl mb-4 italic uppercase">Important Login Guidelines (VERY IMPORTANT)</h2>
                    <p class="text-gray-200 font-bold mb-4 italic underline">Do NOT Change Sensitive Settings for the first 7–14 days:</p>
                    <div class="grid grid-cols-2 gap-4 text-xs font-mono text-gray-400">
                        <span>[ ] Password</span>
                        <span>[ ] Recovery Email</span>
                        <span>[ ] Phone Number</span>
                        <span>[ ] 2FA Settings</span>
                        <span>[ ] Advanced Security</span>
                        <span>[ ] Advanced Security</span>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl mb-12">
                    <h4 class="text-white font-bold mb-4 uppercase text-[11px]">Verify It’s You Message (Normal Behavior)</h4>
                    <p class="text-xs text-gray-400 mb-6 leading-relaxed">Some Aged Profiles May Display a “Verify it’s you” Message When Accessed From a New Device, IP, or Location. This is a Normal Platform-Level Security Check.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h5 class="text-blue-400 font-bold text-[10px] uppercase mb-2">Why it happens:</h5>
                            <ul class="text-[10px] text-gray-500 space-y-1">
                                <li>• New Device/IP Login</li>
                                <li>• Long Inactivity Period</li>
                                <li>• Different Region</li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="text-blue-400 font-bold text-[10px] uppercase mb-2">Verification Help:</h5>
                            <p class="text-[10px] text-gray-500">Join our Community Group to access the walkthrough solution and step-by-step help.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl mb-12 border-l-4 border-l-blue-500">
                    <h2 class="text-xl font-bold mb-6 italic underline">Replacement Policy (7-Day Guarantee)</h2>
                    <div class="grid md:grid-cols-2 gap-8 text-xs leading-relaxed">
                        <div>
                            <h5 class="text-green-500 font-bold mb-4 uppercase tracking-widest">Available If:</h5>
                            <ul class="space-y-2 text-gray-400 italic">
                                <li>• Account Details Do Not Work Before First Login</li>
                                <li>• Screenshot and Order ID Provided</li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="text-red-500 font-bold mb-4 uppercase tracking-widest">NOT Available If:</h5>
                            <ul class="space-y-2 text-gray-400 italic">
                                <li>• Info Changed Immediately</li>
                                <li>• Multiple Device Logins</li>
                                <li>• Locked due to Unsafe Actions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-[420px]">
                <div class="buy-card p-10 rounded-[40px] sticky top-[100px] shadow-2xl">
                    <div class="text-[10px] font-bold text-blue-500 uppercase tracking-[0.4em] mb-4">Purchase Entry</div>
                    <h2 class="text-2xl font-bold text-white mb-8">Aged YouTube (<?php echo $item_name; ?>)</h2>
                    
                    <div class="flex items-center justify-between mb-8">
                        <span class="text-gray-500 font-medium">Quantity</span>
                        <input type="number" id="detail-qty" value="1" min="1" class="w-20 bg-white/5 border border-white/10 rounded-xl p-3 text-center text-white">
                    </div>

                    <button onclick="redirectToCheckout()" class="w-full bg-blue-600 hover:bg-blue-700 py-6 rounded-3xl font-black text-xl mb-8 uppercase tracking-widest shadow-xl shadow-blue-600/20 transition-all">
                        BUY NOW
                    </button>

                    <div class="space-y-6 pt-6 border-t border-white/5">
                        <div class="flex items-start gap-4">
                            <span class="text-blue-500 text-lg">🛡️</span>
                            <div class="text-[11px] text-gray-400">
                                <span class="font-bold text-white block mb-1 uppercase tracking-widest text-[9px]">Replacement Guarantee</span>
                                Replacement available if login fails. (Requires Screenshot & Order ID)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section id="inventory" class="py-24 border-t border-white/5">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-white italic uppercase tracking-tighter">Available Aged Profiles</h2>
                    <p class="text-gray-500 text-sm">
                        Browse other aged YouTube profiles by creation period below
                    </p>
                </div>
                <div class="text-[10px] font-bold bg-blue-600/10 text-blue-400 px-4 py-2 rounded-full border border-blue-500/20 uppercase tracking-widest">
                    Total Stock: 11,000+ Profiles
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="glass-panel p-8 rounded-3xl inventory-card transition-all">
                    <div class="flex justify-between mb-4">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">With Videos</span>
                        <span class="text-[10px] text-gray-500 font-mono">Stock: 150</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 text-white">2006 – 2009</h3>
                    <p class="text-gray-500 text-xs mb-8 italic">Premium legacy authority</p>
                    <div class="flex items-center justify-between border-t border-white/5 pt-6">
                        <span class="text-3xl font-bold text-white">$13</span>
                        <a href="details.php?data=<?php echo base64_encode('2006 – 2009|13|With Videos'); ?>" 
                           class="bg-white/5 hover:bg-white text-black px-4 py-2 rounded-lg text-[10px] font-bold transition uppercase">
                            View Details
                        </a>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl inventory-card transition-all">
                    <div class="flex justify-between mb-4">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">With Videos</span>
                        <span class="text-[10px] text-gray-500 font-mono">Stock: 1734</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 text-white">2010 – 2014</h3>
                    <p class="text-gray-500 text-xs mb-8 italic">Premium legacy authority</p>
                    <div class="flex items-center justify-between border-t border-white/5 pt-6">
                        <span class="text-3xl font-bold text-white">$11</span>
                        <a href="details.php?data=<?php echo base64_encode('2010 – 2014|11|With Videos'); ?>" 
                           class="bg-white/5 hover:bg-white text-black px-4 py-2 rounded-lg text-[10px] font-bold transition uppercase">
                            View Details
                        </a>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl inventory-card transition-all">
                    <div class="flex justify-between mb-4">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">With Videos</span>
                        <span class="text-[10px] text-gray-500 font-mono">Stock: 2195</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 text-white">2015 – 2020</h3>
                    <p class="text-gray-500 text-xs mb-8 italic">Established algorithm trust</p>
                    <div class="flex items-center justify-between border-t border-white/5 pt-6">
                        <span class="text-3xl font-bold text-white">$6</span>
                        <a href="details.php?data=<?php echo base64_encode('2015 – 2020|6|With Videos'); ?>" 
                           class="bg-white/5 hover:bg-white text-black px-4 py-2 rounded-lg text-[10px] font-bold transition uppercase">
                            View Details
                        </a>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl inventory-card transition-all">
                    <div class="flex justify-between mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Without Videos</span>
                        <span class="text-[10px] text-gray-500 font-mono">Stock: 745</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 text-white">2006 – 2009</h3>
                    <p class="text-gray-500 text-xs mb-8 italic">Clean legacy accounts</p>
                    <div class="flex items-center justify-between border-t border-white/5 pt-6">
                        <span class="text-3xl font-bold text-white">$8</span>
                        <a href="details.php?data=<?php echo base64_encode('2006 – 2009|8|Without Videos'); ?>" 
                           class="bg-white/5 hover:bg-white text-black px-4 py-2 rounded-lg text-[10px] font-bold transition uppercase">
                            View Details
                        </a>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl inventory-card transition-all">
                    <div class="flex justify-between mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Without Videos</span>
                        <span class="text-[10px] text-gray-500 font-mono">Stock: 745</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 text-white">2011 – 2019</h3>
                    <p class="text-gray-500 text-xs mb-8 italic">Clean legacy accounts</p>
                    <div class="flex items-center justify-between border-t border-white/5 pt-6">
                        <span class="text-3xl font-bold text-white">$6</span>
                        <a href="details.php?data=<?php echo base64_encode('2011 – 2019|6|Without Videos'); ?>" 
                           class="bg-white/5 hover:bg-white text-black px-4 py-2 rounded-lg text-[10px] font-bold transition uppercase">
                            View Details
                        </a>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl inventory-card transition-all">
                    <div class="flex justify-between mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Without Videos</span>
                        <span class="text-[10px] text-gray-500 font-mono">Stock: 745</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-1 text-white">2020 – 2024</h3>
                    <p class="text-gray-500 text-xs mb-8 italic">Clean legacy accounts</p>
                    <div class="flex items-center justify-between border-t border-white/5 pt-6">
                        <span class="text-3xl font-bold text-white">$4</span>
                        <a href="details.php?data=<?php echo base64_encode('2020 – 2024|4|Without Videos'); ?>" 
                           class="bg-white/5 hover:bg-white text-black px-4 py-2 rounded-lg text-[10px] font-bold transition uppercase">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-4xl mx-auto pb-20">
            <h2 class="text-center text-3xl font-bold mb-12 italic text-white uppercase underline decoration-blue-500 underline-offset-8">FAQ's</h2>
            <div class="space-y-4">
                <div class="glass-panel p-6 rounded-2xl border-white/5 hover:border-blue-500/20 transition">
                    <h4 class="font-bold text-blue-400 text-sm mb-2 italic underline underline-offset-4">Are these monetized YouTube channels?</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">No. These are Aged YouTube Profiles With Existing History. Monetization Depends on Future Usage and YouTube Policies.</p>
                </div>
                <div class="glass-panel p-6 rounded-2xl border-white/5 hover:border-blue-500/20 transition">
                    <h4 class="font-bold text-blue-400 text-sm mb-2 italic underline underline-offset-4">How Fast Will I Receive The Details?</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Delivery is Done Manually Within 1–10 Hours After Payment Confirmation via Email or WhatsApp.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-12 text-center border-t border-white/5 text-[10px] text-gray-600 uppercase tracking-[0.3em]">
        © 2026 Powered by Agedprofile
    </footer>

    <script>
    function redirectToCheckout() {
        const qty = document.getElementById('detail-qty').value;
        const itemName = "<?php echo base64_encode($item_name . ' (' . $item_type . ')'); ?>";
        const itemPrice = "<?php echo $item_price; ?>";
        window.location.href = `buy-now.php?qty=${qty}&item=${itemName}&price=${itemPrice}`;
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>