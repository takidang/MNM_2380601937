<?php
$pageTitle = 'Quên mật khẩu | TECH-SPECTRUM';
$authSubtitle = 'QUÊN MẬT KHẨU';
include 'app/views/layouts/auth_top.php';
?>

<?php if (!empty($done)): ?>
    <div class="text-center">
        <div class="w-14 h-14 mx-auto rounded-full bg-primary-container/30 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-primary" style="font-size:32px">forward_to_inbox</span>
        </div>
        <p class="text-on-surface-variant text-sm mb-5">
            Nếu email tồn tại trong hệ thống, chúng tôi đã gửi liên kết đặt lại mật khẩu. Vui lòng kiểm tra hộp thư.
        </p>
        <?php if (!empty($devLink)): ?>
            <div class="bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 mb-5 text-left">
                <p class="text-xs text-on-surface-variant mb-1 tracking-widest">CHẾ ĐỘ THỬ NGHIỆM — LIÊN KẾT ĐẶT LẠI:</p>
                <a href="<?= htmlspecialchars($devLink) ?>" class="text-primary text-xs break-all hover:underline"><?= htmlspecialchars($devLink) ?></a>
            </div>
        <?php endif; ?>
        <a href="/Auth/login" class="text-primary hover:underline text-sm">← Về trang đăng nhập</a>
    </div>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm space-y-1">
            <?php foreach ($errors as $e): ?>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-base">error</span><span><?= htmlspecialchars($e) ?></span></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p class="text-on-surface-variant text-sm mb-5">Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>

    <form method="POST" action="/Auth/forgotPassword" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Email</label>
            <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
                <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">mail</span>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       class="inp text-sm w-full py-3 text-on-surface" placeholder="email@example.com">
            </div>
        </div>
        <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg w-full py-3 flex items-center justify-center gap-2 font-medium text-sm transition glow-btn">
            <span class="material-symbols-outlined text-base">send</span>
            Gửi liên kết đặt lại
        </button>
    </form>

    <p class="text-center text-sm text-on-surface-variant mt-5">
        <a href="/Auth/login" class="text-primary hover:underline">← Về trang đăng nhập</a>
    </p>
<?php endif; ?>

<?php include 'app/views/layouts/auth_bottom.php'; ?>
