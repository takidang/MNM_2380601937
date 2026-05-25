<?php
$pageTitle  = ($currentCategory ? $currentCategory->name : 'All Products') . ' | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';
?>

<!-- BREADCRUMB -->
<nav class="text-sm text-on-surface-variant mb-4 flex items-center gap-2">
    <a href="/" class="hover:text-primary transition">Trang chủ</a>
    <span>/</span>
    <span class="text-on-surface"><?= $currentCategory ? htmlspecialchars($currentCategory->name) : 'Tất cả sản phẩm' ?></span>
</nav>

<!-- HEADER -->
<div class="flex justify-between items-end mb-8">
    <div>
        <h1 class="text-4xl font-bold mb-2">
            <?= $currentCategory ? htmlspecialchars($currentCategory->name) : 'Tất cả sản phẩm' ?>
        </h1>
        <p class="text-on-surface-variant text-sm max-w-2xl">
            <?= $currentCategory && $currentCategory->description
                ? htmlspecialchars($currentCategory->description)
                : 'Engineered for absolute reliability and technical superiority. From workstation powerhouses to ultra-portable performance.' ?>
        </p>
    </div>
    <div class="hidden md:flex items-center gap-3 text-sm">
        <span class="text-on-surface-variant">Showing <?= count($products) ?> results</span>
        <select class="bg-surface-container border border-outline-variant/30 rounded-lg px-3 py-2 text-on-surface text-sm">
            <option>Sort by: Popularity</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
            <option>Newest</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6">

    <!-- SIDEBAR FILTERS -->
    <aside class="space-y-4">
        <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs tracking-widest font-semibold text-on-surface-variant">FILTERS</h3>
                <button class="text-primary text-xs hover:underline">Clear All</button>
            </div>

            <div class="space-y-5">
                <div>
                    <h4 class="font-semibold text-sm mb-3">Danh mục</h4>
                    <div class="space-y-2 text-sm">
                        <a href="/Shop/all" class="block hover:text-primary transition <?= !$currentCategory ? 'text-primary' : 'text-on-surface' ?>">
                            Tất cả
                        </a>
                        <?php foreach ($categories as $c): ?>
                            <a href="/Shop/category/<?= $c->id ?>"
                               class="block hover:text-primary transition <?= ($currentCategory && $currentCategory->id == $c->id) ? 'text-primary font-medium' : 'text-on-surface' ?>">
                                <?= htmlspecialchars($c->name) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="border-t border-outline-variant/20 pt-4">
                    <h4 class="font-semibold text-sm mb-3">Khoảng giá (đ)</h4>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <input type="number" placeholder="Min" class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2 text-sm focus:border-primary focus:outline-none">
                        <input type="number" placeholder="Max" class="bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- PROMO CARD -->
        <div class="bg-gradient-to-br from-primary-container/30 to-tertiary-container/10 border border-primary-container/30 rounded-xl p-5">
            <span class="inline-block px-2.5 py-0.5 bg-primary-container/40 text-primary text-xs font-semibold tracking-widest rounded mb-3">TECH DEAL</span>
            <h4 class="font-bold mb-2">Build Your Pro Setup</h4>
            <p class="text-on-surface-variant text-xs mb-4">Get 15% off on peripherals with any laptop purchase.</p>
            <a href="#" class="text-primary text-sm font-medium hover:underline">Explore Bundles →</a>
        </div>
    </aside>

    <!-- PRODUCT GRID -->
    <div>
        <?php if (empty($products)): ?>
            <div class="bg-surface-container rounded-xl p-16 text-center border border-outline-variant/20">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 80px">inventory_2</span>
                <p class="text-on-surface-variant mt-4">Chưa có sản phẩm nào trong danh mục này.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($products as $product): ?>
                    <a href="/Shop/detail/<?= $product->getID() ?>"
                       class="bg-surface-container hover:bg-surface-container-high transition rounded-xl border border-outline-variant/20 hover:border-primary/40 group flex flex-col overflow-hidden">

                        <div class="aspect-square bg-surface-container-low flex items-center justify-center overflow-hidden relative">
                            <?php if ($product->getImage()): ?>
                                <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                                     alt="<?= htmlspecialchars($product->getName()) ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 100px">image</span>
                            <?php endif; ?>
                            <span class="absolute top-3 left-3 bg-primary-container/40 text-primary text-xs font-semibold tracking-widest px-2 py-0.5 rounded">NEW</span>
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <div class="text-on-surface-variant text-xs mb-1"><?= htmlspecialchars($product->getCategoryName() ?: '') ?></div>
                            <h3 class="font-semibold mb-2 line-clamp-2"><?= htmlspecialchars($product->getName()) ?></h3>
                            <p class="text-on-surface-variant text-xs mb-3 line-clamp-1">
                                <?= htmlspecialchars(mb_strimwidth($product->getDescription() ?? '', 0, 60, '...')) ?>
                            </p>
                            <div class="flex items-center justify-between mt-auto pt-2">
                                <span class="text-secondary-container font-bold text-lg">
                                    <?= number_format($product->getPrice(), 0, ',', '.') ?>đ
                                </span>
                                <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?= $product->getID() ?>, this)" class="w-10 h-10 rounded-full bg-surface-container-high hover:bg-primary-container hover:text-white flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                                </button>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function addToCart(id, btn) {
    fetch('/Cart/add/' + id, { method: 'POST' })
        .then(() => {
            const badge = document.querySelector('.cart-badge');
            if (badge) { badge.classList.remove('hidden'); badge.textContent = parseInt(badge.textContent || 0) + 1; }
            if (btn) {
                const icon = btn.querySelector('.material-symbols-outlined');
                icon.textContent = 'check';
                btn.classList.add('bg-primary-container', 'text-white');
                setTimeout(() => { icon.textContent = 'add_shopping_cart'; btn.classList.remove('bg-primary-container','text-white'); }, 1200);
            }
        });
}
</script>
<?php include 'app/views/layouts/customer_footer.php'; ?>
