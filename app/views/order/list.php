<?php
$pageTitle  = 'Order Management | TECH-SPECTRUM Admin';
$activeMenu = 'orders';
include 'app/views/layouts/admin_header.php';

$statusFilters = [
    ''          => ['All',         'bg-surface-container-high', 'text-on-surface'],
    'pending'   => ['Pending',     'bg-surface-container-high', 'text-on-surface'],
    'confirmed' => ['Confirmed',   'bg-tertiary-container/30',  'text-tertiary'],
    'shipping'  => ['Shipping',    'bg-primary-container/30',   'text-primary'],
    'completed' => ['Completed',   'bg-secondary-container/30', 'text-secondary-container'],
    'cancelled' => ['Cancelled',   'bg-error-container/30',     'text-error'],
];
$currentStatus = $_GET['status'] ?? '';
?>

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-4xl font-bold mb-2">Order Management</h1>
        <p class="text-on-surface-variant text-sm">Theo dõi và quản lý tất cả đơn hàng từ khách hàng.</p>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">TỔNG ĐƠN HÀNG</div>
        <div class="text-3xl font-bold"><?= count($orders) ?></div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">CHỜ XỬ LÝ</div>
        <div class="text-3xl font-bold"><?= $stats['pending'] ?? 0 ?></div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">ĐANG GIAO</div>
        <div class="text-3xl font-bold text-primary"><?= $stats['shipping'] ?? 0 ?></div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">DOANH THU</div>
        <div class="text-2xl font-bold text-secondary-container"><?= number_format($revenue, 0, ',', '.') ?>đ</div>
    </div>
</div>

<div class="flex gap-2 mb-4 overflow-x-auto pb-1">
    <?php foreach ($statusFilters as $k => $info): ?>
        <a href="/Order/list<?= $k ? '?status='.$k : '' ?>"
           class="px-4 py-2 rounded-lg text-sm font-medium border transition whitespace-nowrap
                  <?= $currentStatus === $k
                        ? 'bg-primary-container text-white border-primary-container'
                        : 'bg-surface-container hover:bg-surface-container-high text-on-surface border-outline-variant/30' ?>">
            <?= $info[0] ?>
            <?php if ($k && isset($stats[$k])): ?>
                <span class="ml-1 text-xs opacity-70">(<?= $stats[$k] ?>)</span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs tracking-widest font-semibold text-on-surface-variant uppercase border-b border-outline-variant/20">
                <tr>
                    <th class="text-left p-4">Mã đơn</th>
                    <th class="text-left p-4">Khách hàng</th>
                    <th class="text-left p-4">SĐT</th>
                    <th class="text-left p-4">Tổng tiền</th>
                    <th class="text-left p-4">Trạng thái</th>
                    <th class="text-left p-4">Ngày đặt</th>
                    <th class="text-right p-4 pr-6">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php if (!empty($orders)): foreach ($orders as $order): ?>
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low transition">
                        <td class="p-4">
                            <span class="font-mono font-bold text-primary">#<?= str_pad($order->getID(), 5, '0', STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="p-4">
                            <div class="font-semibold"><?= htmlspecialchars($order->getCustomerName()) ?></div>
                            <?php if ($order->getCustomerEmail()): ?>
                                <div class="text-on-surface-variant text-xs"><?= htmlspecialchars($order->getCustomerEmail()) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 font-mono text-on-surface-variant"><?= htmlspecialchars($order->getCustomerPhone()) ?></td>
                        <td class="p-4 font-semibold text-secondary-container"><?= number_format($order->getTotalAmount(), 0, ',', '.') ?>đ</td>
                        <td class="p-4">
                            <?php
                            $badge = $statusFilters[$order->getStatus()] ?? ['?', 'bg-surface-container-high', 'text-on-surface'];
                            ?>
                            <span class="<?= $badge[1] ?> <?= $badge[2] ?> px-2 py-1 rounded text-xs font-semibold tracking-widest uppercase">
                                <?= $order->getStatusLabel() ?>
                            </span>
                        </td>
                        <td class="p-4 text-on-surface-variant text-xs"><?= date('d/m/Y H:i', strtotime($order->getCreatedAt())) ?></td>
                        <td class="p-4 pr-6">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="/Order/detail/<?= $order->getID() ?>"
                                   class="w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary"
                                   title="Xem">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>
                                <a href="/Order/edit/<?= $order->getID() ?>"
                                   class="w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary"
                                   title="Sửa">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </a>
                                <a href="/Order/delete/<?= $order->getID() ?>"
                                   onclick="return confirm('Xóa đơn hàng này?')"
                                   class="w-8 h-8 rounded-lg hover:bg-error-container/30 flex items-center justify-center transition text-on-surface-variant hover:text-error"
                                   title="Xóa">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-16 text-on-surface-variant">
                            <span class="material-symbols-outlined" style="font-size: 60px">shopping_bag</span>
                            <p class="mt-3">Chưa có đơn hàng nào.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
