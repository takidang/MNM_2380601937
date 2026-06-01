<?php
// Layout đầu trang cho các màn hình xác thực (login/register/forgot/reset/notice...)
// Biến tùy chọn: $pageTitle, $authSubtitle
$pageTitle    = $pageTitle    ?? 'TECH-SPECTRUM';
$authSubtitle = $authSubtitle ?? '';
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
tailwind.config = { darkMode: "class", theme: { extend: { colors: {
    "background": "#0b1326", "surface-container": "#171f33",
    "surface-container-low": "#131b2e", "surface-container-high": "#222a3d",
    "on-surface": "#dae2fd", "on-surface-variant": "#c2c6d8",
    "outline-variant": "#424656", "primary": "#b3c5ff",
    "primary-container": "#0066ff", "on-primary": "#002b75",
    "error": "#ffb4ab", "error-container": "#93000a", "secondary-container": "#00eefc",
} } } };
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400; }
    .glow-btn { box-shadow: 0 0 20px rgba(0, 102, 255, 0.3); }
    .glow-btn:hover { box-shadow: 0 0 30px rgba(0, 240, 255, 0.5); }
    .inp { background: transparent; border: 0; outline: none; }
</style>
</head>
<body class="bg-background text-on-surface min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="/" class="text-primary font-bold text-2xl tracking-tight">TECH-SPECTRUM</a>
        <?php if ($authSubtitle !== ''): ?>
            <p class="text-on-surface-variant text-sm mt-2 tracking-widest"><?= htmlspecialchars($authSubtitle) ?></p>
        <?php endif; ?>
    </div>

    <div class="bg-surface-container rounded-2xl border border-outline-variant/20 p-8">
