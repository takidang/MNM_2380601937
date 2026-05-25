<?php
$pageTitle  = 'Product Management | TECH-SPECTRUM Admin';
$activeMenu = 'inventory';
include 'app/views/layouts/admin_header.php';

$totalProducts   = count($products);
$activeCategories = count($categories ?? []);
?>

<!-- HEADER -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-4xl font-bold mb-2">Product Management</h1>
        <p class="text-on-surface-variant text-sm">Manage your high-performance hardware inventory.</p>
    </div>
    <div class="flex gap-2">
        <button class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
            <span class="material-symbols-outlined text-base">download</span>
            Export CSV
        </button>
        <a href="/Product/add" class="bg-primary-container hover:bg-primary-container/90 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition glow-btn">
            <span class="material-symbols-outlined text-base">add</span>
            Thêm sản phẩm
        </a>
    </div>
</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">TOTAL INVENTORY</div>
        <div class="flex items-end gap-2">
            <div class="text-3xl font-bold"><?= number_format($totalProducts) ?></div>
            <div class="text-secondary-container text-xs mb-1">Sản phẩm</div>
        </div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">LOW STOCK</div>
        <div class="flex items-end gap-2">
            <div class="text-3xl font-bold text-error">0</div>
            <div class="text-error text-xs mb-1">Cần bổ sung</div>
        </div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">ACTIVE CATEGORIES</div>
        <div class="flex items-end gap-2">
            <div class="text-3xl font-bold"><?= $activeCategories ?></div>
            <div class="text-on-surface-variant text-xs mb-1">Danh mục</div>
        </div>
    </div>
    <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
        <div class="text-xs font-semibold tracking-widest text-on-surface-variant mb-3">NEW ARRIVALS</div>
        <div class="flex items-end gap-2">
            <div class="text-3xl font-bold"><?= min($totalProducts, 48) ?></div>
            <div class="text-secondary-container text-xs mb-1">Tháng này</div>
        </div>
    </div>
</div>

<!-- MAIN PANEL -->
<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">

    <!-- Filter row -->
    <div class="p-5 border-b border-outline-variant/20 flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        <div class="flex-1 bg-surface-container-low rounded-lg flex items-center px-4 border border-outline-variant/30 focus-within:border-primary transition">
            <span class="material-symbols-outlined text-on-surface-variant text-base mr-2">search</span>
            <input type="text" placeholder="Search by product name, SKU, or brand..."
                   class="bg-transparent border-0 outline-none text-sm flex-1 py-2.5 text-on-surface placeholder:text-on-surface-variant">
        </div>
        <select class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-sm focus:border-primary focus:outline-none">
            <option>All Categories</option>
        </select>
        <select class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-sm focus:border-primary focus:outline-none">
            <option>Status: All</option>
        </select>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="text-xs tracking-widest font-semibold text-on-surface-variant uppercase border-b border-outline-variant/20">
                <tr>
                    <th class="w-12 p-4"><input type="checkbox" class="accent-primary rounded"></th>
                    <th class="text-left p-4">Product Name</th>
                    <th class="text-left p-4">Category</th>
                    <th class="text-left p-4">Price</th>
                    <th class="text-left p-4">Inventory</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-right p-4 pr-6">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php if (!empty($products)): foreach ($products as $product): ?>
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low transition">
                        <td class="p-4"><input type="checkbox" class="accent-primary rounded"></td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-surface-container-low overflow-hidden flex items-center justify-center shrink-0">
                                    <?php if ($product->getImage()): ?>
                                        <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>" class="w-full h-full object-cover" alt="">
                                    <?php else: ?>
                                        <span class="material-symbols-outlined text-on-surface-variant text-base">image</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-semibold line-clamp-1 max-w-xs"><?= htmlspecialchars($product->getName()) ?></div>
                                    <div class="text-on-surface-variant text-xs font-mono">SKU: SP-<?= str_pad($product->getID(), 5, '0', STR_PAD_LEFT) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="bg-primary-container/30 text-primary px-2 py-1 rounded text-xs font-semibold tracking-widest uppercase">
                                <?= htmlspecialchars($product->getCategoryName() ?: 'OTHER') ?>
                            </span>
                        </td>
                        <td class="p-4 font-medium"><?= number_format($product->getPrice(), 0, ',', '.') ?>đ</td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="w-20 h-1.5 bg-surface-container-highest rounded-full overflow-hidden">
                                    <div class="h-full bg-primary" style="width: <?= rand(40, 90) ?>%"></div>
                                </div>
                                <span class="text-on-surface-variant text-xs"><?= rand(10, 200) ?></span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2 text-secondary-container text-xs font-medium">
                                <div class="w-2 h-2 rounded-full bg-secondary-container"></div>
                                <span>Active</span>
                            </div>
                        </td>
                        <td class="p-4 pr-6">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="/Shop/detail/<?= $product->getID() ?>" target="_blank"
                                   class="w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary"
                                   title="Xem">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>
                                <a href="/Product/edit/<?= $product->getID() ?>"
                                   class="w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary"
                                   title="Sửa">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </a>
                                <a href="/Product/delete/<?= $product->getID() ?>"
                                   onclick="return confirm('Xóa sản phẩm này?')"
                                   class="w-8 h-8 rounded-lg hover:bg-error-container/30 flex items-center justify-center transition text-on-surface-variant hover:text-error"
                                   title="Xóa">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-16 text-on-surface-variant">
                            <span class="material-symbols-outlined" style="font-size: 60px">inventory_2</span>
                            <p class="mt-3">Chưa có sản phẩm nào. <a href="/Product/add" class="text-primary hover:underline">Thêm sản phẩm đầu tiên</a></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer pagination -->
    <div class="p-4 border-t border-outline-variant/20 flex items-center justify-between text-sm">
        <span class="text-on-surface-variant">Showing 1-<?= count($products) ?> of <?= count($products) ?> products</span>
        <div class="flex items-center gap-1">
            <button class="w-9 h-9 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition">
                <span class="material-symbols-outlined text-base">chevron_left</span>
            </button>
            <button class="w-9 h-9 rounded-lg bg-primary-container text-white flex items-center justify-center text-xs font-semibold">1</button>
            <button class="w-9 h-9 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition">
                <span class="material-symbols-outlined text-base">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/admin_footer.php'; ?>
