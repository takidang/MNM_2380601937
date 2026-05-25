<?php
$pageTitle  = 'Category Management | TECH-SPECTRUM Admin';
$activeMenu = 'categories';
include 'app/views/layouts/admin_header.php';

// Mapping icon cho từng danh mục
$categoryIcons = [
    'Điện thoại'        => 'smartphone',
    'Laptop'            => 'laptop_mac',
    'Máy tính bảng'     => 'tablet_mac',
    'Phụ kiện'          => 'headphones',
    'Thiết bị âm thanh' => 'speaker'
];
?>

<!-- HEADER -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-4xl font-bold mb-2">Category Management</h1>
        <p class="text-on-surface-variant text-sm max-w-2xl">
            Organize and manage your hardware inventory structure. Categorization helps customers find the right specs faster.
        </p>
    </div>
    <div class="flex gap-2">
        <button class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
            <span class="material-symbols-outlined text-base">download</span>
            Export List
        </button>
        <a href="/Category/add" class="bg-primary-container hover:bg-primary-container/90 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition glow-btn">
            <span class="material-symbols-outlined text-base">create_new_folder</span>
            Danh mục mới
        </a>
    </div>
</div>

<!-- STATS -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">TOTAL CATEGORIES</div>
        <div class="text-3xl font-bold"><?= count($categories) ?></div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">MOST ACTIVE</div>
        <div class="text-2xl font-bold"><?= !empty($categories) ? htmlspecialchars($categories[0]->name) : '—' ?></div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-primary/50">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">STOCK HEALTH</div>
        <div class="text-3xl font-bold text-primary">98%</div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">HIDDEN TAGS</div>
        <div class="text-3xl font-bold">0</div>
    </div>
</div>

<!-- TABLE -->
<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="p-5 border-b border-outline-variant/20 flex items-center gap-3">
        <div class="flex-1 bg-surface-container-low rounded-lg flex items-center px-4 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-base mr-2">search</span>
            <input type="text" placeholder="Lọc danh mục theo tên hoặc ID..."
                   class="bg-transparent border-0 outline-none text-sm flex-1 py-2.5">
        </div>
        <span class="text-on-surface-variant text-sm">Showing <?= count($categories) ?> of <?= count($categories) ?></span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs tracking-widest font-semibold text-on-surface-variant uppercase border-b border-outline-variant/20">
                <tr>
                    <th class="text-left p-4 w-16">Icon</th>
                    <th class="text-left p-4">Tên danh mục</th>
                    <th class="text-left p-4">Mô tả</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-right p-4 pr-6">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php if (!empty($categories)): foreach ($categories as $cat):
                    $icon = $categoryIcons[$cat->name] ?? 'category'; ?>
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low transition">
                        <td class="p-4">
                            <span class="material-symbols-outlined text-primary text-2xl"><?= $icon ?></span>
                        </td>
                        <td class="p-4">
                            <div class="font-semibold"><?= htmlspecialchars($cat->name) ?></div>
                            <div class="text-on-surface-variant text-xs font-mono">CAT-<?= str_pad($cat->id, 3, '0', STR_PAD_LEFT) ?></div>
                        </td>
                        <td class="p-4 text-on-surface-variant max-w-md">
                            <?= htmlspecialchars($cat->description ?? '—') ?>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2 text-secondary-container text-xs font-medium">
                                <div class="w-2 h-2 rounded-full bg-secondary-container"></div>
                                <span>Active</span>
                            </div>
                        </td>
                        <td class="p-4 pr-6">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="/Shop/category/<?= $cat->id ?>" target="_blank"
                                   class="w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary"
                                   title="Xem">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>
                                <a href="/Category/edit/<?= $cat->id ?>"
                                   class="w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary"
                                   title="Sửa">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </a>
                                <a href="/Category/delete/<?= $cat->id ?>"
                                   onclick="return confirm('Xóa danh mục này sẽ xóa luôn các sản phẩm thuộc nó. Bạn chắc chắn?')"
                                   class="w-8 h-8 rounded-lg hover:bg-error-container/30 flex items-center justify-center transition text-on-surface-variant hover:text-error"
                                   title="Xóa">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-16 text-on-surface-variant">
                            <span class="material-symbols-outlined" style="font-size: 60px">folder_open</span>
                            <p class="mt-3">Chưa có danh mục nào. <a href="/Category/add" class="text-primary hover:underline">Tạo danh mục đầu tiên</a></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
