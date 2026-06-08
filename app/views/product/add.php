<?php
$pageTitle  = 'Thêm sản phẩm | TECH-SPECTRUM Admin';
$activeMenu = 'inventory';
include 'app/views/layouts/admin_header.php';
?>

<!-- HEADER -->
<div class="flex items-center justify-between mb-8">
    <div>
        <nav class="text-sm text-on-surface-variant mb-2 flex items-center gap-2">
            <a href="/Product/list" class="hover:text-primary transition">Inventory</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface">Thêm sản phẩm</span>
        </nav>
        <h1 class="text-3xl font-bold">Thêm sản phẩm mới</h1>
    </div>
    <a href="/Product/list" class="bg-surface-container hover:bg-surface-container-high text-on-surface px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 border border-outline-variant/30 transition">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Quay lại
    </a>
</div>

<div id="errorBox" class="bg-error-container/20 border border-error/40 text-error rounded-lg p-4 mb-6 text-sm hidden">
    <ul id="errorList" class="list-disc list-inside space-y-1"></ul>
</div>

<form id="addForm" class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-6">

    <!-- LEFT: BASIC INFO -->
    <div class="space-y-6">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit_note</span>
                Thông tin cơ bản
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Tên sản phẩm <span class="text-error">*</span></label>
                    <input type="text" id="name" required
                           placeholder="VD: MacBook Pro M3 Max 14-inch"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Giá bán (đ) <span class="text-error">*</span></label>
                        <input type="number" id="price" required min="1" step="1"
                               placeholder="VD: 25990000"
                               class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Danh mục <span class="text-error">*</span></label>
                        <select id="category_id" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
                            <option value="">-- Đang tải... --</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Mô tả sản phẩm <span class="text-error">*</span></label>
                    <textarea id="description" rows="5" required
                              placeholder="Mô tả chi tiết tính năng, thông số kỹ thuật..."
                              class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: ACTION -->
    <div class="space-y-6">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 space-y-3">
            <button type="submit" id="submitBtn" class="w-full bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3 font-semibold flex items-center justify-center gap-2 transition glow-btn">
                <span class="material-symbols-outlined text-base">save</span>
                Lưu sản phẩm
            </button>
            <a href="/Product/list" class="block w-full text-center bg-surface-container-high hover:bg-surface-bright text-on-surface rounded-lg py-3 font-medium transition border border-outline-variant/30">
                Hủy bỏ
            </a>
        </div>
    </div>
</form>

<script>
async function loadCategories() {
    const select = document.getElementById('category_id');
    const res = await fetch('/api/category');
    const categories = await res.json();
    select.innerHTML = '<option value="">-- Chọn danh mục --</option>' +
        categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
}

document.getElementById('addForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;

    const payload = {
        name:        document.getElementById('name').value,
        description: document.getElementById('description').value,
        price:       document.getElementById('price').value,
        category_id: document.getElementById('category_id').value,
    };

    const res  = await fetch('/api/product', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(payload),
    });
    const data = await res.json();

    if (res.status === 201) {
        window.location.href = '/Product/list';
        return;
    }

    const box  = document.getElementById('errorBox');
    const list = document.getElementById('errorList');
    const errs = data.errors ?? [data.error ?? 'Lỗi không xác định'];
    list.innerHTML = errs.map(e => `<li>${e}</li>`).join('');
    box.classList.remove('hidden');
    btn.disabled = false;
});

document.addEventListener('DOMContentLoaded', loadCategories);
</script>

<?php include 'app/views/layouts/admin_footer.php'; ?>
