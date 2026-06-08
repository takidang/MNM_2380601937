<?php
$pageTitle = 'Đặt lại mật khẩu | TECH-SPECTRUM';
$authSubtitle = 'ĐẶT LẠI MẬT KHẨU';
include 'app/views/layouts/auth_top.php';
?>

<?php if (!empty($invalid)): ?>
    <div class="text-center">
        <div class="w-14 h-14 mx-auto rounded-full bg-error-container/40 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-error" style="font-size:32px">link_off</span>
        </div>
        <h1 class="text-lg font-bold mb-2">Liên kết không hợp lệ hoặc đã hết hạn</h1>
        <p class="text-on-surface-variant text-sm mb-6">Vui lòng yêu cầu liên kết đặt lại mật khẩu mới.</p>
        <a href="/Auth/forgotPassword" class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary-container/90 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition glow-btn">
            <span class="material-symbols-outlined text-base">refresh</span> Yêu cầu lại
        </a>
    </div>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm space-y-1">
            <?php foreach ($errors as $e): ?>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-base">error</span><span><?= htmlspecialchars($e) ?></span></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p class="text-on-surface-variant text-sm mb-5">Nhập mật khẩu mới cho tài khoản <b class="text-on-surface"><?= htmlspecialchars($user['username'] ?? '') ?></b>.</p>

    <form method="POST" action="/Auth/resetPassword/<?= htmlspecialchars($token) ?>" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Mật khẩu mới</label>
            <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
                <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock_open</span>
                <input type="password" name="new_password" required class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="Tối thiểu 6 ký tự">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Nhập lại mật khẩu mới</label>
            <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
                <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock_reset</span>
                <input type="password" name="confirm_password" required class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="••••••••">
            </div>
        </div>
        <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg w-full py-3 flex items-center justify-center gap-2 font-medium text-sm transition glow-btn">
            <span class="material-symbols-outlined text-base">save</span>
            Đặt lại mật khẩu
        </button>
    </form>
<?php endif; ?>

<?php include 'app/views/layouts/auth_bottom.php'; ?>
