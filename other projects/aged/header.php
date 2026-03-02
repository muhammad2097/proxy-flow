<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

// Generate Admin Key for URL
$admin_key = base64_encode("admin|HelloAgedProfileAdmin|Orders");
?>

<header class="glass-panel sticky top-0 z-50 border-b border-white/5 bg-[#05070a]/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        
        <a href="index.php" class="flex items-center group">
            <span class="text-xl font-bold text-white uppercase tracking-tighter">Aged<span class="text-blue-500">Profile</span></span>
        </a>

        <div class="flex items-center gap-6">
            
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <?php if (isset($_SESSION['role']) && strtolower(trim($_SESSION['role'])) === 'admin'): ?>
                    <a href="admin-orders.php?key=<?php echo $admin_key; ?>" class="hidden md:flex flex-col items-center bg-yellow-500/10 border border-yellow-500/20 px-4 py-1 rounded-xl hover:bg-yellow-500 group transition-all">
                        <span class="text-[7px] uppercase tracking-[0.2em] text-yellow-500 group-hover:text-black font-black leading-none mb-1">Master Admin</span>
                        <span class="text-[10px] font-bold text-yellow-500 group-hover:text-black uppercase leading-none">Orders List</span>
                    </a>
                <?php endif; ?>

                <div class="flex items-center gap-4 bg-white/5 border border-white/10 px-4 py-1 rounded-xl">
                    <div class="flex flex-col items-end border-r border-white/10 pr-4">
                        <span class="text-[8px] uppercase tracking-widest text-gray-500 font-bold leading-none mb-1">Wallet</span>
                        <span class="text-sm font-black text-blue-400 leading-none">$<?php echo number_format($_SESSION['balance'] ?? 0, 2); ?></span>
                    </div>
                    <a href="topup.php" class="bg-blue-600 hover:bg-blue-700 p-1 rounded-lg transition-all" title="Add Funds">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>

                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[8px] uppercase tracking-widest text-gray-500 font-bold leading-none">User</span>
                    <span class="text-xs font-bold text-white leading-none mt-1"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Account'); ?></span>
                </div>

                <a href="logout.php" class="text-gray-500 hover:text-red-500 transition-colors" title="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>

            <?php else: ?>
                <div class="flex items-center gap-6">
                    <a href="login.php" class="text-gray-400 hover:text-white text-xs font-bold uppercase tracking-widest transition-colors">Login</a>
                    <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all">Get Started</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>