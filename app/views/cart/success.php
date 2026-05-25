<?php
$pageTitle  = 'Đặt hàng thành công | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';
?>

<div class="max-w-2xl mx-auto py-12 text-center">
    <div class="w-20 h-20 rounded-full bg-secondary-container/20 border-2 border-secondary-container flex items-center justify-center mx-auto mb-6">
        <span class="material-symbols-outlined text-secondary-container text-4xl">check_circle</span>
    </div>

    <h1 class="text-3xl font-bold mb-3">Đặt hàng thành công!</h1>
    <p class="text-on-surface-variant mb-2">Cảm ơn bạn <strong class="text-on-surface"><?= htmlspecialchars($order->getCustomerName()) ?></strong> đã tin dùng TECH-SPECTRUM.</p>
    <p class="text-on-surface-variant mb-8">Mã đơn hàng: <span class="text-primary font-mono font-bold">#<?= $order->getID() ?></span></p>

    <div class="bg-surface-container border border-outline-variant/20 rounded-xl p-6 text-left mb-6">
        <h2 class="text-lg font-semibold mb-4 pb-4 border-b border-outline-variant/20">Chi tiết đơn hàng</h2>

        <div class="space-y-3 mb-4">
            <?php foreach ($details as $d): ?>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-surface-container-low overflow-hidden flex items-center justify-center shrink-0">
                        <?php if ($d->getProductImage()): ?>
                            <img src="/public/images/products/<?= htmlspecialchars($d->getProductImage()) ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-on-surface-variant text-base">image</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm"><?= htmlspecialchars($d->getProductName()) ?></div>
                        <div class="text-on-surface-variant text-xs">SL: <?= $d->getQuantity() ?> × <?= number_format($d->getPrice(), 0, ',', '.') ?>đ</div>
                    </div>
                    <div class="text-secondary-container font-semibold text-sm">
                        <?= number_format($d->getSubtotal(), 0, ',', '.') ?>đ
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-between items-baseline pt-4 border-t border-outline-variant/20">
            <span class="font-semibold">Tổng cộng</span>
            <span class="text-secondary-container text-2xl font-bold"><?= number_format($order->getTotalAmount(), 0, ',', '.') ?>đ</span>
        </div>

        <div class="mt-4 pt-4 border-t border-outline-variant/20 text-sm space-y-2">
            <div class="flex justify-between text-on-surface-variant">
                <span>Trạng thái:</span>
                <span class="px-2 py-0.5 bg-surface-container-high text-on-surface rounded text-xs"><?= $order->getStatusLabel() ?></span>
            </div>
            <div class="flex justify-between text-on-surface-variant">
                <span>Địa chỉ giao:</span>
                <span class="text-on-surface text-right max-w-xs"><?= htmlspecialchars($order->getCustomerAddress()) ?></span>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-3">
        <a href="/" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-6 py-3 rounded-lg font-medium transition border border-outline-variant/30">
            Tiếp tục mua sắm
        </a>
        <a href="/Order/detail/<?= $order->getID() ?>" class="bg-primary-container hover:bg-primary-container/90 text-white px-6 py-3 rounded-lg font-medium transition glow-btn">
            Xem chi tiết đơn
        </a>
    </div>
</div>

<?php include 'app/views/layouts/customer_footer.php'; ?>
