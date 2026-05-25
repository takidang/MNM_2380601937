<?php
$pageTitle  = 'Chi tiết đơn hàng #' . $order->getID() . ' | TECH-SPECTRUM Admin';
$activeMenu = 'orders';
include 'app/views/layouts/admin_header.php';

$statusOptions = [
    'pending'   => 'Chờ xử lý',
    'confirmed' => 'Đã xác nhận',
    'shipping'  => 'Đang giao',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
];
?>

<!-- HEADER -->
<div class="flex items-center justify-between mb-8">
    <div>
        <nav class="text-sm text-on-surface-variant mb-2 flex items-center gap-2">
            <a href="/Order/list" class="hover:text-primary transition">Orders</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface">#<?= str_pad($order->getID(), 5, '0', STR_PAD_LEFT) ?></span>
        </nav>
        <div class="flex items-center gap-4">
            <h1 class="text-3xl font-bold">Đơn hàng #<?= str_pad($order->getID(), 5, '0', STR_PAD_LEFT) ?></h1>
            <span class="<?= $order->getStatusBadgeClass() ?> bg-opacity-30 px-3 py-1 rounded text-xs font-semibold tracking-widest uppercase text-white"
                  style="background-color: rgba(0, 102, 255, 0.3); color: #b3c5ff;">
                <?= $order->getStatusLabel() ?>
            </span>
        </div>
        <p class="text-on-surface-variant text-sm mt-1">Đặt ngày <?= date('d/m/Y H:i', strtotime($order->getCreatedAt())) ?></p>
    </div>
    <div class="flex gap-2">
        <a href="/Order/list" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Quay lại
        </a>
        <a href="/Order/edit/<?= $order->getID() ?>" class="bg-primary-container hover:bg-primary-container/90 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition glow-btn">
            <span class="material-symbols-outlined text-base">edit</span>
            Sửa thông tin
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">

    <!-- LEFT: ITEMS -->
    <div>
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden mb-6">
            <div class="p-5 border-b border-outline-variant/20">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Sản phẩm trong đơn (<?= count($details) ?>)
                </h2>
            </div>
            <table class="w-full">
                <thead class="text-xs tracking-widest font-semibold text-on-surface-variant uppercase border-b border-outline-variant/20">
                    <tr>
                        <th class="text-left p-4">Sản phẩm</th>
                        <th class="text-right p-4">Đơn giá</th>
                        <th class="text-center p-4">SL</th>
                        <th class="text-right p-4 pr-6">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($details as $d): ?>
                        <tr class="border-b border-outline-variant/10">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-lg bg-surface-container-low overflow-hidden flex items-center justify-center shrink-0">
                                        <?php if ($d->getProductImage()): ?>
                                            <img src="/public/images/products/<?= htmlspecialchars($d->getProductImage()) ?>" class="w-full h-full object-cover" alt="">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-on-surface-variant text-base">image</span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-semibold"><?= htmlspecialchars($d->getProductName() ?? '(Đã xóa)') ?></div>
                                        <div class="text-on-surface-variant text-xs font-mono">SP-<?= str_pad($d->getProductID(), 5, '0', STR_PAD_LEFT) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right p-4 font-mono"><?= number_format($d->getPrice(), 0, ',', '.') ?>đ</td>
                            <td class="text-center p-4 font-semibold"><?= $d->getQuantity() ?></td>
                            <td class="text-right p-4 pr-6 font-bold text-secondary-container"><?= number_format($d->getSubtotal(), 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="text-sm bg-surface-container-low">
                    <tr>
                        <td colspan="3" class="p-4 text-right font-semibold">TỔNG CỘNG:</td>
                        <td class="p-4 pr-6 text-right font-bold text-secondary-container text-xl"><?= number_format($order->getTotalAmount(), 0, ',', '.') ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Note -->
        <?php if ($order->getNote()): ?>
            <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
                <h3 class="font-semibold mb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">sticky_note_2</span>
                    Ghi chú đơn hàng
                </h3>
                <p class="text-on-surface-variant text-sm"><?= nl2br(htmlspecialchars($order->getNote())) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: CUSTOMER + STATUS -->
    <div class="space-y-4">
        <!-- Update status -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">pending_actions</span>
                Cập nhật trạng thái
            </h3>
            <form action="/Order/updateStatus/<?= $order->getID() ?>" method="POST" class="space-y-3">
                <select name="status" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-sm focus:border-primary focus:outline-none">
                    <?php foreach ($statusOptions as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $order->getStatus() === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="w-full bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-2.5 text-sm font-semibold transition">
                    Cập nhật
                </button>
            </form>
        </div>

        <!-- Customer info -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">person</span>
                Thông tin khách hàng
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-on-surface-variant text-xs mb-1">Họ tên</div>
                    <div class="font-medium"><?= htmlspecialchars($order->getCustomerName()) ?></div>
                </div>
                <div>
                    <div class="text-on-surface-variant text-xs mb-1">Số điện thoại</div>
                    <div class="font-mono"><?= htmlspecialchars($order->getCustomerPhone()) ?></div>
                </div>
                <?php if ($order->getCustomerEmail()): ?>
                    <div>
                        <div class="text-on-surface-variant text-xs mb-1">Email</div>
                        <div class="font-mono text-xs break-all"><?= htmlspecialchars($order->getCustomerEmail()) ?></div>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="text-on-surface-variant text-xs mb-1">Địa chỉ giao hàng</div>
                    <div class="text-on-surface"><?= htmlspecialchars($order->getCustomerAddress()) ?></div>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">schedule</span>
                Thông tin thời gian
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-on-surface-variant text-xs mb-1">Ngày đặt</div>
                    <div><?= date('d/m/Y H:i:s', strtotime($order->getCreatedAt())) ?></div>
                </div>
                <div>
                    <div class="text-on-surface-variant text-xs mb-1">Cập nhật lần cuối</div>
                    <div><?= date('d/m/Y H:i:s', strtotime($order->getUpdatedAt())) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
