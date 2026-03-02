<?php
// 1. Logic & DB
require_once 'db.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; $email = $_POST['email']; 
    $pass_raw = $_POST['password'];
    $hashed = password_hash($pass_raw, PASSWORD_BCRYPT);

    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already exists.";
    } else {
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed')");
        $new_id = $conn->insert_id;
        $conn->query("INSERT INTO user_balances (user_id, balance) VALUES ($new_id, 0.00)");
        header("Location: login.php?msg=registered");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | AgedProfile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05070a; color: #e2e8f0; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(16px); }
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; color: white; width: 100%; outline: none; transition: 0.3s; }
        .input-field:focus { border-color: #3b82f6; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="max-w-md mx-auto px-6 py-10">
        <h1 class="text-4xl font-black text-white text-center uppercase tracking-tighter mb-8">Join <span class="text-blue-500">Aged</span></h1>
        
        <?php if($error): ?> <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6 text-xs font-bold uppercase tracking-widest text-center"><?php echo $error; ?></div> <?php endif; ?>

        <form method="POST" class="glass-panel p-8 rounded-[32px] space-y-5">
            <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Full Name</label><input type="text" name="name" required class="input-field" placeholder="John Doe"></div>
            <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Email</label><input type="email" name="email" required class="input-field" placeholder="john@example.com"></div>
            <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">Password</label><input type="password" name="password" required class="input-field" placeholder="••••••••"></div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-4 rounded-xl font-black text-white uppercase tracking-widest transition-all">Sign Up</button>
        </form>
    </main>
</body>
</html>