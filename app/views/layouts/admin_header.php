<?php
// Admin layout - Biến cần: $pageTitle, $activeMenu (dashboard|inventory|categories|orders|analytics)
require_once 'app/helpers/SessionHelper.php';
$adminName   = SessionHelper::displayName();
$adminAvatar = SessionHelper::avatarUrl();
$activeMenu = $activeMenu ?? '';
$pageTitle  = $pageTitle  ?? 'Admin Portal | TECH-SPECTRUM';
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
                "primary-container": "#0066ff",
                "secondary": "#d3fbff",
                "secondary-container": "#00eefc",
                "tertiary-container": "#8447ff",
                "error": "#ffb4ab",
                "error-container": "#93000a",
            }
        }
    }
};
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .glow-btn { box-shadow: 0 0 20px rgba(0, 102, 255, 0.3); }
    .glow-btn:hover { box-shadow: 0 0 30px rgba(0, 240, 255, 0.5); }
    .sidebar-link-active {
        background-color: #171f33;
        color: #b3c5ff;
        border-right: 2px solid #b3c5ff;
    }
</style>
</head>
<body class="bg-background text-on-surface min-h-screen flex">

<!-- SIDEBAR -->
<aside class="w-64 bg-background border-r border-outline-variant/30 flex flex-col py-6 min-h-screen sticky top-0">
    <div class="px-6 mb-8">
        <a href="/" class="block">
            <div class="text-primary font-bold text-xl tracking-tight">TECH-SPECTRUM</div>
            <div class="text-on-surface-variant text-xs tracking-widest mt-1">ADMIN PORTAL</div>
        </a>
    </div>

    <nav class="flex-1 flex flex-col gap-1 text-sm font-medium">
        <a href="/Admin/dashboard" class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container transition <?= $activeMenu==='dashboard' ? 'sidebar-link-active' : 'text-on-surface' ?>">
            <span class="material-symbols-outlined text-base">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="/Product/list" class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container transition <?= $activeMenu==='inventory' ? 'sidebar-link-active' : 'text-on-surface' ?>">
            <span class="material-symbols-outlined text-base">inventory_2</span>
            <span>Inventory</span>
        </a>
        <a href="/Category/list" class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container transition <?= $activeMenu==='categories' ? 'sidebar-link-active' : 'text-on-surface' ?>">
            <span class="material-symbols-outlined text-base">category</span>
            <span>Categories</span>
        </a>
        <a href="/Order/list" class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container transition <?= $activeMenu==='orders' ? 'sidebar-link-active' : 'text-on-surface' ?>">
            <span class="material-symbols-outlined text-base">shopping_cart</span>
            <span>Orders</span>
        </a>
        <a href="/User/list" class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container transition <?= $activeMenu==='users' ? 'sidebar-link-active' : 'text-on-surface' ?>">
            <span class="material-symbols-outlined text-base">group</span>
            <span>Users</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container transition <?= $activeMenu==='analytics' ? 'sidebar-link-active' : 'text-on-surface' ?>">
            <span class="material-symbols-outlined text-base">bar_chart</span>
            <span>Analytics</span>
        </a>
    </nav>

    <div class="px-6 mt-6">
        <a href="/Product/add" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg w-full py-3 flex items-center justify-center gap-2 font-medium text-sm transition glow-btn">
            <span class="material-symbols-outlined text-base">add</span>
            <span>Add Product</span>
        </a>
    </div>

    <div class="px-6 pt-6 mt-6 border-t border-outline-variant/30 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full overflow-hidden bg-primary-container flex items-center justify-center text-white font-bold uppercase">
            <?php if ($adminAvatar): ?>
                <img src="<?= htmlspecialchars($adminAvatar) ?>" class="w-full h-full object-cover" alt="">
            <?php else: ?>
                <?= htmlspecialchars(mb_substr($adminName, 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-on-surface truncate"><?= htmlspecialchars($adminName) ?></div>
            <div class="text-xs text-on-surface-variant">System Controller</div>
        </div>
    </div>

    <div class="px-6 mt-3 flex flex-col gap-1">
        <a href="/" class="flex items-center gap-2 text-xs text-on-surface-variant hover:text-primary px-2 py-2 rounded-lg hover:bg-surface-container transition">
            <span class="material-symbols-outlined" style="font-size:16px">storefront</span>
            <span>Về trang cửa hàng</span>
        </a>
        <a href="/Auth/logout" class="flex items-center gap-2 text-xs text-on-surface-variant hover:text-error px-2 py-2 rounded-lg hover:bg-surface-container transition">
            <span class="material-symbols-outlined" style="font-size:16px">logout</span>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 p-8 md:p-12">
