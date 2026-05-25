<?php
$pageTitle  = 'TECH-SPECTRUM | High-Performance Hardware';
$activeMenu = 'home';
include 'app/views/layouts/customer_header.php';
?>



<!-- HERO SECTION -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center py-8 lg:py-16">
    <div class="space-y-6">
        <span class="inline-block px-3 py-1 bg-primary-container/20 border border-primary/30 rounded-full text-primary text-xs font-semibold tracking-widest uppercase">
            New Arrival: Phantom X1
        </span>
        <h1 class="text-5xl md:text-6xl font-bold tracking-tight leading-tight">
            UNLEASH THE<br>
            <span class="text-secondary-container">POWER WITHIN.</span>
        </h1>
        <p class="text-on-surface-variant text-base leading-relaxed max-lg">
            Experience uncompromised performance with the new Phantom X1. Featuring the latest generation processors and liquid-cooled thermals for the ultimate professional edge.
        </p>
        <div class="flex flex-wrap gap-3 pt-2">
            <a href="/Shop/category/2" class="bg-primary-container hover:bg-primary-container/90 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition glow-btn font-medium">
                <span>Buy Now</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
            <a href="#" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-6 py-3 rounded-lg transition font-medium border border-outline-variant/30">
                Technical Specs
            </a>
        </div>
    </div>

    <div class="aspect-square rounded-xl bg-gradient-to-br from-surface-container to-surface-container-low border border-outline-variant/20 flex items-center justify-center overflow-hidden">
        <div class="text-center p-8">
            <span class="material-symbols-outlined text-primary-container" style="font-size: 200px; opacity: 0.4">laptop_mac</span>
        </div>
    </div>
</section>

<!-- SHOP BY CATEGORY -->
<section class="py-12">
    <h2 class="text-3xl font-bold mb-6">Shop by <span class="text-primary">Category</span></h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <?php
        $categoryIcons = [
            'Điện thoại'        => 'smartphone',
            'Laptop'            => 'laptop_mac',
            'Máy tính bảng'     => 'tablet_mac',
            'Phụ kiện'          => 'headphones',
            'Thiết bị âm thanh' => 'speaker'
        ];
        $first = true;
        if (!empty($categories)):
            foreach ($categories as $idx => $cat):
                $icon = $categoryIcons[$cat->name] ?? 'category';
                if ($first):
                    $first = false; ?>
                    <a href="/Shop/category/<?= $cat->id ?>" class="bg-surface-container hover:bg-surface-container-high transition rounded-xl p-8 row-span-2 flex flex-col justify-between border border-outline-variant/20 hover:border-primary/40 group">
                        <div>
                            <h3 class="text-2xl font-semibold mb-2"><?= htmlspecialchars($cat->name) ?></h3>
                            <p class="text-on-surface-variant text-sm"><?= htmlspecialchars($cat->description ?? '') ?></p>
                        </div>
                        <div class="flex-1 flex items-center justify-center my-6">
                            <span class="material-symbols-outlined text-primary opacity-30 group-hover:opacity-50 transition" style="font-size: 180px"><?= $icon ?></span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface text-sm font-medium">
                            <span>Explore All</span>
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </div>
                    </a>
                    <div class="grid gap-4">
                <?php else: ?>
                    <a href="/Shop/category/<?= $cat->id ?>" class="bg-surface-container hover:bg-surface-container-high transition rounded-xl p-6 flex items-center justify-between border border-outline-variant/20 hover:border-primary/40 group">
                        <div>
                            <h3 class="text-xl font-semibold mb-1"><?= htmlspecialchars($cat->name) ?></h3>
                            <p class="text-on-surface-variant text-sm"><?= htmlspecialchars($cat->description ?? '') ?></p>
                        </div>
                        <span class="material-symbols-outlined text-primary opacity-30 group-hover:opacity-50 transition" style="font-size: 80px"><?= $icon ?></span>
                    </a>
                <?php endif;
            endforeach;
            if (!$first): ?>
                </div>
            <?php endif;
        endif; ?>
    </div>
</section>

<!-- BEST SELLERS -->
<section class="py-12">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold">Best <span class="text-primary">Sellers</span></h2>
            <p class="text-on-surface-variant text-sm mt-1">Voted by our community of enthusiasts.</p>
        </div>
        <a href="/Shop/all" class="text-primary text-sm font-medium hover:underline underline-offset-4">View All Products</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php if (!empty($products)):
            $featuredProducts = array_slice($products, 0, 4);
            foreach ($featuredProducts as $product): ?>
                <a href="/Shop/detail/<?= $product->getID() ?>" class="bg-surface-container hover:bg-surface-container-high transition rounded-xl p-4 border border-outline-variant/20 hover:border-primary/40 group flex flex-col">
                    <div class="aspect-square rounded-lg bg-surface-container-low overflow-hidden mb-4 flex items-center justify-center">
                        <?php if ($product->getImage()): ?>
                            <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                                 alt="<?= htmlspecialchars($product->getName()) ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 80px">image</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-on-surface font-medium text-sm mb-2 line-clamp-2"><?= htmlspecialchars($product->getName()) ?></h3>
                    <div class="flex items-center text-secondary-container text-xs mb-3">
                        <span class="material-symbols-outlined text-xs">star</span>
                        <span class="material-symbols-outlined text-xs">star</span>
                        <span class="material-symbols-outlined text-xs">star</span>
                        <span class="material-symbols-outlined text-xs">star</span>
                        <span class="material-symbols-outlined text-xs">star_half</span>
                        <span class="text-on-surface-variant ml-1">(<?= rand(20, 200) ?>)</span>
                    </div>
                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-secondary-container font-bold text-lg"><?= number_format($product->getPrice(), 0, ',', '.') ?>đ</span>
                        <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?= $product->getID() ?>, this)" class="w-9 h-9 rounded-full bg-surface-container-high hover:bg-primary-container hover:text-white flex items-center justify-center transition">
                            <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                        </button>
                    </div>
                </a>
            <?php endforeach;
        else: ?>
            <div class="col-span-4 text-center py-12 text-on-surface-variant">Chưa có sản phẩm nào.</div>
        <?php endif; ?>
    </div>
</section>

<!-- PROMO BANNER -->
<section class="py-12">
    <div class="bg-gradient-to-br from-primary-container/20 to-tertiary-container/10 border border-primary-container/30 rounded-xl p-10 grid md:grid-cols-2 gap-6 items-center">
        <div>
            <h2 class="text-3xl font-bold mb-3">
                Upgrade your<br>
                <span class="text-secondary-container">Workstation</span>
            </h2>
            <p class="text-on-surface-variant text-sm mb-6">
                Get up to 30% off on all accessories when you purchase any Phantom Series laptop. Limited time offer for the tech community.
            </p>
            <a href="/Shop/category/4" class="inline-block bg-surface-container-high hover:bg-surface-bright text-on-surface px-5 py-2.5 rounded-lg text-sm font-medium border border-outline-variant/30 transition">
                Claim Discount
            </a>
        </div>
        <div class="hidden md:flex items-center justify-center">
            <span class="material-symbols-outlined text-primary opacity-30" style="font-size: 200px">desktop_windows</span>
        </div>
    </div>
</section>

<!-- TRUST FEATURES -->
<section class="py-12 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
    <?php
    $features = [
        ['local_shipping', 'Global Shipping', 'Tracked from hub to door'],
        ['verified',       'Authentic Tech',  'Certified genuine hardware'],
        ['support_agent',  '24/7 Expert Help','Talk to hardware specialists'],
        ['shield',         '2-Year Warranty', 'Comprehensive protection'],
    ];
    foreach ($features as $f): ?>
        <div class="flex flex-col items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl"><?= $f[0] ?></span>
            <h4 class="font-semibold text-on-surface text-sm"><?= $f[1] ?></h4>
            <p class="text-on-surface-variant text-xs"><?= $f[2] ?></p>
        </div>
    <?php endforeach; ?>
</section>

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
