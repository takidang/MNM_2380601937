<?php
$pageTitle = 'Đăng nhập | TECH-SPECTRUM';
?>
<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title><?= htmlspecialchars($pageTitle) ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script>
tailwind.config = {
    darkMode: "class",
    theme: { extend: { colors: {
        "background": "#0b1326", "surface-container": "#171f33",
        "surface-container-low": "#131b2e", "surface-container-high": "#222a3d",
        "on-surface": "#dae2fd", "on-surface-variant": "#c2c6d8",
        "outline-variant": "#424656", "primary": "#b3c5ff",
        "primary-container": "#0066ff", "on-primary": "#002b75",
        "error": "#ffb4ab", "error-container": "#93000a",
        "secondary-container": "#00eefc",
    } } }
};
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400; }
    .glow-btn { box-shadow: 0 0 20px rgba(0, 102, 255, 0.3); }
    .glow-btn:hover { box-shadow: 0 0 30px rgba(0, 240, 255, 0.5); }
</style>
</head>
<body class="bg-background text-on-surface min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="/" class="text-primary font-bold text-2xl tracking-tight">TECH-SPECTRUM</a>
        <p class="text-on-surface-variant text-sm mt-2 tracking-widest">ĐĂNG NHẬP HỆ THỐNG</p>
    </div>

    <div class="bg-surface-container rounded-2xl border border-outline-variant/20 p-8">

        <?php if (!empty($errors)): ?>
            <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm">
                <?php foreach ($errors as $e): ?>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">error</span>
                        <span><?= htmlspecialchars($e) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($flashError)): ?>
            <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-base">lock</span>
                <span><?= htmlspecialchars($flashError) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/Auth/login" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-2">Tên đăng nhập</label>
                <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
                    <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">person</span>
                    <input type="text" name="username" autofocus required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant"
                           placeholder="admin hoặc user">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-2">Mật khẩu</label>
                <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
                    <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock</span>
                    <input type="password" name="password" required
                           class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-on-surface-variant cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1" class="accent-primary rounded">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="/Auth/forgotPassword" class="text-primary hover:underline">Quên mật khẩu?</a>
            </div>

            <button type="submit"
                    class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg w-full py-3 flex items-center justify-center gap-2 font-medium text-sm transition glow-btn">
                <span class="material-symbols-outlined text-base">login</span>
                Đăng nhập
            </button>
        </form>

        <p class="text-center text-sm text-on-surface-variant mt-5">
            Chưa có tài khoản?
            <a href="/Auth/register" class="text-primary hover:underline font-medium">Đăng ký ngay</a>
        </p>

        <div class="mt-6 pt-5 border-t border-outline-variant/20 text-xs text-on-surface-variant space-y-1">
            <p class="font-semibold tracking-widest mb-1">TÀI KHOẢN DÙNG THỬ</p>
            <p>Admin (toàn quyền): <span class="font-mono text-primary">admin / admin123</span></p>
            <p>User (mua hàng): <span class="font-mono text-secondary-container">user / user123</span></p>
        </div>
    </div>

    <p class="text-center text-on-surface-variant text-sm mt-6">
        <a href="/" class="hover:text-primary transition">← Về trang chủ cửa hàng</a>
    </p>
</div>

</body>
</html>
