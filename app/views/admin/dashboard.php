<?php
$pageTitle  = 'Admin Dashboard | TECH-SPECTRUM';
$activeMenu = 'dashboard';
include 'app/views/layouts/admin_header.php';

$totalProducts  = count($products ?? []);
$totalCategories = count($categories ?? []);
$totalOrders    = count($orders ?? []);
$pendingOrders  = $orderStats['pending'] ?? 0;

$categoryIcons = [
    'Điện thoại'        => 'smartphone',
    'Laptop'            => 'laptop_mac',
    'Máy tính bảng'     => 'tablet_mac',
    'Phụ kiện'          => 'headphones',
    'Thiết bị âm thanh' => 'speaker',
    'Máy ảnh'           => 'photo_camera',
];
?>

<!-- PAGE HEADER -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-on-surface">Dashboard</h1>
        <p class="text-on-surface-variant text-sm mt-1">Tổng quan hệ thống TECH-SPECTRUM</p>
    </div>
    <a href="/" class="flex items-center gap-2 text-sm bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg border border-outline-variant/30 transition font-medium">
        <span class="material-symbols-outlined text-base">storefront</span>
        Về trang chủ
    </a>
</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-on-surface-variant tracking-widest uppercase">Sản phẩm</span>
            <div class="w-9 h-9 rounded-lg bg-primary-container/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-base">inventory_2</span>
            </div>
        </div>
        <div class="text-3xl font-bold text-on-surface"><?= $totalProducts ?></div>
        <a href="/Product/list" class="text-xs text-primary hover:underline mt-1 inline-block">Xem tất cả →</a>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-on-surface-variant tracking-widest uppercase">Danh mục</span>
            <div class="w-9 h-9 rounded-lg bg-secondary-container/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary-container text-base">category</span>
            </div>
        </div>
        <div class="text-3xl font-bold text-on-surface"><?= $totalCategories ?></div>
        <a href="/Category/list" class="text-xs text-primary hover:underline mt-1 inline-block">Xem tất cả →</a>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-on-surface-variant tracking-widest uppercase">Đơn hàng</span>
            <div class="w-9 h-9 rounded-lg bg-tertiary-container/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-tertiary-container text-base">shopping_cart</span>
            </div>
        </div>
        <div class="text-3xl font-bold text-on-surface"><?= $totalOrders ?></div>
        <a href="/Order/list" class="text-xs text-primary hover:underline mt-1 inline-block">Xem tất cả →</a>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-on-surface-variant tracking-widest uppercase">Chờ xử lý</span>
            <div class="w-9 h-9 rounded-lg bg-error-container/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-error text-base">pending_actions</span>
            </div>
        </div>
        <div class="text-3xl font-bold text-on-surface"><?= $pendingOrders ?></div>
        <a href="/Order/list" class="text-xs text-primary hover:underline mt-1 inline-block">Xử lý ngay →</a>
    </div>
</div>

<!-- MAIN GRID: Products + Categories -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">

    <!-- PRODUCTS TABLE -->
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/20">
            <h2 class="font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">inventory_2</span>
                Sản phẩm gần đây
            </h2>
            <a href="/Product/list" class="text-xs text-primary hover:underline underline-offset-2 flex items-center gap-1">
                Xem tất cả
                <span class="material-symbols-outlined" style="font-size:13px">arrow_forward</span>
            </a>
        </div>

        <div class="divide-y divide-outline-variant/20">
            <?php $recentProducts = array_slice($products ?? [], 0, 5);
            if (!empty($recentProducts)):
                foreach ($recentProducts as $p): ?>
                    <div class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container-high/50 transition group">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-low overflow-hidden border border-outline-variant/20 flex items-center justify-center flex-shrink-0">
                            <?php if ($p->getImage()): ?>
                                <img src="/public/images/products/<?= htmlspecialchars($p->getImage()) ?>" class="w-full h-full object-cover" alt="">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size:18px">image</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-on-surface truncate"><?= htmlspecialchars($p->getName()) ?></div>
                            <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($p->getCategoryName() ?? 'Chưa phân loại') ?></div>
                        </div>
                        <div class="text-sm font-semibold text-secondary-container flex-shrink-0"><?= number_format($p->getPrice(), 0, ',', '.') ?>đ</div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                            <a href="/Product/edit/<?= $p->getID() ?>" class="w-7 h-7 rounded-md bg-surface-container-high hover:bg-primary-container/30 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-primary" style="font-size:14px">edit</span>
                            </a>
                            <a href="/Product/delete/<?= $p->getID() ?>" onclick="return confirm('Xóa sản phẩm này?')" class="w-7 h-7 rounded-md bg-surface-container-high hover:bg-error-container/30 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-error" style="font-size:14px">delete</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach;
            else: ?>
                <div class="px-6 py-10 text-center text-on-surface-variant text-sm">Chưa có sản phẩm nào.</div>
            <?php endif; ?>
        </div>

        <div class="px-6 py-3 border-t border-outline-variant/20">
            <a href="/Product/add" class="flex items-center gap-1.5 text-xs text-primary hover:underline font-medium">
                <span class="material-symbols-outlined" style="font-size:14px">add</span>
                Thêm sản phẩm mới
            </a>
        </div>
    </div>

    <!-- CATEGORIES LIST -->
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/20">
            <h2 class="font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary-container text-base">category</span>
                Danh mục
            </h2>
            <a href="/Category/list" class="text-xs text-primary hover:underline underline-offset-2 flex items-center gap-1">
                Xem tất cả
                <span class="material-symbols-outlined" style="font-size:13px">arrow_forward</span>
            </a>
        </div>

        <div class="divide-y divide-outline-variant/20">
            <?php if (!empty($categories)):
                foreach ($categories as $cat):
                    $icon = $categoryIcons[$cat->name] ?? 'category'; ?>
                    <div class="flex items-center gap-3 px-6 py-3 hover:bg-surface-container-high/50 transition group">
                        <div class="w-10 h-10 rounded-lg bg-secondary-container/10 border border-secondary-container/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-secondary-container" style="font-size:18px"><?= $icon ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cat->name) ?></div>
                            <div class="text-xs text-on-surface-variant truncate"><?= htmlspecialchars($cat->description ?? 'Không có mô tả') ?></div>
                        </div>
                        <span class="text-xs bg-surface-container-high text-on-surface-variant rounded px-2 py-0.5 font-mono flex-shrink-0">#<?= $cat->id ?></span>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                            <a href="/Category/edit/<?= $cat->id ?>" class="w-7 h-7 rounded-md bg-surface-container-high hover:bg-primary-container/30 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-primary" style="font-size:14px">edit</span>
                            </a>
                            <a href="/Category/delete/<?= $cat->id ?>" onclick="return confirm('Xóa danh mục \'<?= htmlspecialchars(addslashes($cat->name)) ?>\'?')" class="w-7 h-7 rounded-md bg-surface-container-high hover:bg-error-container/30 flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-error" style="font-size:14px">delete</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach;
            else: ?>
                <div class="px-6 py-10 text-center text-on-surface-variant text-sm">Chưa có danh mục nào.</div>
            <?php endif; ?>
        </div>

        <div class="px-6 py-3 border-t border-outline-variant/20">
            <a href="/Category/add" class="flex items-center gap-1.5 text-xs text-secondary-container hover:underline font-medium">
                <span class="material-symbols-outlined" style="font-size:14px">add</span>
                Thêm danh mục mới
            </a>
        </div>
    </div>
</div>

<!-- RECENT ORDERS -->
<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/20">
        <h2 class="font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-tertiary-container text-base">shopping_cart</span>
            Đơn hàng gần đây
        </h2>
        <a href="/Order/list" class="text-xs text-primary hover:underline underline-offset-2 flex items-center gap-1">
            Xem tất cả
            <span class="material-symbols-outlined" style="font-size:13px">arrow_forward</span>
        </a>
    </div>

    <?php $recentOrders = array_slice($orders ?? [], 0, 5);
    if (!empty($recentOrders)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-on-surface-variant uppercase tracking-wider border-b border-outline-variant/20">
                        <th class="px-6 py-3 text-left font-semibold">Mã đơn</th>
                        <th class="px-6 py-3 text-left font-semibold">Khách hàng</th>
                        <th class="px-6 py-3 text-left font-semibold">Số điện thoại</th>
                        <th class="px-6 py-3 text-left font-semibold">Trạng thái</th>
                        <th class="px-6 py-3 text-left font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <?php foreach ($recentOrders as $order):
                        $status = $order->status ?? 'pending';
                        $statusMap = [
                            'pending'   => ['label' => 'Chờ xử lý',  'class' => 'bg-error-container/20 text-error'],
                            'confirmed' => ['label' => 'Đã xác nhận','class' => 'bg-primary-container/20 text-primary'],
                            'shipped'   => ['label' => 'Đang giao',  'class' => 'bg-tertiary-container/20 text-tertiary-container'],
                            'delivered' => ['label' => 'Đã giao',    'class' => 'bg-secondary-container/10 text-secondary-container'],
                            'cancelled' => ['label' => 'Đã hủy',     'class' => 'bg-surface-container-high text-on-surface-variant'],
                        ];
                        $s = $statusMap[$status] ?? $statusMap['pending'];
                    ?>
                        <tr class="hover:bg-surface-container-high/50 transition">
                            <td class="px-6 py-3 font-mono text-xs text-on-surface-variant">#<?= str_pad($order->id, 4, '0', STR_PAD_LEFT) ?></td>
                            <td class="px-6 py-3 font-medium text-on-surface"><?= htmlspecialchars($order->customer_name ?? '') ?></td>
                            <td class="px-6 py-3 text-on-surface-variant"><?= htmlspecialchars($order->customer_phone ?? '') ?></td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $s['class'] ?>">
                                    <?= $s['label'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <a href="/Order/detail/<?= $order->id ?>" class="text-xs text-primary hover:underline">Chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="px-6 py-10 text-center text-on-surface-variant text-sm">Chưa có đơn hàng nào.</div>
    <?php endif; ?>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
