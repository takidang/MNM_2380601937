<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>API Demo | TECH-SPECTRUM</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet">
<script>
tailwind.config = {
    darkMode: 'class',
    theme: { extend: { colors: {
        "background": "#0b1326", "surface-container": "#171f33",
        "surface-container-low": "#131b2e", "surface-container-high": "#222a3d",
        "on-surface": "#dae2fd", "on-surface-variant": "#c2c6d8",
        "outline-variant": "#424656", "primary": "#b3c5ff",
        "primary-container": "#0066ff", "error": "#ffb4ab",
        "error-container": "#93000a",
    }}}
};
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400; vertical-align: middle; }
    .overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:40; }
    .modal  { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:50; width:100%; max-width:480px; }
</style>
</head>
<body class="dark bg-background text-on-surface min-h-screen p-6">

<!-- HEADER -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold">API Demo</h1>
        <p class="text-on-surface-variant text-sm mt-1">jQuery + RESTful API — TECH-SPECTRUM</p>
    </div>
    <a href="/Product/list" class="px-4 py-2 rounded-lg border border-outline-variant/30 text-sm hover:bg-surface-container-high transition">
        <span class="material-symbols-outlined text-base">arrow_back</span> Admin view
    </a>
</div>

<!-- ALERT -->
<div id="alert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium border"></div>

<!-- ===================== PRODUCT SECTION ===================== -->
<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden mb-6">
    <div class="p-4 border-b border-outline-variant/20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">inventory_2</span>
            <span class="font-semibold">Sản phẩm</span>
            <span id="product-count" class="text-on-surface-variant text-xs"></span>
        </div>
        <button id="btnAddProduct" class="px-3 py-1.5 rounded-lg bg-primary-container text-white text-xs font-medium hover:bg-primary-container/90 transition flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span> Thêm
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs tracking-widest text-on-surface-variant uppercase border-b border-outline-variant/20">
                <tr>
                    <th class="text-left p-4">ID</th>
                    <th class="text-left p-4">Tên sản phẩm</th>
                    <th class="text-left p-4">Danh mục</th>
                    <th class="text-left p-4">Giá</th>
                    <th class="text-right p-4 pr-6">Thao tác</th>
                </tr>
            </thead>
            <tbody id="product-tbody">
                <tr><td colspan="5" class="text-center py-10 text-on-surface-variant">Đang tải...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== CATEGORY SECTION ===================== -->
<div class="bg-surface-container rounded-xl border border-outline-variant/20 overflow-hidden">
    <div class="p-4 border-b border-outline-variant/20 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">category</span>
            <span class="font-semibold">Danh mục</span>
            <span id="cat-count" class="text-on-surface-variant text-xs"></span>
        </div>
        <button id="btnAddCat" class="px-3 py-1.5 rounded-lg bg-primary-container text-white text-xs font-medium hover:bg-primary-container/90 transition flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span> Thêm
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs tracking-widest text-on-surface-variant uppercase border-b border-outline-variant/20">
                <tr>
                    <th class="text-left p-4">ID</th>
                    <th class="text-left p-4">Tên danh mục</th>
                    <th class="text-left p-4">Mô tả</th>
                    <th class="text-right p-4 pr-6">Thao tác</th>
                </tr>
            </thead>
            <tbody id="cat-tbody">
                <tr><td colspan="4" class="text-center py-10 text-on-surface-variant">Đang tải...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== OVERLAY ===================== -->
<div class="overlay" id="overlay-product"></div>
<div class="overlay" id="overlay-cat"></div>

<!-- ===================== PRODUCT MODAL ===================== -->
<div class="modal bg-surface-container rounded-xl border border-outline-variant/20 p-6 shadow-2xl" id="modal-product">
    <div class="flex items-center justify-between mb-5">
        <h2 id="product-modal-title" class="text-lg font-bold">Thêm sản phẩm</h2>
        <button class="btn-close-product text-on-surface-variant hover:text-on-surface">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div id="product-modal-errors" class="hidden bg-error-container/20 border border-error/40 text-error rounded-lg p-3 mb-4 text-sm"></div>
    <form id="product-form" class="space-y-4">
        <input type="hidden" id="p-id">
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Tên sản phẩm *</label>
            <input type="text" id="p-name" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm focus:border-primary focus:outline-none transition">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-1">Giá (đ) *</label>
                <input type="number" id="p-price" required min="1" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm font-mono focus:border-primary focus:outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-1">Danh mục *</label>
                <select id="p-category" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm focus:border-primary focus:outline-none transition">
                    <option value="">-- Chọn --</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Mô tả *</label>
            <textarea id="p-desc" rows="3" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm focus:border-primary focus:outline-none transition resize-none"></textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" id="p-submit" class="flex-1 bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-2.5 font-semibold text-sm transition">Lưu</button>
            <button type="button" class="btn-close-product flex-1 border border-outline-variant/30 rounded-lg py-2.5 text-sm hover:bg-surface-container-high transition">Hủy</button>
        </div>
    </form>
</div>

<!-- ===================== CATEGORY MODAL ===================== -->
<div class="modal bg-surface-container rounded-xl border border-outline-variant/20 p-6 shadow-2xl" id="modal-cat">
    <div class="flex items-center justify-between mb-5">
        <h2 id="cat-modal-title" class="text-lg font-bold">Thêm danh mục</h2>
        <button class="btn-close-cat text-on-surface-variant hover:text-on-surface">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div id="cat-modal-errors" class="hidden bg-error-container/20 border border-error/40 text-error rounded-lg p-3 mb-4 text-sm"></div>
    <form id="cat-form" class="space-y-4">
        <input type="hidden" id="c-id">
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Tên danh mục *</label>
            <input type="text" id="c-name" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm focus:border-primary focus:outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Mô tả</label>
            <textarea id="c-desc" rows="2" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-3 py-2.5 text-sm focus:border-primary focus:outline-none transition resize-none"></textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" id="c-submit" class="flex-1 bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-2.5 font-semibold text-sm transition">Lưu</button>
            <button type="button" class="btn-close-cat flex-1 border border-outline-variant/30 rounded-lg py-2.5 text-sm hover:bg-surface-container-high transition">Hủy</button>
        </div>
    </form>
</div>

<script>
const BASE = '/api';

// ===== HELPERS =====
function esc(s) { return $('<div>').text(s ?? '').html(); }
function fmtPrice(p) { return Number(p).toLocaleString('vi-VN') + 'đ'; }

function showAlert(msg, isError = false) {
    const $a = $('#alert').removeClass().addClass('mb-4 p-3 rounded-lg text-sm font-medium border');
    $a.addClass(isError ? 'bg-error-container/20 text-error border-error/40' : 'bg-green-500/20 text-green-300 border-green-500/40');
    $a.text(msg).show();
    setTimeout(() => $a.addClass('hidden'), 3000);
}

// ===== PRODUCT MODAL =====
function openProductModal() { $('#overlay-product, #modal-product').show(); }
function closeProductModal() {
    $('#overlay-product, #modal-product').hide();
    $('#product-modal-errors').addClass('hidden').html('');
    $('#product-form')[0].reset();
    $('#p-id').val('');
}

function loadCategoryOptions(selectedId = '') {
    $.ajax({ url: BASE + '/category', method: 'GET', success: function(cats) {
        const $sel = $('#p-category').empty().append('<option value="">-- Chọn --</option>');
        $.each(cats, function(_, c) {
            $sel.append(`<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${esc(c.name)}</option>`);
        });
    }});
}

// ===== CATEGORY MODAL =====
function openCatModal() { $('#overlay-cat, #modal-cat').show(); }
function closeCatModal() {
    $('#overlay-cat, #modal-cat').hide();
    $('#cat-modal-errors').addClass('hidden').html('');
    $('#cat-form')[0].reset();
    $('#c-id').val('');
}

// ===== LOAD PRODUCTS =====
function loadProducts() {
    $.ajax({ url: BASE + '/product', method: 'GET', success: function(list) {
        $('#product-count').text('(' + list.length + ')');
        const $t = $('#product-tbody').empty();
        if (!list.length) { $t.html('<tr><td colspan="5" class="text-center py-10 text-on-surface-variant">Chưa có sản phẩm nào</td></tr>'); return; }
        $.each(list, function(_, p) {
            $t.append(`<tr class="border-b border-outline-variant/10 hover:bg-surface-container-low transition">
                <td class="p-4 font-mono text-on-surface-variant text-xs">#${p.id}</td>
                <td class="p-4">
                    <div class="font-medium line-clamp-1">${esc(p.name)}</div>
                    <div class="text-xs text-on-surface-variant line-clamp-1">${esc(p.description)}</div>
                </td>
                <td class="p-4"><span class="bg-primary-container/20 text-primary px-2 py-0.5 rounded text-xs font-semibold uppercase">${esc(p.category_name || 'OTHER')}</span></td>
                <td class="p-4 font-mono font-medium">${fmtPrice(p.price)}</td>
                <td class="p-4 pr-6">
                    <div class="flex items-center gap-1 justify-end">
                        <button class="btn-edit-product w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary" data-id="${p.id}">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button class="btn-del-product w-8 h-8 rounded-lg hover:bg-error-container/30 flex items-center justify-center transition text-on-surface-variant hover:text-error" data-id="${p.id}" data-name="${esc(p.name)}">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </td>
            </tr>`);
        });
    }});
}

// ===== LOAD CATEGORIES =====
function loadCategories() {
    $.ajax({ url: BASE + '/category', method: 'GET', success: function(list) {
        $('#cat-count').text('(' + list.length + ')');
        const $t = $('#cat-tbody').empty();
        if (!list.length) { $t.html('<tr><td colspan="4" class="text-center py-10 text-on-surface-variant">Chưa có danh mục nào</td></tr>'); return; }
        $.each(list, function(_, c) {
            $t.append(`<tr class="border-b border-outline-variant/10 hover:bg-surface-container-low transition">
                <td class="p-4 font-mono text-on-surface-variant text-xs">#${c.id}</td>
                <td class="p-4 font-medium">${esc(c.name)}</td>
                <td class="p-4 text-on-surface-variant text-xs">${esc(c.description || '—')}</td>
                <td class="p-4 pr-6">
                    <div class="flex items-center gap-1 justify-end">
                        <button class="btn-edit-cat w-8 h-8 rounded-lg hover:bg-surface-container-high flex items-center justify-center transition text-on-surface-variant hover:text-primary" data-id="${c.id}">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button class="btn-del-cat w-8 h-8 rounded-lg hover:bg-error-container/30 flex items-center justify-center transition text-on-surface-variant hover:text-error" data-id="${c.id}" data-name="${esc(c.name)}">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </td>
            </tr>`);
        });
    }});
}

// ===== PRODUCT EVENTS =====
$('#btnAddProduct').on('click', function() {
    $('#product-modal-title').text('Thêm sản phẩm');
    $('#p-submit').text('Thêm');
    loadCategoryOptions();
    openProductModal();
});

$(document).on('click', '.btn-edit-product', function() {
    const id = $(this).data('id');
    $.ajax({ url: BASE + '/product/' + id, method: 'GET', success: function(p) {
        $('#product-modal-title').text('Sửa sản phẩm #' + p.id);
        $('#p-submit').text('Cập nhật');
        $('#p-id').val(p.id); $('#p-name').val(p.name);
        $('#p-price').val(p.price); $('#p-desc').val(p.description);
        loadCategoryOptions(p.category_id);
        openProductModal();
    }});
});

$(document).on('click', '.btn-del-product', function() {
    const id = $(this).data('id'), name = $(this).data('name');
    if (!confirm('Xóa sản phẩm "' + name + '"?')) return;
    $.ajax({ url: BASE + '/product/' + id, method: 'DELETE',
        success: function(r) { showAlert(r.message); loadProducts(); },
        error:   function(x) { showAlert(x.responseJSON?.error ?? 'Lỗi khi xóa', true); }
    });
});

$('#product-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#p-id').val();
    $.ajax({
        url: BASE + '/product' + (id ? '/' + id : ''),
        method: id ? 'PUT' : 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ name: $('#p-name').val(), description: $('#p-desc').val(), price: $('#p-price').val(), category_id: $('#p-category').val() }),
        success: function(r) { closeProductModal(); showAlert(r.message); loadProducts(); },
        error:   function(x) {
            const errs = x.responseJSON?.errors ?? [x.responseJSON?.error ?? 'Lỗi'];
            $('#product-modal-errors').removeClass('hidden').html(errs.map(e => `<div>• ${e}</div>`).join(''));
        }
    });
});

$('.btn-close-product, #overlay-product').on('click', closeProductModal);

// ===== CATEGORY EVENTS =====
$('#btnAddCat').on('click', function() {
    $('#cat-modal-title').text('Thêm danh mục');
    $('#c-submit').text('Thêm');
    openCatModal();
});

$(document).on('click', '.btn-edit-cat', function() {
    const id = $(this).data('id');
    $.ajax({ url: BASE + '/category/' + id, method: 'GET', success: function(c) {
        $('#cat-modal-title').text('Sửa danh mục #' + c.id);
        $('#c-submit').text('Cập nhật');
        $('#c-id').val(c.id); $('#c-name').val(c.name); $('#c-desc').val(c.description);
        openCatModal();
    }});
});

$(document).on('click', '.btn-del-cat', function() {
    const id = $(this).data('id'), name = $(this).data('name');
    if (!confirm('Xóa danh mục "' + name + '"?')) return;
    $.ajax({ url: BASE + '/category/' + id, method: 'DELETE',
        success: function(r) { showAlert(r.message); loadCategories(); loadProducts(); },
        error:   function(x) { showAlert(x.responseJSON?.error ?? 'Lỗi khi xóa', true); }
    });
});

$('#cat-form').on('submit', function(e) {
    e.preventDefault();
    const id = $('#c-id').val();
    $.ajax({
        url: BASE + '/category' + (id ? '/' + id : ''),
        method: id ? 'PUT' : 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ name: $('#c-name').val(), description: $('#c-desc').val() }),
        success: function(r) { closeCatModal(); showAlert(r.message); loadCategories(); loadCategoryOptions(); },
        error:   function(x) {
            const errs = x.responseJSON?.errors ?? [x.responseJSON?.error ?? 'Lỗi'];
            $('#cat-modal-errors').removeClass('hidden').html(errs.map(e => `<div>• ${e}</div>`).join(''));
        }
    });
});

$('.btn-close-cat, #overlay-cat').on('click', closeCatModal);

// ===== INIT =====
$(document).ready(function() {
    loadProducts();
    loadCategories();
});
</script>
</body>
</html>
