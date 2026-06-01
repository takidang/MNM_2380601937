<?php
$pageTitle = ($title ?? 'Thông báo') . ' | TECH-SPECTRUM';
$authSubtitle = '';
include 'app/views/layouts/auth_top.php';
?>

<div class="text-center">
    <div class="w-14 h-14 mx-auto rounded-full bg-primary-container/30 flex items-center justify-center mb-4">
        <span class="material-symbols-outlined text-primary" style="font-size:32px">mark_email_read</span>
    </div>
    <h1 class="text-xl font-bold mb-2"><?= htmlspecialchars($title ?? 'Thông báo') ?></h1>
    <p class="text-on-surface-variant text-sm mb-6"><?= htmlspecialchars($message ?? '') ?></p>

    <?php if (!empty($devLink)): ?>
        <div class="bg-surface-container-low border border-outline-variant/30 rounded-lg p-3 mb-6 text-left">
            <p class="text-xs text-on-surface-variant mb-1 tracking-widest">CHẾ ĐỘ THỬ NGHIỆM — LIÊN KẾT (thay cho email thật):</p>
            <a href="<?= htmlspecialchars($devLink) ?>" class="text-primary text-xs break-all hover:underline"><?= htmlspecialchars($devLink) ?></a>
        </div>
    <?php endif; ?>

    <a href="<?= htmlspecialchars($backUrl ?? '/') ?>"
       class="inline-flex items-center justify-center gap-2 bg-primary-container hover:bg-primary-container/90 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition glow-btn">
        <span class="material-symbols-outlined text-base">arrow_forward</span>
        Tiếp tục
    </a>
</div>

<?php include 'app/views/layouts/auth_bottom.php'; ?>
