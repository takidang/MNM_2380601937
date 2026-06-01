<?php
$pageTitle = 'Đổi mật khẩu | TECH-SPECTRUM';
$authSubtitle = 'ĐỔI MẬT KHẨU';
include 'app/views/layouts/auth_top.php';
?>

<?php if (!empty($success)): ?>
    <div class="bg-primary-container/20 border border-primary/40 text-primary rounded-lg p-3 mb-5 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-base">check_circle</span><span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm space-y-1">
        <?php foreach ($errors as $e): ?>
            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-base">error</span><span><?= htmlspecialchars($e) ?></span></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/Auth/changePassword" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Mật khẩu hiện tại</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock</span>
            <input type="password" name="current_password" required class="inp text-sm w-full py-3 text-on-surface" placeholder="••••••••">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Mật khẩu mới</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock_open</span>
            <input type="password" name="new_password" required class="inp text-sm w-full py-3 text-on-surface" placeholder="Tối thiểu 6 ký tự">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Nhập lại mật khẩu mới</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock_reset</span>
            <input type="password" name="confirm_password" required class="inp text-sm w-full py-3 text-on-surface" placeholder="••••••••">
        </div>
    </div>

    <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg w-full py-3 flex items-center justify-center gap-2 font-medium text-sm transition glow-btn">
        <span class="material-symbols-outlined text-base">key</span>
        Cập nhật mật khẩu
    </button>
</form>

<p class="text-center text-sm text-on-surface-variant mt-5">
    <a href="/Profile" class="text-primary hover:underline">← Về hồ sơ cá nhân</a>
</p>

<?php include 'app/views/layouts/auth_bottom.php'; ?>
