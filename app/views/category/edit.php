<?php
$pageTitle  = 'Chỉnh sửa danh mục | TECH-SPECTRUM Admin';
$activeMenu = 'categories';
include 'app/views/layouts/admin_header.php';
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <nav class="text-sm text-on-surface-variant mb-2 flex items-center gap-2">
            <a href="/Category/list" class="hover:text-primary transition">Categories</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface">Chỉnh sửa #<?= $category->id ?></span>
        </nav>
        <h1 class="text-3xl font-bold">Chỉnh sửa danh mục</h1>
    </div>
    <a href="/Category/list" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-error-container/20 border border-error/40 text-error rounded-lg p-4 mb-6 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/Category/edit/<?= htmlspecialchars($category->id) ?>" method="POST" class="max-w-2xl">
    <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 mb-6">
        <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit_note</span>
            Thông tin danh mục
        </h2>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Tên danh mục <span class="text-error">*</span></label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($category->name) ?>"
                       class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Mô tả</label>
                <textarea name="description" rows="4"
                          class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm resize-none"><?= htmlspecialchars($category->description ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="bg-primary-container hover:bg-primary-container/90 text-white rounded-lg px-6 py-3 font-semibold flex items-center gap-2 transition glow-btn">
            <span class="material-symbols-outlined text-base">save</span>
            Cập nhật
        </button>
        <a href="/Category/list" class="bg-surface-container-high hover:bg-surface-bright text-on-surface rounded-lg px-6 py-3 font-medium transition border border-outline-variant/30">
            Hủy bỏ
        </a>
    </div>
</form>

<?php include 'app/views/layouts/admin_footer.php'; ?>
