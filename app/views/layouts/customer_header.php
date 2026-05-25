<?php
// Layout chung cho khách hàng (customer-facing pages)
// Biến cần truyền vào: $pageTitle, $activeMenu (optional: 'home' | 'laptops' | 'phones' | 'accessories')
$activeMenu = $activeMenu ?? '';
$pageTitle  = $pageTitle  ?? 'TECH-SPECTRUM | High-Performance Hardware';

// Đếm số item trong giỏ hàng (session)
if (session_status() === PHP_SESSION_NONE) session_start();
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title><?= htmlspecialchars($pageTitle) ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;family=JetBrains+Mono:wght@400;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "background": "#0b1326",
                "surface": "#0b1326",
                "surface-dim": "#0b1326",
                "surface-container-lowest": "#060e20",
                "surface-container-low": "#131b2e",
                "surface-container": "#171f33",
                "surface-container-high": "#222a3d",
                "surface-container-highest": "#2d3449",
                "surface-bright": "#31394d",
                "surface-variant": "#2d3449",
                "on-surface": "#dae2fd",
                "on-surface-variant": "#c2c6d8",
                "outline": "#8c90a1",
                "outline-variant": "#424656",
                "primary": "#b3c5ff",
                "on-primary": "#002b75",
                "primary-container": "#0066ff",
                "on-primary-container": "#f8f7ff",
                "secondary": "#d3fbff",
                "secondary-container": "#00eefc",
                "tertiary": "#d1bcff",
                "tertiary-container": "#8447ff",
                "error": "#ffb4ab",
                "error-container": "#93000a",
            },
            fontFamily: {
                "display": ["Inter", "sans-serif"],
                "mono": ["JetBrains Mono", "monospace"]
            }
        }
    }
};
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .nav-link-active { color: #b3c5ff; border-bottom: 2px solid #b3c5ff; padding-bottom: 4px; }
    .glow-btn { box-shadow: 0 0 20px rgba(0, 102, 255, 0.3); }
    .glow-btn:hover { box-shadow: 0 0 30px rgba(0, 240, 255, 0.5); }
</style>
</head>
<body class="bg-background text-on-surface min-h-screen">

<!-- HEADER -->
<header class="border-b border-outline-variant/30 sticky top-0 z-50 bg-background/80 backdrop-blur-lg">
    <div class="max-w-[1280px] mx-auto px-4 md:px-12 py-4 flex items-center justify-between gap-6">
        <a href="/" class="text-primary font-bold text-xl tracking-tight whitespace-nowrap">TECH-SPECTRUM</a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="/Shop/category/2" class="hover:text-primary transition <?= $activeMenu==='laptops' ? 'nav-link-active' : 'text-on-surface' ?>">Laptops</a>
            <a href="/Shop/category/1" class="hover:text-primary transition <?= $activeMenu==='phones' ? 'nav-link-active' : 'text-on-surface' ?>">Phones</a>
            <a href="/Shop/category/4" class="hover:text-primary transition <?= $activeMenu==='accessories' ? 'nav-link-active' : 'text-on-surface' ?>">Accessories</a>
            <a href="/Shop/category/5" class="hover:text-primary transition <?= $activeMenu==='deals' ? 'nav-link-active' : 'text-on-surface' ?>">Deals</a>
        </nav>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center bg-surface-container rounded-lg px-3 py-2 w-64">
                <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">search</span>
                <input type="text" placeholder="Search hardware..."
                       class="bg-transparent border-0 outline-none text-sm w-full text-on-surface placeholder:text-on-surface-variant">
            </div>
            <a href="/Admin/dashboard" class="flex items-center gap-1.5 text-xs bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-primary px-3 py-2 rounded-lg border border-outline-variant/30 transition font-medium">
                <span class="material-symbols-outlined" style="font-size:15px">admin_panel_settings</span>
                <span>Quản trị</span>
            </a>
            <a href="/Cart/list" class="relative bg-primary-container hover:bg-primary-container/90 text-white rounded-lg px-4 py-2 flex items-center gap-2 transition glow-btn font-medium text-sm">
                <span class="material-symbols-outlined text-base">shopping_cart</span>
                <span>Cart</span>
                <span class="cart-badge absolute -top-1 -right-1 bg-secondary-container text-on-primary text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center <?= $cartCount > 0 ? '' : 'hidden' ?>">
                    <?= $cartCount ?>
                </span>
            </a>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<main class="max-w-[1280px] mx-auto px-4 md:px-12 py-8">
