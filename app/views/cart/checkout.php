<?php
$pageTitle  = 'Thanh toán | TECH-SPECTRUM';
$activeMenu = '';
include 'app/views/layouts/customer_header.php';
$shipping = 0; // Miễn phí cho demo
$discount = 0;
$total = $subtotal + $shipping - $discount;
?>

<!-- BREADCRUMB -->
<nav class="text-sm text-on-surface-variant mb-4 flex items-center gap-2">
    <a href="/" class="hover:text-primary transition">Trang chủ</a>
    <span class="material-symbols-outlined text-base">chevron_right</span>
    <a href="/Cart/list" class="hover:text-primary transition">Giỏ hàng</a>
    <span class="material-symbols-outlined text-base">chevron_right</span>
    <span class="text-on-surface">Thanh toán</span>
</nav>

<?php if (!empty($errors)): ?>
    <div class="bg-error-container/20 border border-error/40 text-error rounded-lg p-4 mb-6 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/Cart/checkout" method="POST" class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6">

    <!-- LEFT: FORM -->
    <div class="space-y-6">

        <!-- Customer info -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-xl font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">local_shipping</span>
                Thông tin nhận hàng
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-2">Họ và tên <span class="text-error">*</span></label>
                    <input type="text" name="customer_name" required
                           value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>"
                           placeholder="Nguyễn Văn A"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm mb-2">Số điện thoại <span class="text-error">*</span></label>
                    <input type="tel" name="customer_phone" required
                           value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>"
                           placeholder="090 123 4567"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">Email</label>
                    <input type="email" name="customer_email"
                           value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>"
                           placeholder="example@techspectrum.com"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">Địa chỉ giao hàng <span class="text-error">*</span></label>
                    <textarea name="customer_address" required rows="3"
                              placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..."
                              class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm resize-none"><?= htmlspecialchars($_POST['customer_address'] ?? '') ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-2">Ghi chú</label>
                    <input type="text" name="note"
                           value="<?= htmlspecialchars($_POST['note'] ?? '') ?>"
                           placeholder="Ghi chú thêm (vd: Giao giờ hành chính)"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 focus:border-primary focus:outline-none transition text-sm">
                </div>
            </div>
        </div>

        <!-- Payment method -->
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-xl font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">payments</span>
                Phương thức thanh toán
            </h2>

            <div class="space-y-3">
                <label class="bg-surface-container-low border border-primary rounded-lg p-4 flex items-start gap-3 cursor-pointer">
                    <input type="radio" name="payment_method" value="cod" checked class="mt-1 accent-primary">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">Thanh toán khi nhận hàng (COD)</div>
                            <span class="material-symbols-outlined text-on-surface-variant">local_mall</span>
                        </div>
                        <p class="text-on-surface-variant text-sm mt-1">Thanh toán bằng tiền mặt khi shipper giao hàng đến địa chỉ của bạn.</p>
                    </div>
                </label>

                <label class="bg-surface-container-low border border-outline-variant/30 hover:border-primary/50 rounded-lg p-4 flex items-start gap-3 cursor-pointer transition">
                    <input type="radio" name="payment_method" value="bank" class="mt-1 accent-primary">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">Chuyển khoản ngân hàng</div>
                            <span class="material-symbols-outlined text-on-surface-variant">account_balance</span>
                        </div>
                        <p class="text-on-surface-variant text-sm mt-1">Quét mã QR hoặc chuyển khoản trực tiếp qua ứng dụng ngân hàng.</p>
                    </div>
                </label>

                <label class="bg-surface-container-low border border-outline-variant/30 hover:border-primary/50 rounded-lg p-4 flex items-start gap-3 cursor-pointer transition">
                    <input type="radio" name="payment_method" value="card" class="mt-1 accent-primary">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">Thẻ tín dụng / Ghi nợ</div>
                            <span class="material-symbols-outlined text-on-surface-variant">credit_card</span>
                        </div>
                        <p class="text-on-surface-variant text-sm mt-1">Thanh toán an toàn qua cổng liên kết quốc tế (Visa, Mastercard, JCB).</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- RIGHT: ORDER SUMMARY -->
    <div class="space-y-4">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-xl font-bold mb-5 border-b border-outline-variant/20 pb-4">Đơn hàng của bạn</h2>

            <div class="space-y-4 mb-5 max-h-80 overflow-y-auto">
                <?php foreach ($cartItems as $item):
                    $p = $item['product']; ?>
                    <div class="flex items-start gap-3">
                        <div class="w-14 h-14 rounded-lg bg-surface-container-low overflow-hidden flex items-center justify-center shrink-0">
                            <?php if ($p->getImage()): ?>
                                <img src="/public/images/products/<?= htmlspecialchars($p->getImage()) ?>"
                                     class="w-full h-full object-cover" alt="">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant text-base">image</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-sm line-clamp-1"><?= htmlspecialchars($p->getName()) ?></div>
                            <div class="text-on-surface-variant text-xs">SL: <?= $item['quantity'] ?></div>
                        </div>
                        <div class="text-secondary-container font-semibold text-sm whitespace-nowrap">
                            <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="space-y-2 text-sm border-t border-outline-variant/20 pt-4">
                <div class="flex justify-between">
                    <span>Tạm tính</span>
                    <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                </div>
                <div class="flex justify-between">
                    <span>Phí vận chuyển</span>
                    <span class="text-secondary-container">Miễn phí</span>
                </div>
            </div>

            <div class="flex justify-between items-baseline mt-4 pt-4 border-t border-outline-variant/20">
                <span class="font-semibold">Tổng cộng</span>
                <span class="text-secondary-container text-2xl font-bold"><?= number_format($total, 0, ',', '.') ?>đ</span>
            </div>
            <p class="text-on-surface-variant text-xs text-right mt-1">(Đã bao gồm VAT)</p>

            <button type="submit" class="w-full mt-5 bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3.5 font-semibold uppercase tracking-wide flex items-center justify-center gap-2 transition glow-btn">
                <span>Xác nhận đặt hàng</span>
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
            <p class="text-on-surface-variant text-xs text-center mt-3">
                Bằng cách nhấn đặt hàng, bạn đồng ý với <a href="#" class="text-primary hover:underline">Điều khoản dịch vụ</a>.
            </p>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-4 flex items-start gap-3">
            <span class="material-symbols-outlined text-secondary-container">verified_user</span>
            <div>
                <div class="font-semibold text-sm">Thanh toán an toàn 100%</div>
                <div class="text-on-surface-variant text-xs">Thông tin của bạn được mã hóa hoàn toàn.</div>
            </div>
        </div>
    </div>
</form>

<?php include 'app/views/layouts/customer_footer.php'; ?>
