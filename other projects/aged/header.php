<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Database Connection (Standard for your app)
require_once 'db.php';

// Refresh balance from DB for the session
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $bal_query = $conn->query("SELECT balance FROM user_balances WHERE user_id = $uid");
    $bal_data = $bal_query->fetch_assoc();
    $_SESSION['balance'] = $bal_data['balance'] ?? 0.00;
}
?>
<nav class="sticky top-0 z-50 glass border-b border-white/5 px-6 py-4 mb-8">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <a href="index.php" class="text-xl font-extrabold tracking-tighter text-white">
      AGED<span class="text-blue-500">PROFILE</span>
    </a>

    <div class="flex items-center gap-6">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="flex items-center gap-4 bg-white/5 border border-white/10 px-4 py-2 rounded-2xl">
          <div class="flex flex-col items-end border-r border-white/10 pr-4">
            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold leading-none mb-1">Balance</span>
            <span class="text-base font-black text-blue-400 leading-none">$<?php echo number_format($_SESSION['balance'], 2); ?></span>
          </div>
          <a href="topup.php" class="bg-blue-600 hover:bg-blue-700 p-1.5 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
          </a>
        </div>
        <div class="hidden md:flex flex-col items-end">
          <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold leading-none">Account</span>
          <span class="text-xs font-bold text-white"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        </div>
        <a href="logout.php" class="text-gray-500 hover:text-red-500 transition"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2"/></svg></a>
      <?php else: ?>
        <a href="login.php" class="text-gray-400 hover:text-white text-[10px] font-bold uppercase tracking-widest transition">Login</a>
        <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold transition uppercase tracking-widest">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</nav>