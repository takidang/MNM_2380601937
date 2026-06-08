<?php
$pageTitle = 'Đăng ký | TECH-SPECTRUM';
$authSubtitle = 'TẠO TÀI KHOẢN MỚI';
include 'app/views/layouts/auth_top.php';
?>

<?php if (!empty($errors)): ?>
    <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm space-y-1">
        <?php foreach ($errors as $e): ?>
            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-base">error</span><span><?= htmlspecialchars($e) ?></span></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/Auth/register" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Tên đăng nhập</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">person</span>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="username">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Họ và tên</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">badge</span>
            <input type="text" name="fullname" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                   class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="Nguyễn Văn A">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Email</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">mail</span>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="email@example.com">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Mật khẩu</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock</span>
            <input type="password" name="password" required class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="Tối thiểu 6 ký tự">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-on-surface-variant mb-2">Nhập lại mật khẩu</label>
        <div class="flex items-center bg-surface-container-low rounded-lg px-3 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">lock_reset</span>
            <input type="password" name="confirm" required class="bg-transparent border-0 outline-none text-sm w-full py-3 text-on-surface placeholder:text-on-surface-variant" placeholder="••••••••">
        </div>
    </div>

    <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg w-full py-3 flex items-center justify-center gap-2 font-medium text-sm transition glow-btn">
        <span class="material-symbols-outlined text-base">person_add</span>
        Đăng ký
    </button>
</form>

<p class="text-center text-sm text-on-surface-variant mt-5">
    Đã có tài khoản?
    <a href="/Auth/login" class="text-primary hover:underline font-medium">Đăng nhập</a>
</p>

<?php include 'app/views/layouts/auth_bottom.php'; ?>
