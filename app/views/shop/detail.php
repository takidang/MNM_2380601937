<?php
$pageTitle  = $product->getName() . ' | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';

// Build full image list: main image first, then extras (avoid duplicates)
$allImages = [];
if ($product->getImage()) $allImages[] = $product->getImage();
foreach ($productImages as $img) {
    if ($img->filename !== $product->getImage()) $allImages[] = $img->filename;
}
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
            <?php if (!empty($allImages)): ?>
                <img id="mainImage"
                     src="/public/images/products/<?= htmlspecialchars($allImages[0]) ?>"
                     alt="<?= htmlspecialchars($product->getName()) ?>"
                     class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 200px">image</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Thumbnail row -->
        <?php if (count($allImages) > 1): ?>
        <div class="grid grid-cols-4 gap-3">
            <?php foreach (array_slice($allImages, 0, 4) as $i => $imgFile): ?>
                <div onclick="switchImage('/public/images/products/<?= htmlspecialchars($imgFile) ?>', this)"
                     class="aspect-square bg-surface-container rounded-lg border <?= $i===0 ? 'border-primary' : 'border-outline-variant/20' ?> hover:border-primary transition cursor-pointer overflow-hidden thumb-item">
                    <img src="/public/images/products/<?= htmlspecialchars($imgFile) ?>"
                         alt="thumb" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition">
                </div>
            <?php endforeach; ?>
            <?php if (count($allImages) > 4): ?>
                <div class="aspect-square bg-surface-container rounded-lg border border-outline-variant/20 flex items-center justify-center">
                    <span class="text-on-surface-variant text-sm font-medium">+<?= count($allImages) - 4 ?></span>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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

        <!-- Variants -->
        <?php if (!empty($variants)): ?>
        <div class="bg-surface-container rounded-xl p-5 border border-outline-variant/20">
            <label class="block text-xs font-semibold text-on-surface-variant tracking-widest mb-3">CHỌN CẤU HÌNH</label>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($variants as $i => $v): ?>
                    <button type="button"
                            onclick="selectVariant(this, <?= $v->id ?>, '<?= htmlspecialchars($v->name, ENT_QUOTES) ?>', <?= $v->price ?>)"
                            class="variant-btn px-4 py-2 rounded-lg text-sm font-medium border transition
                                   <?= $i === 0 ? 'border-primary text-primary bg-surface-container-low' : 'border-outline-variant/30 text-on-surface hover:border-primary' ?>"
                            data-price="<?= $v->price ?>">
                        <?= htmlspecialchars($v->name) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Price -->
        <div>
            <div id="displayPrice" class="text-4xl font-bold text-secondary-container">
                <?= number_format(!empty($variants) ? $variants[0]->price : $product->getPrice(), 0, ',', '.') ?>đ
            </div>
        </div>

        <!-- Hidden fields for cart -->
        <input type="hidden" id="selectedVariantId" value="<?= !empty($variants) ? $variants[0]->id : '' ?>">
        <input type="hidden" id="selectedVariantName" value="<?= !empty($variants) ? htmlspecialchars($variants[0]->name, ENT_QUOTES) : '' ?>">

        <!-- CTA Buttons -->
        <div class="space-y-3">
            <button onclick="addToCart(<?= $product->getID() ?>, this)" class="w-full bg-gradient-to-r from-primary-container to-secondary-container/80 hover:opacity-90 text-white rounded-lg py-4 font-semibold flex items-center justify-center gap-2 transition glow-btn">
                <span class="material-symbols-outlined">add_shopping_cart</span>
                <span>Thêm vào giỏ hàng</span>
            </button>
            <form action="/Cart/buyNow/<?= $product->getID() ?>" method="POST" id="buyNowForm">
                <input type="hidden" name="variant_id" id="buyNowVariantId" value="<?= !empty($variants) ? $variants[0]->id : '' ?>">
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
function switchImage(src, el) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumb-item').forEach(t => t.classList.replace('border-primary', 'border-outline-variant/20'));
    el.classList.replace('border-outline-variant/20', 'border-primary');
}

function selectVariant(btn, variantId, variantName, price) {
    document.querySelectorAll('.variant-btn').forEach(b => {
        b.classList.remove('border-primary', 'text-primary');
        b.classList.add('border-outline-variant/30', 'text-on-surface');
    });
    btn.classList.add('border-primary', 'text-primary');
    btn.classList.remove('border-outline-variant/30', 'text-on-surface');

    document.getElementById('displayPrice').textContent = price.toLocaleString('vi-VN') + 'đ';
    document.getElementById('selectedVariantId').value   = variantId;
    document.getElementById('selectedVariantName').value = variantName;
    document.getElementById('buyNowVariantId').value     = variantId;
}

function addToCart(id, btn) {
    const variantId   = document.getElementById('selectedVariantId').value;
    const variantName = document.getElementById('selectedVariantName').value;
    const body = new URLSearchParams({ variant_id: variantId, variant_name: variantName });

    fetch('/Cart/add/' + id, { method: 'POST', body })
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
