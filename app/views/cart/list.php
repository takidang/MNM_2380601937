<?php
$pageTitle  = 'Giỏ hàng | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';
$shipping = empty($cartItems) ? 0 : 25000;
$tax      = $subtotal * 0.08;
$total    = $subtotal + $shipping + $tax - $discount;
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
                            <h3 class="font-semibold mb-0.5"><?= htmlspecialchars($p->getName()) ?></h3>
                            <?php if (!empty($item['variant_name'])): ?>
                                <span class="inline-block bg-primary-container/20 text-primary text-xs px-2 py-0.5 rounded mb-1"><?= htmlspecialchars($item['variant_name']) ?></span>
                            <?php endif; ?>
                            <div class="flex items-center gap-1 text-secondary-container text-xs">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span>Còn hàng</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center font-medium text-sm">
                        <?= number_format($item['price'], 0, ',', '.') ?>đ
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
                <?php if ($discount > 0 && $coupon): ?>
                <div class="flex justify-between text-secondary-container">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">local_offer</span>
                        Mã <?= htmlspecialchars($coupon['code']) ?> (-<?= $coupon['percent'] ?>%)
                    </span>
                    <span class="font-medium">-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Coupon input -->
            <?php if (!$coupon): ?>
            <div id="couponBox" class="bg-surface-container-low rounded-lg flex items-center justify-between px-4 py-3 my-5 border border-outline-variant/20">
                <div class="flex items-center gap-2 flex-1">
                    <span class="material-symbols-outlined text-on-surface-variant text-base">local_offer</span>
                    <input type="text" id="couponInput" placeholder="Mã giảm giá" class="bg-transparent border-0 outline-none text-sm w-full uppercase">
                </div>
                <button onclick="applyCoupon()" class="text-primary text-sm font-semibold tracking-widest hover:text-primary/80 transition">APPLY</button>
            </div>
            <div id="couponMsg" class="text-xs mb-4 hidden"></div>
            <?php else: ?>
            <div class="bg-secondary-container/10 border border-secondary-container/30 rounded-lg flex items-center justify-between px-4 py-3 my-5">
                <div class="flex items-center gap-2 text-secondary-container text-sm">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    <span>Đã áp dụng mã <strong><?= htmlspecialchars($coupon['code']) ?></strong></span>
                </div>
                <a href="/Cart/removeCoupon" class="text-error text-xs hover:underline">Xóa</a>
            </div>
            <?php endif; ?>

            <div class="border-t border-outline-variant/20 pt-4 mb-5">
                <div class="flex justify-between items-baseline">
                    <span class="font-semibold">Tổng cộng</span>
                    <span id="totalDisplay" class="text-secondary-container text-2xl font-bold"><?= number_format($total, 0, ',', '.') ?>đ</span>
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
                    Mọi đơn hàng được bảo hành 12 tháng chính hãng và hỗ trợ kỹ thuật 24/7.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const subtotal = <?= $subtotal ?>;
const shipping = <?= $shipping ?>;
const tax      = <?= $tax ?>;

function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    const msg  = document.getElementById('couponMsg');
    if (!code) return;

    fetch('/Cart/applyCoupon', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'code=' + encodeURIComponent(code) + '&subtotal=' + encodeURIComponent(subtotal)
    })
    .then(r => r.json())
    .then(data => {
        msg.classList.remove('hidden', 'text-error', 'text-secondary-container');
        if (data.success) {
            msg.textContent = data.message;
            msg.classList.add('text-secondary-container');
            const newTotal = subtotal + shipping + tax - data.discount;
            document.getElementById('totalDisplay').textContent = newTotal.toLocaleString('vi-VN') + 'đ';
            setTimeout(() => location.reload(), 800);
        } else {
            msg.textContent = data.message;
            msg.classList.add('text-error');
        }
        msg.classList.remove('hidden');
    });
}

document.getElementById('couponInput') && document.getElementById('couponInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); }
});
</script>

<?php include 'app/views/layouts/customer_footer.php'; ?>
