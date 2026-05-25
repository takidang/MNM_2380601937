<?php
$pageTitle  = 'Sửa đơn hàng #' . $order->getID() . ' | TECH-SPECTRUM Admin';
$activeMenu = 'orders';
include 'app/views/layouts/admin_header.php';
?>

<!-- HEADER -->
<div class="flex items-center justify-between mb-8">
    <div>
        <nav class="text-sm text-on-surface-variant mb-2 flex items-center gap-2">
            <a href="/Order/list" class="hover:text-primary transition">Orders</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <a href="/Order/detail/<?= $order->getID() ?>" class="hover:text-primary transition">#<?= str_pad($order->getID(), 5, '0', STR_PAD_LEFT) ?></a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface">Chỉnh sửa</span>
        </nav>
        <h1 class="text-3xl font-bold">Sửa đơn hàng #<?= str_pad($order->getID(), 5, '0', STR_PAD_LEFT) ?></h1>
    </div>
    <a href="/Order/detail/<?= $order->getID() ?>" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-error-container/20 border border-error/40 text-error rounded-lg p-4 mb-6 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="bg-primary-container/10 border border-primary-container/30 rounded-xl p-4 mb-6 flex items-start gap-3">
    <span class="material-symbols-outlined text-primary">info</span>
    <div class="text-on-surface text-sm">
        Bạn chỉ có thể sửa <strong>thông tin khách hàng</strong> và <strong>ghi chú</strong>. Để đổi sản phẩm trong đơn hoặc trạng thái, vui lòng dùng các form tương ứng ở trang chi tiết.
    </div>
</div>

<form action="/Order/edit/<?= $order->getID() ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">

    <!-- LEFT: FORM -->
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
        <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">person</span>
            Thông tin khách hàng
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Họ và tên <span class="text-error">*</span></label>
                <input type="text" name="customer_name" required
                       value="<?= htmlspecialchars($order->getCustomerName()) ?>"
                       class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Số điện thoại <span class="text-error">*</span></label>
                <input type="tel" name="customer_phone" required
                       value="<?= htmlspecialchars($order->getCustomerPhone()) ?>"
                       class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm font-mono">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="customer_email"
                       value="<?= htmlspecialchars($order->getCustomerEmail() ?? '') ?>"
                       class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Địa chỉ giao hàng <span class="text-error">*</span></label>
                <textarea name="customer_address" required rows="3"
                          class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm resize-none"><?= htmlspecialchars($order->getCustomerAddress()) ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Ghi chú</label>
                <textarea name="note" rows="2"
                          class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm resize-none"><?= htmlspecialchars($order->getNote() ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- RIGHT: ORDER SUMMARY (readonly) -->
    <div class="space-y-4">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">receipt_long</span>
                Tổng quan đơn
            </h3>
            <div class="space-y-3 text-sm mb-5">
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Trạng thái:</span>
                    <span class="font-semibold text-primary"><?= $order->getStatusLabel() ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Ngày tạo:</span>
                    <span><?= date('d/m/Y H:i', strtotime($order->getCreatedAt())) ?></span>
                </div>
                <div class="flex justify-between items-baseline border-t border-outline-variant/20 pt-3">
                    <span class="font-semibold">Tổng tiền:</span>
                    <span class="text-secondary-container text-xl font-bold"><?= number_format($order->getTotalAmount(), 0, ',', '.') ?>đ</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3 font-semibold flex items-center justify-center gap-2 transition glow-btn mb-3">
                <span class="material-symbols-outlined text-base">save</span>
                Lưu thay đổi
            </button>
            <a href="/Order/detail/<?= $order->getID() ?>" class="block w-full text-center bg-surface-container-high hover:bg-surface-bright text-on-surface rounded-lg py-3 font-medium transition border border-outline-variant/30">
                Hủy bỏ
            </a>
        </div>
    </div>
</form>

<?php include 'app/views/layouts/admin_footer.php'; ?>
