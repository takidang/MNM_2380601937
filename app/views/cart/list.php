<?php
$pageTitle  = 'Giỏ hàng | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';
$shipping = empty($cartItems) ? 0 : 25000;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;
?>

<!-- BREADCRUMB -->
<nav class="text-sm text-on-surface-variant mb-4 flex items-center gap-2">
    <a href="/" class="hover:text-primary transition">Home</a>
    <span class="material-symbols-outlined text-base">chevron_right</span>
    <span class="text-on-surface">Giỏ hàng</span>
</nav>

<h1 class="text-4xl font-bold mb-8">
    Giỏ hàng <span class="text-primary text-2xl">(<?= count($cartItems) ?> sản phẩm)</span>
</h1>

<?php if (empty($cartItems)): ?>
    <div class="bg-surface-container rounded-xl p-16 text-center border border-outline-variant/20">
        <span class="material-symbols-outlined text-on-surface-variant" style="font-size: 100px">shopping_cart</span>
        <h3 class="text-xl font-semibold mt-4 mb-2">Giỏ hàng trống</h3>
        <p class="text-on-surface-variant text-sm mb-6">Khám phá các sản phẩm công nghệ hiệu suất cao của chúng tôi.</p>
        <a href="/" class="inline-block bg-primary-container hover:bg-primary-container/90 text-white px-6 py-3 rounded-lg font-medium transition glow-btn">
            Tiếp tục mua sắm
        </a>
    </div>
<?php else: ?>
<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">

    <!-- LEFT: CART LIST -->
    <div>
        <div class="grid grid-cols-[1fr_100px_140px_120px] gap-4 px-4 py-3 text-xs font-semibold tracking-widest text-on-surface-variant uppercase border-b border-outline-variant/20">
            <div>Chi tiết sản phẩm</div>
            <div class="text-center">Giá</div>
            <div class="text-center">Số lượng</div>
            <div class="text-right">Tổng</div>
        </div>

        <div class="space-y-3 mt-4">
            <?php foreach ($cartItems as $item):
                $p = $item['product']; ?>
                <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-4 grid grid-cols-[1fr_100px_140px_120px] gap-4 items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-lg bg-surface-container-low overflow-hidden flex items-center justify-center shrink-0">
                            <?php if ($p->getImage()): ?>
                                <img src="/public/images/products/<?= htmlspecialchars($p->getImage()) ?>"
                                     alt="<?= htmlspecialchars($p->getName()) ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant">image</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1"><?= htmlspecialchars($p->getName()) ?></h3>
                            <p class="text-on-surface-variant text-xs mb-2 line-clamp-1">
                                <?= htmlspecialchars(mb_strimwidth($p->getDescription() ?? '', 0, 60, '...')) ?>
                            </p>
                            <div class="flex items-center gap-1 text-secondary-container text-xs">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span>Còn hàng</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center font-medium">
                        <?= number_format($p->getPrice(), 0, ',', '.') ?>đ
                    </div>

                    <div class="flex justify-center">
                        <form action="/Cart/update/<?= $p->getID() ?>" method="POST" class="flex items-center bg-surface-container-low rounded-lg overflow-hidden border border-outline-variant/30">
                            <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>"
                                    class="w-8 h-8 hover:bg-surface-container-high text-on-surface flex items-center justify-center transition">−</button>
                            <input type="text" value="<?= $item['quantity'] ?>" readonly class="w-10 text-center bg-transparent border-0 outline-none text-sm">
                            <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>"
                                    class="w-8 h-8 hover:bg-surface-container-high text-on-surface flex items-center justify-center transition">+</button>
                        </form>
                    </div>

                    <div class="text-right">
                        <div class="text-secondary-container font-bold">
                            <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                        </div>
                        <a href="/Cart/remove/<?= $p->getID() ?>"
                           onclick="return confirm('Xóa sản phẩm khỏi giỏ?')"
                           class="text-error text-xs hover:underline mt-1 inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            Xóa
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="/" class="inline-flex items-center gap-2 text-on-surface hover:text-primary transition mt-6 text-sm">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Tiếp tục mua sắm</span>
        </a>
    </div>

    <!-- RIGHT: ORDER SUMMARY -->
    <div class="space-y-4">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-xl font-bold mb-5">Tóm tắt đơn hàng</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-on-surface">Tạm tính</span>
                    <span class="font-medium"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface">Phí vận chuyển</span>
                    <span class="font-medium"><?= number_format($shipping, 0, ',', '.') ?>đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface">VAT (8%)</span>
                    <span class="font-medium"><?= number_format($tax, 0, ',', '.') ?>đ</span>
                </div>
            </div>

            <div class="bg-surface-container-low rounded-lg flex items-center justify-between px-4 py-3 my-5 border border-outline-variant/20">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant text-base">local_offer</span>
                    <input type="text" placeholder="Mã giảm giá" class="bg-transparent border-0 outline-none text-sm w-full">
                </div>
                <button class="text-primary text-sm font-semibold tracking-widest">APPLY</button>
            </div>

            <div class="border-t border-outline-variant/20 pt-4 mb-5">
                <div class="flex justify-between items-baseline">
                    <span class="font-semibold">Tổng cộng</span>
                    <span class="text-secondary-container text-2xl font-bold"><?= number_format($total, 0, ',', '.') ?>đ</span>
                </div>
                <p class="text-on-surface-variant text-xs mt-1 text-right">Đã bao gồm VAT</p>
            </div>

            <a href="/Cart/checkout" class="w-full bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3.5 font-semibold flex items-center justify-center gap-2 transition glow-btn">
                <span>Tiến hành thanh toán</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-5 flex items-start gap-3">
            <span class="material-symbols-outlined text-primary">shield</span>
            <div>
                <h4 class="font-semibold text-sm mb-1">Tech-Spectrum Protection</h4>
                <p class="text-on-surface-variant text-xs leading-relaxed">
                    Mọi đơn hàng được bảo hành 12 tháng chính hãng và hỗ trợ kỹ thuật 24/7 từ đội ngũ engineering.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'app/views/layouts/customer_footer.php'; ?>
