<?php
// 1. Database Connection
require_once 'db.php';

// 2. Logic: Handle Login Request
if (session_status() === PHP_SESSION_NONE) session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Join with user_balances so we get the balance immediately
    $sql = "SELECT u.*, IFNULL(b.balance, 0.00) as balance 
            FROM users u 
            LEFT JOIN user_balances b ON u.id = b.user_id 
            WHERE u.email = '$email'";
            
    $result = $conn->query($sql);
    $user_data = $result->fetch_assoc();

    if ($user_data && password_verify($password, $user_data['password'])) {
        // Create Session
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['name'] = $user_data['name'];
        $_SESSION['balance'] = (float)$user_data['balance'];

        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; color: white; width: 100%; outline: none; transition: 0.3s; }
        .input-field:focus { border-color: #3b82f6; background: rgba(255,255,255,0.08); }
        .accent-text { color: #3b82f6; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="max-w-md mx-auto px-6 py-16">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-white uppercase tracking-tighter italic">Welcome <span class="accent-text">Back</span></h1>
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-2">Access your aged profile dashboard</p>
        </div>
        
        <?php if($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-xs font-bold uppercase tracking-widest text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <div class="bg-green-500/10 border border-green-500/50 text-green-500 p-4 rounded-xl mb-6 text-xs font-bold uppercase tracking-widest text-center">
                Registration Successful! Please login.
            </div>
        <?php endif; ?>

        <form method="POST" class="glass-panel p-8 rounded-[32px] space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1 tracking-widest">Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required class="input-field">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1 tracking-widest">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="input-field">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-4 rounded-xl font-black text-white uppercase tracking-widest shadow-xl shadow-blue-600/20 transition-all active:scale-[0.98]">
                Login Account
            </button>
        </form>

        <p class="text-center mt-8 text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em]">
            Don't have an account? <a href="signup.php" class="text-blue-500 hover:underline">Create one</a>
        </p>
    </main>

    <footer class="py-12 text-center text-[10px] text-gray-600 uppercase tracking-[0.3em]">
        © 2026 Powered by Agedprofile
    </footer>

</body>
</html>