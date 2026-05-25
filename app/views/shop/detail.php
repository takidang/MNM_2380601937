<?php
$pageTitle  = $product->getName() . ' | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';
?>

<!-- BREADCRUMB -->
<nav class="text-sm text-on-surface-variant mb-6 flex items-center gap-2">
    <a href="/" class="hover:text-primary transition">Trang chủ</a>
    <span>/</span>
    <a href="/Shop/category/<?= $product->getCategory() ?>" class="hover:text-primary transition"><?= htmlspecialchars($product->getCategoryName() ?: 'Sản phẩm') ?></a>
    <span>/</span>
    <span class="text-on-surface line-clamp-1"><?= htmlspecialchars($product->getName()) ?></span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

    <!-- LEFT: IMAGES -->
    <div>
        <div class="bg-surface-container rounded-xl overflow-hidden aspect-square relative border border-outline-variant/20 mb-4">
            <span class="absolute top-4 left-4 bg-primary-container/40 text-primary text-xs font-bold tracking-widest px-3 py-1 rounded z-10">TECH SPEC ELITE</span>
            <?php if ($product->getImage()): ?>
                <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                     alt="<?= htmlspecialchars($product->getName()) ?>"
                     class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 200px">image</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Thumbnail row -->
        <div class="grid grid-cols-4 gap-3">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="aspect-square bg-surface-container rounded-lg border <?= $i===0 ? 'border-primary' : 'border-outline-variant/20' ?> hover:border-primary transition cursor-pointer flex items-center justify-center overflow-hidden">
                    <?php if ($i === 3): ?>
                        <span class="text-on-surface-variant text-sm font-medium">+5</span>
                    <?php elseif ($product->getImage()): ?>
                        <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                             alt="thumb" class="w-full h-full object-cover opacity-70 hover:opacity-100 transition">
                    <?php else: ?>
                        <span class="material-symbols-outlined text-on-surface-variant text-2xl">image</span>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- RIGHT: PRODUCT INFO -->
    <div class="space-y-5">
        <!-- Rating -->
        <div class="flex items-center gap-2 text-on-surface-variant text-sm">
            <div class="flex text-secondary-container">
                <span class="material-symbols-outlined text-base">star</span>
                <span class="material-symbols-outlined text-base">star</span>
                <span class="material-symbols-outlined text-base">star</span>
                <span class="material-symbols-outlined text-base">star</span>
                <span class="material-symbols-outlined text-base">star_half</span>
            </div>
            <span>(<?= rand(50, 500) ?> Đánh giá)</span>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold leading-tight"><?= htmlspecialchars($product->getName()) ?></h1>
        <p class="text-on-surface-variant"><?= htmlspecialchars($product->getDescription() ?? '') ?></p>

        <!-- Specs (option groups - placeholder) -->
        <div class="bg-surface-container rounded-xl p-5 space-y-4 border border-outline-variant/20">
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant tracking-widest mb-2">CẤU HÌNH RAM</label>
                <div class="grid grid-cols-3 gap-2">
                    <button class="bg-surface-container-low border border-primary text-primary rounded-lg py-2 text-sm font-medium">16GB</button>
                    <button class="bg-surface-container-low border border-outline-variant/30 text-on-surface rounded-lg py-2 text-sm hover:border-primary transition">32GB</button>
                    <button class="bg-surface-container-low border border-outline-variant/30 text-on-surface rounded-lg py-2 text-sm hover:border-primary transition">64GB</button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant tracking-widest mb-2">LƯU TRỮ SSD</label>
                <div class="grid grid-cols-2 gap-2">
                    <button class="bg-surface-container-low border border-outline-variant/30 text-on-surface rounded-lg py-2 text-sm hover:border-primary transition">512GB</button>
                    <button class="bg-surface-container-low border border-primary text-primary rounded-lg py-2 text-sm font-medium">1TB</button>
                </div>
            </div>
        </div>

        <!-- Price -->
        <div>
            <div class="text-4xl font-bold text-secondary-container">
                <?= number_format($product->getPrice(), 0, ',', '.') ?>đ
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-on-surface-variant line-through text-sm"><?= number_format($product->getPrice() * 1.06, 0, ',', '.') ?>đ</span>
                <span class="bg-error-container/40 text-error text-xs font-bold px-2 py-0.5 rounded">-6% GIẢM</span>
                <span class="text-on-surface-variant text-sm">Tiết kiệm <?= number_format($product->getPrice() * 0.06, 0, ',', '.') ?>đ</span>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="space-y-3">
            <button onclick="addToCart(<?= $product->getID() ?>, this)" class="w-full bg-gradient-to-r from-primary-container to-secondary-container/80 hover:opacity-90 text-white rounded-lg py-4 font-semibold flex items-center justify-center gap-2 transition glow-btn">
                <span class="material-symbols-outlined">add_shopping_cart</span>
                <span>Thêm vào giỏ hàng</span>
            </button>
            <form action="/Cart/buyNow/<?= $product->getID() ?>" method="POST">
                <button type="submit" class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface rounded-lg py-4 font-semibold transition border border-outline-variant/30">
                    Mua ngay
                </button>
            </form>
        </div>

        <!-- Trust line -->
        <div class="grid grid-cols-2 gap-4 pt-2">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-primary">local_shipping</span>
                <div>
                    <div class="text-sm font-semibold">Giao hàng miễn phí</div>
                    <div class="text-xs text-on-surface-variant">Nhận hàng trong 2 giờ tại TP HCM</div>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-primary">shield</span>
                <div>
                    <div class="text-sm font-semibold">Bảo hành 12 tháng</div>
                    <div class="text-xs text-on-surface-variant">Chính hãng phân phối toàn quốc</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RELATED PRODUCTS -->
<?php if (!empty($relatedProducts)): ?>
<section class="py-12 mt-8 border-t border-outline-variant/20">
    <h2 class="text-2xl font-bold mb-6">Sản phẩm <span class="text-primary">cùng danh mục</span></h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($relatedProducts as $rp): ?>
            <a href="/Shop/detail/<?= $rp->getID() ?>" class="bg-surface-container hover:bg-surface-container-high transition rounded-xl p-4 border border-outline-variant/20 hover:border-primary/40 group flex flex-col">
                <div class="aspect-square rounded-lg bg-surface-container-low overflow-hidden mb-3 flex items-center justify-center">
                    <?php if ($rp->getImage()): ?>
                        <img src="/public/images/products/<?= htmlspecialchars($rp->getImage()) ?>" alt="<?= htmlspecialchars($rp->getName()) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 60px">image</span>
                    <?php endif; ?>
                </div>
                <h3 class="font-medium text-sm mb-2 line-clamp-2"><?= htmlspecialchars($rp->getName()) ?></h3>
                <span class="text-secondary-container font-bold mt-auto"><?= number_format($rp->getPrice(), 0, ',', '.') ?>đ</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
function addToCart(id, btn) {
    fetch('/Cart/add/' + id, { method: 'POST' })
        .then(() => {
            const badge = document.querySelector('.cart-badge');
            if (badge) { badge.classList.remove('hidden'); badge.textContent = parseInt(badge.textContent || 0) + 1; }
            if (btn) {
                const icon = btn.querySelector('.material-symbols-outlined');
                const text = btn.querySelector('span:last-child');
                icon.textContent = 'check';
                if (text) text.textContent = 'Đã thêm vào giỏ!';
                setTimeout(() => {
                    icon.textContent = 'add_shopping_cart';
                    if (text) text.textContent = 'Thêm vào giỏ hàng';
                }, 1500);
            }
        });
}
</script>
<?php include 'app/views/layouts/customer_footer.php'; ?>
