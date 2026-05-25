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

<?php if (!empty($errors)): ?>
    <div class="bg-error-container/20 border border-error/40 text-error rounded-lg p-4 mb-6 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/Product/add" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-6">

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
                    <input type="text" name="name" required
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                           placeholder="VD: MacBook Pro M3 Max 14-inch"
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
                    <p class="text-on-surface-variant text-xs mt-1">10 - 100 ký tự</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Giá bán (đ) <span class="text-error">*</span></label>
                        <input type="number" name="price" required min="1" max="999999999999" step="1"
                               value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                               placeholder="VD: 25990000"
                               class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Danh mục <span class="text-error">*</span></label>
                        <select name="category_id" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm">
                            <option value="">-- Chọn danh mục --</option>
                            <?php
                            $selectedCat = $_POST['category_id'] ?? '';
                            foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat->id) ?>"
                                    <?= ((string)$selectedCat === (string)$cat->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Mô tả sản phẩm</label>
                    <textarea name="description" rows="5"
                              placeholder="Mô tả chi tiết tính năng, thông số kỹ thuật..."
                              class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-3 focus:border-primary focus:outline-none transition text-sm resize-none"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: IMAGE + ACTION -->
    <div class="space-y-6">
        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">image</span>
                Ảnh sản phẩm
            </h2>

            <label for="imageInput" class="block">
                <div id="dropZone" class="aspect-square border-2 border-dashed border-outline-variant/40 hover:border-primary rounded-xl flex flex-col items-center justify-center cursor-pointer transition text-on-surface-variant hover:text-primary">
                    <span class="material-symbols-outlined text-5xl mb-2">cloud_upload</span>
                    <p class="font-medium">Nhấn để chọn ảnh</p>
                    <p class="text-xs mt-1">JPG, PNG, GIF, WEBP • Tối đa 5MB</p>
                </div>
                <img id="imgPreview" class="hidden aspect-square w-full rounded-xl object-cover border border-outline-variant/30" alt="">
            </label>
            <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">

            <button type="button" id="removeImg" onclick="clearImage()" class="hidden w-full mt-3 bg-error-container/20 text-error hover:bg-error-container/30 rounded-lg py-2 text-sm font-medium transition flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-base">delete</span>
                Xóa ảnh
            </button>
        </div>

        <div class="bg-surface-container rounded-xl border border-outline-variant/20 p-6 space-y-3">
            <button type="submit" class="w-full bg-primary-container hover:bg-primary-container/90 text-white rounded-lg py-3 font-semibold flex items-center justify-center gap-2 transition glow-btn">
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
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('dropZone').classList.add('hidden');
        const preview = document.getElementById('imgPreview');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        document.getElementById('removeImg').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});

function clearImage() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imgPreview').classList.add('hidden');
    document.getElementById('imgPreview').src = '';
    document.getElementById('removeImg').classList.add('hidden');
    document.getElementById('dropZone').classList.remove('hidden');
}
</script>

<?php include 'app/views/layouts/admin_footer.php'; ?>
