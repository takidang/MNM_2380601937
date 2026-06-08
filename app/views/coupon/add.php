<?php include 'app/views/layouts/admin_header.php'; ?>

<div class="flex items-center justify-between mb-8">
    <div>
        <nav class="text-sm text-on-surface-variant mb-2 flex items-center gap-2">
            <a href="/Coupon/list" class="hover:text-primary transition">Mã giảm giá</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface">Thêm mới</span>
        </nav>
        <h1 class="text-3xl font-bold">Thêm mã giảm giá</h1>
    </div>
    <a href="/Coupon/list" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-error-container/20 border border-error/40 text-error rounded-lg p-4 mb-6 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/Coupon/add" method="POST" class="max-w-xl">
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 space-y-5">

        <div>
            <label class="block text-sm font-medium mb-2">Mã giảm giá <span class="text-error">*</span></label>
            <input type="text" name="code" required value="<?= htmlspecialchars($_POST['code'] ?? '') ?>"
                   placeholder="VD: SALE10" style="text-transform:uppercase"
                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 font-mono font-bold tracking-widest focus:border-primary focus:outline-none transition text-sm">
            <p class="text-on-surface-variant text-xs mt-1">Chữ hoa, không dấu, không khoảng trắng</p>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Phần trăm giảm (%) <span class="text-error">*</span></label>
            <input type="number" name="discount_value" required min="1" max="100" step="0.01"
                   value="<?= htmlspecialchars($_POST['discount_value'] ?? '') ?>"
                   placeholder="VD: 10"
                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Giới hạn lượt dùng</label>
            <input type="number" name="usage_limit" min="1" value="<?= htmlspecialchars($_POST['usage_limit'] ?? '') ?>"
                   placeholder="Để trống = không giới hạn"
                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Ngày bắt đầu</label>
                <input type="datetime-local" name="valid_from" value="<?= htmlspecialchars($_POST['valid_from'] ?? '') ?>"
                       class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Ngày hết hạn</label>
                <input type="datetime-local" name="valid_until" value="<?= htmlspecialchars($_POST['valid_until'] ?? '') ?>"
                       class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3 font-semibold flex items-center justify-center gap-2 transition glow-btn">
                <span class="material-symbols-outlined text-base">save</span>
                Lưu
            </button>
            <a href="/Coupon/list" class="flex-1 text-center bg-surface-container-high hover:bg-surface-bright text-on-surface rounded-lg py-3 font-medium transition border border-outline-variant/30">
                Hủy
            </a>
        </div>
    </div>
</form>

<?php include 'app/views/layouts/admin_footer.php'; ?>
