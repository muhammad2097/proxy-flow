<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Database Connection
require_once 'db.php';

// 2. Refresh balance from DB for the session if logged in
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $bal_query = $conn->query("SELECT balance FROM user_balances WHERE user_id = $uid");
    $bal_data = $bal_query->fetch_assoc();
    $_SESSION['balance'] = $bal_data['balance'] ?? 0.00;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgedProfile | Skip the New Channel Phase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: "Plus Jakarta Sans", sans-serif;
        background-color: #050505;
        color: white;
        scroll-behavior: smooth;
      }
      .glass {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
      }
      .hero-gradient {
        background: radial-gradient(circle at 50% 50%, #1e40af 0%, #050505 70%);
      }
      .card-hover:hover {
        border-color: #3b82f6;
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -20px rgba(59, 130, 246, 0.4);
      }
    </style>
  </head>
  <body>
    <nav class="sticky top-0 z-50 glass border-b border-white/5 px-6 py-4">
      <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="text-xl font-extrabold tracking-tighter">
          AGED<span class="text-blue-500">PROFILE</span>
        </div>

        <div class="flex items-center gap-8">
            <div class="hidden md:flex gap-8 text-sm font-medium text-gray-400">
                <a href="#inventory" class="hover:text-blue-400 transition">Inventory</a>
                <a href="#why" class="hover:text-blue-400 transition">Why Aged?</a>
                <a href="#faq" class="hover:text-blue-400 transition">FAQ</a>
            </div>

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
                    <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-bold transition">Get Started</a>
                </div>
            <?php endif; ?>
        </div>
      </div>
    </nav>

    <section class="hero-gradient pt-24 pb-20 px-6">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl md:text-7xl font-extrabold mb-6 tracking-tight">
          Aged YouTube Profiles for a
          <span class="text-blue-500">Faster Start</span>
        </h1>
        <p class="text-xl text-gray-300 mb-8 leading-relaxed">
          Many creators notice that brand-new YouTube profiles struggle
          early—low impressions, delayed reach, and slow algorithm pickup.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <a
            href="#inventory"
            class="bg-white text-black px-8 py-4 rounded-xl font-bold hover:bg-blue-500 hover:text-white transition"
            >Browse Inventory</a
          >
        </div>
      </div>
    </section>

    <section id="inventory" class="py-24 px-6 max-w-7xl mx-auto">
      <div
        class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4"
      >
        <div>
          <h2 class="text-3xl font-bold mb-2">Available Aged Profiles</h2>
          <p class="text-gray-500">
            Browse aged YouTube profiles by creation period below
          </p>
        </div>
        <div
          class="text-sm bg-blue-600/10 text-blue-400 px-4 py-2 rounded-full border border-blue-500/20"
        >
          Total Stock: 11,000+ Profiles
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="glass p-8 rounded-3xl card-hover transition-all">
          <div class="flex justify-between mb-4">
            <span
              class="text-xs font-bold text-blue-500 uppercase tracking-widest"
              >With Videos</span
            >
            <span class="text-xs text-gray-500 font-mono">Stock: 150</span>
          </div>
          <h3 class="text-2xl font-bold mb-1">2006 – 2009</h3>
          <p class="text-gray-500 text-sm mb-8">Premium legacy authority</p>
          <div
            class="flex items-center justify-between border-t border-white/5 pt-6"
          >
            <span class="text-3xl font-bold text-white">$13</span>
            <a
              href="details.php?data=<?php echo base64_encode('2006 – 2009|13|With Videos'); ?>"
              class="bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-semibold transition"
              >View Details</a
            >
          </div>
        </div>

        <div class="glass p-8 rounded-3xl card-hover transition-all">
          <div class="flex justify-between mb-4">
            <span
              class="text-xs font-bold text-blue-500 uppercase tracking-widest"
              >With Videos</span
            >
            <span class="text-xs text-gray-500 font-mono">Stock: 1734</span>
          </div>
          <h3 class="text-2xl font-bold mb-1">2010 – 2014</h3>
          <p class="text-gray-500 text-sm mb-8">Premium legacy authority</p>
          <div
            class="flex items-center justify-between border-t border-white/5 pt-6"
          >
            <span class="text-3xl font-bold text-white">$11</span>
            <a
              href="details.php?data=<?php echo base64_encode('2010 – 2014|11|With Videos'); ?>"
              class="bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-semibold transition"
              >View Details</a
            >
          </div>
        </div>

        <div class="glass p-8 rounded-3xl card-hover transition-all">
          <div class="flex justify-between mb-4">
            <span
              class="text-xs font-bold text-blue-500 uppercase tracking-widest"
              >With Videos</span
            >
            <span class="text-xs text-gray-500 font-mono">Stock: 2195</span>
          </div>
          <h3 class="text-2xl font-bold mb-1">2015 – 2020</h3>
          <p class="text-gray-500 text-sm mb-8">Established algorithm trust</p>
          <div
            class="flex items-center justify-between border-t border-white/5 pt-6"
          >
            <span class="text-3xl font-bold text-white">$6</span>
            <a
              href="details.php?data=<?php echo base64_encode('2015 – 2020|6|With Videos'); ?>"
              class="bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-semibold transition"
              >View Details</a
            >
          </div>
        </div>

        <div class="glass p-8 rounded-3xl card-hover transition-all">
          <div class="flex justify-between mb-4">
            <span
              class="text-xs font-bold text-gray-400 uppercase tracking-widest"
              >Without Videos</span
            >
            <span class="text-xs text-gray-500 font-mono">Stock: 745</span>
          </div>
          <h3 class="text-2xl font-bold mb-1">2006 – 2009</h3>
          <p class="text-gray-500 text-sm mb-8">Clean legacy accounts</p>
          <div
            class="flex items-center justify-between border-t border-white/5 pt-6"
          >
            <span class="text-3xl font-bold text-white">$8</span>
            <a
              href="details.php?data=<?php echo base64_encode('2006 – 2009|8|Without Videos'); ?>"
              class="bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-semibold transition"
              >View Details</a
            >
          </div>
        </div>

        <div class="glass p-8 rounded-3xl card-hover transition-all">
          <div class="flex justify-between mb-4">
            <span
              class="text-xs font-bold text-gray-400 uppercase tracking-widest"
              >Without Videos</span
            >
            <span class="text-xs text-gray-500 font-mono">Stock: 745</span>
          </div>
          <h3 class="text-2xl font-bold mb-1">2011 – 2019</h3>
          <p class="text-gray-500 text-sm mb-8">Clean legacy accounts</p>
          <div
            class="flex items-center justify-between border-t border-white/5 pt-6"
          >
            <span class="text-3xl font-bold text-white">$6</span>
            <a
              href="details.php?data=<?php echo base64_encode('2011 – 2019|6|Without Videos'); ?>"
              class="bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-semibold transition"
              >View Details</a
            >
          </div>
        </div>

        <div class="glass p-8 rounded-3xl card-hover transition-all">
          <div class="flex justify-between mb-4">
            <span
              class="text-xs font-bold text-gray-400 uppercase tracking-widest"
              >Without Videos</span
            >
            <span class="text-xs text-gray-500 font-mono">Stock: 745</span>
          </div>
          <h3 class="text-2xl font-bold mb-1">2020 – 2024</h3>
          <p class="text-gray-500 text-sm mb-8">Clean legacy accounts</p>
          <div
            class="flex items-center justify-between border-t border-white/5 pt-6"
          >
            <span class="text-3xl font-bold text-white">$4</span>
            <a
              href="details.php?data=<?php echo base64_encode('2020 – 2024|4|Without Videos'); ?>"
              class="bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-semibold transition"
              >View Details</a
            >
          </div>
        </div>
      </div>
    </section>

    <section id="why" class="bg-blue-600 py-24 px-6">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-4xl font-extrabold mb-12 text-center">
          Why Creators Choose Aged Profiles
        </h2>
        <div class="grid md:grid-cols-2 gap-8">
          <div class="bg-black/20 p-6 rounded-2xl flex gap-4">
            <span class="text-2xl">🚀</span>
            <p class="font-medium">
              Skip the early new-channel waiting phase and avoid slow
              impressions.
            </p>
          </div>
          <div class="bg-black/20 p-6 rounded-2xl flex gap-4">
            <span class="text-2xl">⚙️</span>
            <p class="font-medium">
              Creators prefer starting without initial algorithm friction.
            </p>
          </div>
          <div class="bg-black/20 p-6 rounded-2xl flex gap-4">
            <span class="text-2xl">📈</span>
            <p class="font-medium">
              Faster entry into YouTube’s recommendation systems.
            </p>
          </div>
          <div class="bg-black/20 p-6 rounded-2xl flex gap-4">
            <span class="text-2xl">🛡️</span>
            <p class="font-medium">
              Stronger trust signals already in place upon publishing.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section id="faq" class="py-24 px-6 max-w-3xl mx-auto">
      <h2 class="text-3xl font-bold mb-12 text-center">
        Frequently Asked Questions
      </h2>
      <div class="space-y-4">
        <details class="glass p-6 rounded-2xl cursor-pointer group">
          <summary
            class="font-bold list-none flex justify-between items-center group-open:text-blue-400"
          >
            Who typically uses aged YouTube profiles?
            <span class="text-blue-500 text-2xl group-open:rotate-45 transition"
              >+</span
            >
          </summary>
          <p class="text-gray-400 mt-4 text-sm">
            Professional creators, marketers, and businesses looking to bypass
            the "sandbox" period of new accounts.
          </p>
        </details>
        <details class="glass p-6 rounded-2xl cursor-pointer group">
          <summary
            class="font-bold list-none flex justify-between items-center group-open:text-blue-400"
          >
            Are these monetized channels?
            <span class="text-blue-500 text-2xl group-open:rotate-45 transition"
              >+</span
            >
          </summary>
          <p class="text-gray-400 mt-4 text-sm">
            These are aged profiles. Monetization depends on meeting current
            YouTube requirements, but these accounts provide the best foundation
            to get there faster.
          </p>
        </details>
      </div>
    </section>

    <footer class="border-t border-white/5 py-12 px-6">
      <div
        class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8"
      >
        <div class="text-gray-500 text-sm">© 2026 Powered by Agedprofile</div>
        <div
          class="flex flex-wrap gap-6 text-xs font-bold text-gray-400 uppercase tracking-widest"
        >
          <a href="#" class="hover:text-white">Contact Us</a>
          <a href="#" class="hover:text-white">Privacy Policy</a>
          <a href="#" class="hover:text-white">Reviews</a>
          <a href="#" class="hover:text-white">Terms</a>
        </div>
      </div>
    </footer>
  </body>
</html>
<?php $conn->close(); ?>