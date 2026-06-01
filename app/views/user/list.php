<?php
$pageTitle  = 'Quản lý người dùng | TECH-SPECTRUM Admin';
$activeMenu = 'users';
include 'app/views/layouts/admin_header.php';

$flashSuccess = SessionHelper::getFlash('success');
$flashError   = SessionHelper::getFlash('error');
$meId = SessionHelper::id();
?>

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-4xl font-bold mb-2">Quản lý người dùng</h1>
        <p class="text-on-surface-variant text-sm">Phân quyền, khóa/mở khóa và quản lý tài khoản.</p>
    </div>
    <a href="/User/add" class="bg-primary-container hover:bg-primary-container/90 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition glow-btn self-start">
        <span class="material-symbols-outlined text-base">person_add</span> Thêm người dùng
    </a>
</div>

<?php if ($flashSuccess): ?>
    <div class="bg-primary-container/20 border border-primary/40 text-primary rounded-lg p-3 mb-5 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-base">check_circle</span><span><?= htmlspecialchars($flashSuccess) ?></span>
    </div>
<?php endif; ?>
<?php if ($flashError): ?>
    <div class="bg-error-container/40 border border-error/40 text-error rounded-lg p-3 mb-5 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-base">block</span><span><?= htmlspecialchars($flashError) ?></span>
    </div>
<?php endif; ?>

<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs tracking-widest font-semibold text-on-surface-variant uppercase border-b border-outline-variant/20">
                <tr>
                    <th class="text-left p-4">Người dùng</th>
                    <th class="text-left p-4">Email</th>
                    <th class="text-left p-4">Vai trò</th>
                    <th class="text-left p-4">Trạng thái</th>
                    <th class="text-left p-4">Xác thực</th>
                    <th class="text-right p-4 pr-6">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-sm">
            <?php foreach ($users as $u): $isMe = ($u['id'] == $meId); ?>
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low transition">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-primary-container flex items-center justify-center text-white font-bold shrink-0">
                                <?php if (!empty($u['avatar'])): ?>
                                    <img src="/public/images/avatars/<?= htmlspecialchars($u['avatar']) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <?= htmlspecialchars(strtoupper(mb_substr($u['fullname'] ?: $u['username'], 0, 1))) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-semibold"><?= htmlspecialchars($u['fullname'] ?: $u['username']) ?><?php if ($isMe): ?> <span class="text-xs text-on-surface-variant">(bạn)</span><?php endif; ?></div>
                                <div class="text-on-surface-variant text-xs font-mono">@<?= htmlspecialchars($u['username']) ?> · #<?= (int)$u['id'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-on-surface-variant"><?= htmlspecialchars($u['email'] ?: '—') ?></td>
                    <td class="p-4">
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="bg-tertiary-container/40 text-primary px-2 py-1 rounded text-xs font-semibold uppercase tracking-widest">Admin</span>
                        <?php else: ?>
                            <span class="bg-primary-container/30 text-primary px-2 py-1 rounded text-xs font-semibold uppercase tracking-widest">User</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <?php if ($u['status'] === 'locked'): ?>
                            <span class="flex items-center gap-1.5 text-error text-xs font-medium"><span class="w-2 h-2 rounded-full bg-error"></span>Đã khóa</span>
                        <?php else: ?>
                            <span class="flex items-center gap-1.5 text-secondary-container text-xs font-medium"><span class="w-2 h-2 rounded-full bg-secondary-container"></span>Hoạt động</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <?php if ((int)$u['is_verified'] === 1): ?>
                            <span class="material-symbols-outlined text-secondary-container" style="font-size:18px" title="Đã xác thực">verified</span>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-on-surface-variant" style="font-size:18px" title="Chưa xác thực">pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 pr-6">
                        <?php if ($isMe): ?>
                            <span class="text-xs text-on-surface-variant italic">— tài khoản của bạn —</span>
                        <?php else: ?>
                        <div class="flex items-center gap-1 justify-end flex-wrap">
                            <!-- Đổi vai trò -->
                            <form method="POST" action="/User/role/<?= (int)$u['id'] ?>" class="inline">
                                <input type="hidden" name="role" value="<?= $u['role'] === 'admin' ? 'user' : 'admin' ?>">
                                <button type="submit" onclick="return confirm('Đổi vai trò tài khoản này?')"
                                        class="px-2.5 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-primary transition text-xs flex items-center gap-1" title="Đổi vai trò">
                                    <span class="material-symbols-outlined text-base">swap_horiz</span>
                                    <?= $u['role'] === 'admin' ? 'Hạ User' : 'Lên Admin' ?>
                                </button>
                            </form>
                            <!-- Khóa / Mở khóa -->
                            <?php if ($u['status'] === 'locked'): ?>
                                <a href="/User/unlock/<?= (int)$u['id'] ?>" class="px-2.5 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-secondary-container transition text-xs flex items-center gap-1" title="Mở khóa">
                                    <span class="material-symbols-outlined text-base">lock_open</span>Mở
                                </a>
                            <?php else: ?>
                                <a href="/User/lock/<?= (int)$u['id'] ?>" onclick="return confirm('Khóa tài khoản này?')" class="px-2.5 py-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-error transition text-xs flex items-center gap-1" title="Khóa">
                                    <span class="material-symbols-outlined text-base">lock</span>Khóa
                                </a>
                            <?php endif; ?>
                            <!-- Xóa -->
                            <a href="/User/delete/<?= (int)$u['id'] ?>" onclick="return confirm('Xóa vĩnh viễn tài khoản này?')" class="px-2.5 py-1.5 rounded-lg hover:bg-error-container/30 text-on-surface-variant hover:text-error transition text-xs flex items-center gap-1" title="Xóa">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </a>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-outline-variant/20 text-sm text-on-surface-variant">
        Tổng cộng <?= count($users) ?> tài khoản
    </div>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
