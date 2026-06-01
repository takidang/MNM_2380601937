<?php
$pageTitle  = 'Hồ sơ cá nhân | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';

$avatarUrl = !empty($user['avatar']) ? '/public/images/avatars/' . $user['avatar'] : null;
$initial   = strtoupper(mb_substr($user['fullname'] ?: $user['username'], 0, 1));
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-1">Hồ sơ cá nhân</h1>
    <p class="text-on-surface-variant text-sm mb-8">Xem và cập nhật thông tin tài khoản của bạn.</p>

    <?php if (!empty($success)): ?>
        <div class="bg-primary-container/20 border border-primary/40 text-primary rounded-lg p-3 mb-6 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-base">check_circle</span><span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-6 text-sm space-y-1">
            <?php foreach ($errors as $e): ?>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-base">error</span><span><?= htmlspecialchars($e) ?></span></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- THẺ THÔNG TIN HIỂN THỊ SAU KHI ĐĂNG NHẬP -->
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 mb-8 flex items-center gap-5">
        <div class="w-20 h-20 rounded-full overflow-hidden bg-primary-container flex items-center justify-center text-white text-2xl font-bold shrink-0">
            <?php if ($avatarUrl): ?>
                <img src="<?= htmlspecialchars($avatarUrl) ?>" class="w-full h-full object-cover" alt="avatar">
            <?php else: ?>
                <?= htmlspecialchars($initial) ?>
            <?php endif; ?>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xl font-bold"><?= htmlspecialchars($user['fullname'] ?: $user['username']) ?></span>
                <?php if ($user['role'] === 'admin'): ?>
                    <span class="bg-tertiary-container/40 text-primary text-xs font-semibold px-2 py-0.5 rounded uppercase tracking-widest">Admin</span>
                <?php else: ?>
                    <span class="bg-primary-container/30 text-primary text-xs font-semibold px-2 py-0.5 rounded uppercase tracking-widest">User</span>
                <?php endif; ?>
                <?php if ((int)$user['is_verified'] === 1): ?>
                    <span class="text-secondary-container text-xs flex items-center gap-1"><span class="material-symbols-outlined" style="font-size:14px">verified</span>Đã xác thực</span>
                <?php else: ?>
                    <span class="text-error text-xs flex items-center gap-1"><span class="material-symbols-outlined" style="font-size:14px">error</span>Chưa xác thực email</span>
                <?php endif; ?>
            </div>
            <p class="text-on-surface-variant text-sm mt-1">@<?= htmlspecialchars($user['username']) ?> · <?= htmlspecialchars($user['email'] ?: 'chưa có email') ?></p>
            <p class="text-on-surface-variant text-xs mt-1">Tham gia: <?= htmlspecialchars($user['created_at'] ?? '') ?></p>
        </div>
    </div>

    <?php if ((int)$user['is_verified'] !== 1): ?>
        <div class="bg-error-container/30 border border-error/30 rounded-lg p-4 mb-8 flex items-center justify-between gap-4 flex-wrap">
            <span class="text-sm text-error">Tài khoản chưa xác thực email.</span>
            <a href="/Auth/resendVerification" class="text-sm bg-error-container/60 hover:bg-error-container text-on-surface px-3 py-2 rounded-lg transition">Gửi lại liên kết xác thực</a>
        </div>
    <?php endif; ?>

    <!-- FORM CẬP NHẬT HỒ SƠ + AVATAR -->
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 mb-6">
        <h2 class="font-semibold mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-base">edit</span>Cập nhật thông tin</h2>
        <form method="POST" action="/Profile/update" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-2">Họ và tên</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                       class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-2">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                       class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none">
                <p class="text-xs text-on-surface-variant mt-1">Đổi email sẽ cần xác thực lại.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-2">Ảnh đại diện</label>
                <input type="file" name="avatar" accept="image/*"
                       class="block w-full text-sm text-on-surface-variant file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-container file:text-white file:text-sm file:cursor-pointer hover:file:bg-primary-container/90">
                <p class="text-xs text-on-surface-variant mt-1">JPG, PNG, GIF, WEBP — tối đa 3MB.</p>
            </div>
            <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg px-5 py-2.5 text-sm font-medium flex items-center gap-2 transition glow-btn">
                <span class="material-symbols-outlined text-base">save</span> Lưu thay đổi
            </button>
        </form>
    </div>

    <div class="flex gap-3 flex-wrap">
        <a href="/Auth/changePassword" class="bg-surface-container hover:bg-surface-container-high border border-outline-variant/30 rounded-lg px-4 py-2.5 text-sm flex items-center gap-2 transition">
            <span class="material-symbols-outlined text-base">key</span> Đổi mật khẩu
        </a>
        <?php if ($user['role'] === 'admin'): ?>
            <a href="/User/list" class="bg-surface-container hover:bg-surface-container-high border border-outline-variant/30 rounded-lg px-4 py-2.5 text-sm flex items-center gap-2 transition">
                <span class="material-symbols-outlined text-base">manage_accounts</span> Quản lý người dùng
            </a>
        <?php endif; ?>
        <a href="/Auth/logout" class="bg-surface-container hover:bg-surface-container-high border border-outline-variant/30 rounded-lg px-4 py-2.5 text-sm flex items-center gap-2 transition hover:text-error">
            <span class="material-symbols-outlined text-base">logout</span> Đăng xuất
        </a>
    </div>
</div>

<?php include 'app/views/layouts/customer_footer.php'; ?>
