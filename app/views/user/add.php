<?php
$pageTitle  = 'Thêm người dùng | TECH-SPECTRUM Admin';
$activeMenu = 'users';
include 'app/views/layouts/admin_header.php';
?>

<div class="max-w-xl">
    <div class="flex items-center gap-2 mb-6">
        <a href="/User/list" class="text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-3xl font-bold">Thêm người dùng</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm space-y-1">
            <?php foreach ($errors as $e): ?>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-base">error</span><span><?= htmlspecialchars($e) ?></span></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/User/add" class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Tên đăng nhập</label>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Họ và tên</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                   class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Mật khẩu</label>
            <input type="password" name="password" required
                   class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none" placeholder="Tối thiểu 6 ký tự">
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-2">Vai trò</label>
            <select name="role" class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm w-full text-on-surface focus:border-primary focus:outline-none">
                <option value="user"  <?= (($_POST['role'] ?? '') === 'user')  ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg px-5 py-2.5 text-sm font-medium flex items-center gap-2 transition glow-btn">
                <span class="material-symbols-outlined text-base">save</span> Tạo tài khoản
            </button>
            <a href="/User/list" class="bg-surface-container-low hover:bg-surface-container-high border border-outline-variant/30 rounded-lg px-5 py-2.5 text-sm transition">Hủy</a>
        </div>
    </form>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
