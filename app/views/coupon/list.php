<?php include 'app/views/layouts/admin_header.php'; ?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold">Mã giảm giá</h1>
        <p class="text-on-surface-variant text-sm mt-1"><?= count($coupons) ?> mã đang có</p>
    </div>
    <a href="/Coupon/add" class="bg-primary-container hover:bg-primary-container/90 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition glow-btn">
        <span class="material-symbols-outlined text-base">add</span>
        Thêm mã mới
    </a>
</div>

<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
    <?php if (empty($coupons)): ?>
        <div class="p-16 text-center text-on-surface-variant">
            <span class="material-symbols-outlined text-6xl mb-3 block">local_offer</span>
            <p class="font-medium">Chưa có mã giảm giá nào</p>
        </div>
    <?php else: ?>
    <table class="w-full text-sm">
        <thead class="border-b border-outline-variant/20 text-on-surface-variant text-xs tracking-widest uppercase">
            <tr>
                <th class="text-left px-6 py-4">Mã</th>
                <th class="text-center px-4 py-4">Giảm</th>
                <th class="text-center px-4 py-4">Lượt dùng</th>
                <th class="text-center px-4 py-4">Hết hạn</th>
                <th class="text-center px-4 py-4">Trạng thái</th>
                <th class="text-right px-6 py-4">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/10">
            <?php foreach ($coupons as $c): ?>
            <tr class="hover:bg-surface-container-high transition">
                <td class="px-6 py-4 font-mono font-bold text-primary tracking-widest"><?= htmlspecialchars($c->code) ?></td>
                <td class="px-4 py-4 text-center">
                    <span class="bg-secondary-container/20 text-secondary-container font-bold px-2 py-0.5 rounded text-sm">-<?= $c->discount_value ?>%</span>
                </td>
                <td class="px-4 py-4 text-center text-on-surface-variant">
                    <?= $c->used_count ?><?= $c->usage_limit ? ' / ' . $c->usage_limit : ' / ∞' ?>
                </td>
                <td class="px-4 py-4 text-center text-on-surface-variant text-xs">
                    <?= $c->valid_until ? date('d/m/Y', strtotime($c->valid_until)) : '—' ?>
                </td>
                <td class="px-4 py-4 text-center">
                    <?php
                    $now = date('Y-m-d H:i:s');
                    $expired = $c->valid_until && $now > $c->valid_until;
                    $maxed   = $c->usage_limit && $c->used_count >= $c->usage_limit;
                    if (!$c->is_active): ?>
                        <span class="bg-error-container/30 text-error text-xs px-2 py-0.5 rounded">Tắt</span>
                    <?php elseif ($expired || $maxed): ?>
                        <span class="bg-surface-container-high text-on-surface-variant text-xs px-2 py-0.5 rounded">Hết</span>
                    <?php else: ?>
                        <span class="bg-secondary-container/20 text-secondary-container text-xs px-2 py-0.5 rounded">Hoạt động</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                    <a href="/Coupon/edit/<?= $c->id ?>" class="text-primary hover:underline text-xs">Sửa</a>
                    <a href="/Coupon/delete/<?= $c->id ?>"
                       onclick="return confirm('Xóa mã <?= htmlspecialchars($c->code) ?>?')"
                       class="text-error hover:underline text-xs">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
