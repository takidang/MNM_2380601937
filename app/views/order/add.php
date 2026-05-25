<?php
$pageTitle  = 'Tạo đơn hàng | TECH-SPECTRUM Admin';
$activeMenu = 'orders';
include 'app/views/layouts/admin_header.php';
?>

<!-- HEADER -->
<div class="flex items-center justify-between mb-8">
    <div>
        <nav class="text-sm text-on-surface-variant mb-2 flex items-center gap-2">
            <a href="/Order/list" class="hover:text-primary transition">Orders</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface">Tạo đơn hàng</span>
        </nav>
        <h1 class="text-3xl font-bold">Tạo đơn hàng mới</h1>
        <p class="text-on-surface-variant text-sm mt-1">Tạo đơn hàng thủ công (cho khách hàng đặt qua điện thoại, tại quầy...)</p>
    </div>
    <a href="/Order/list" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
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

<form action="/Order/add" method="POST" class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6">

    <!-- LEFT: ORDER ITEMS + CUSTOMER -->
    <div class="space-y-6">

        <!-- ITEMS PICKER -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Sản phẩm trong đơn
                </h2>
                <button type="button" onclick="addItemRow()" class="bg-primary-container hover:bg-primary-container/90 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1 transition">
                    <span class="material-symbols-outlined text-base">add</span>
                    Thêm dòng
                </button>
            </div>

            <div id="itemsContainer" class="space-y-3">
                <!-- Dynamic rows here -->
            </div>

            <div class="mt-4 pt-4 border-t border-outline-variant/20 flex justify-between items-baseline">
                <span class="text-on-surface-variant text-sm">Tổng tạm tính:</span>
                <span id="orderTotal" class="text-secondary-container text-2xl font-bold">0đ</span>
            </div>
        </div>

        <!-- CUSTOMER INFO -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Thông tin khách hàng
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Họ và tên <span class="text-error">*</span></label>
                    <input type="text" name="customer_name" required
                           value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>"
                           placeholder="Nguyễn Văn A"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Số điện thoại <span class="text-error">*</span></label>
                    <input type="tel" name="customer_phone" required
                           value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>"
                           placeholder="0901234567"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm font-mono">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" name="customer_email"
                           value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>"
                           placeholder="example@email.com"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Địa chỉ giao hàng <span class="text-error">*</span></label>
                    <textarea name="customer_address" required rows="2"
                              placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố..."
                              class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm resize-none"><?= htmlspecialchars($_POST['customer_address'] ?? '') ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Ghi chú đơn hàng</label>
                    <input type="text" name="note"
                           value="<?= htmlspecialchars($_POST['note'] ?? '') ?>"
                           placeholder="VD: Giao giờ hành chính, gọi trước 30 phút..."
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: ACTIONS + HINT -->
    <div class="space-y-4">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 sticky top-24">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">receipt_long</span>
                Xác nhận
            </h3>
            <div class="space-y-3 text-sm mb-5">
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Số sản phẩm:</span>
                    <span id="itemCount" class="font-semibold">0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Tổng số lượng:</span>
                    <span id="totalQty" class="font-semibold">0</span>
                </div>
                <div class="flex justify-between items-baseline border-t border-outline-variant/20 pt-3">
                    <span class="font-semibold">Tổng tiền:</span>
                    <span id="orderTotalSummary" class="text-secondary-container text-xl font-bold">0đ</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3 font-semibold flex items-center justify-center gap-2 transition glow-btn mb-3">
                <span class="material-symbols-outlined text-base">save</span>
                Tạo đơn hàng
            </button>
            <a href="/Order/list" class="block w-full text-center bg-surface-container-high hover:bg-surface-bright text-on-surface rounded-lg py-3 font-medium transition border border-outline-variant/30">
                Hủy bỏ
            </a>
        </div>

        <div class="bg-primary-container/10 border border-primary-container/30 rounded-xl p-5 flex items-start gap-3">
            <span class="material-symbols-outlined text-primary">info</span>
            <div class="text-on-surface text-xs leading-relaxed">
                Đơn hàng được tạo sẽ ở trạng thái <strong class="text-primary">Chờ xử lý</strong>. Sau khi xác nhận với khách, bạn có thể đổi trạng thái trong trang chi tiết.
            </div>
        </div>
    </div>
</form>

<!-- Template hidden cho row mới -->
<template id="itemRowTemplate">
    <div class="item-row bg-surface-container-low rounded-lg p-4 grid grid-cols-[1fr_120px_140px_40px] gap-3 items-center border border-outline-variant/20">
        <select name="product_id[]" class="product-select bg-surface-container border border-outline-variant/30 rounded-lg px-3 py-2 text-sm focus:border-primary focus:outline-none" required>
            <option value="">-- Chọn sản phẩm --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p->getID() ?>" data-price="<?= $p->getPrice() ?>" data-name="<?= htmlspecialchars($p->getName()) ?>">
                    <?= htmlspecialchars($p->getName()) ?> — <?= number_format($p->getPrice(), 0, ',', '.') ?>đ
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantity[]" min="1" value="1" required
               class="qty-input bg-surface-container border border-outline-variant/30 rounded-lg px-3 py-2 text-sm text-center focus:border-primary focus:outline-none">
        <div class="row-subtotal text-right font-bold text-secondary-container text-sm">0đ</div>
        <button type="button" onclick="removeItemRow(this)" class="w-9 h-9 rounded-lg hover:bg-error-container/30 flex items-center justify-center transition text-on-surface-variant hover:text-error">
            <span class="material-symbols-outlined text-base">delete</span>
        </button>
    </div>
</template>

<script>
const container = document.getElementById('itemsContainer');
const template  = document.getElementById('itemRowTemplate');

function addItemRow() {
    const clone = template.content.cloneNode(true);
    container.appendChild(clone);
    bindRow(container.lastElementChild);
    recalcTotal();
}

function removeItemRow(btn) {
    btn.closest('.item-row').remove();
    recalcTotal();
}

function bindRow(row) {
    const select = row.querySelector('.product-select');
    const qty    = row.querySelector('.qty-input');
    const subEl  = row.querySelector('.row-subtotal');

    const update = () => {
        const opt   = select.selectedOptions[0];
        const price = opt ? parseFloat(opt.dataset.price || 0) : 0;
        const q     = parseInt(qty.value) || 0;
        const sub   = price * q;
        subEl.textContent = formatVND(sub);
        recalcTotal();
    };

    select.addEventListener('change', update);
    qty.addEventListener('input', update);
}

function recalcTotal() {
    let total = 0, itemCount = 0, totalQty = 0;
    container.querySelectorAll('.item-row').forEach(row => {
        const opt   = row.querySelector('.product-select').selectedOptions[0];
        const price = opt && opt.value ? parseFloat(opt.dataset.price || 0) : 0;
        const q     = parseInt(row.querySelector('.qty-input').value) || 0;
        if (opt && opt.value) {
            total += price * q;
            itemCount++;
            totalQty += q;
        }
    });
    document.getElementById('orderTotal').textContent          = formatVND(total);
    document.getElementById('orderTotalSummary').textContent   = formatVND(total);
    document.getElementById('itemCount').textContent           = itemCount;
    document.getElementById('totalQty').textContent            = totalQty;
}

function formatVND(n) {
    return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
}

// Thêm 1 row khởi tạo sẵn
addItemRow();
</script>

<?php include 'app/views/layouts/admin_footer.php'; ?>
