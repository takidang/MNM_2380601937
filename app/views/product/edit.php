<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .img-drop-zone {
            height: 200px;
            border: 2px dashed #ced4da;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            color: #adb5bd;
            transition: border-color 0.2s, background 0.2s;
        }
        .img-drop-zone:hover { border-color: #ffc107; background: #fffdf0; color: #856404; }
        .img-preview { width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid #dee2e6; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh sửa sản phẩm #<?= $product->getID() ?></h4>
                        <a href="/Product/list" class="btn btn-dark btn-sm">
                            <i class="fa-solid fa-list me-1"></i>Danh sách
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="/Product/edit/<?= $product->getID() ?>" method="POST" enctype="multipart/form-data">

                            <!-- Ảnh sản phẩm -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Ảnh sản phẩm</label>
                                <?php if ($product->getImage()): ?>
                                    <div class="mb-2">
                                        <img id="imgPreview"
                                             src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                                             class="img-preview" alt="Ảnh sản phẩm hiện tại">
                                    </div>
                                    <div id="dropZone" class="img-drop-zone d-none" onclick="document.getElementById('imageInput').click()">
                                <?php else: ?>
                                    <img id="imgPreview" class="img-preview d-none" alt="Xem trước ảnh">
                                    <div id="dropZone" class="img-drop-zone" onclick="document.getElementById('imageInput').click()">
                                <?php endif; ?>
                                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2"></i>
                                        <span class="fw-semibold">Nhấn để chọn ảnh mới</span>
                                        <small>JPG, PNG, GIF, WEBP &bull; Tối đa 5MB</small>
                                    </div>
                                <input type="file" id="imageInput" name="image" accept="image/*" class="d-none">
                                <div class="mt-2 d-flex gap-2">
                                    <?php if ($product->getImage()): ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="document.getElementById('imageInput').click()">
                                            <i class="fa-solid fa-arrow-up-from-bracket me-1"></i>Đổi ảnh
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" id="removeImg" class="btn btn-sm btn-outline-danger <?= $product->getImage() ? '' : 'd-none' ?>"
                                            onclick="showDropZone()">
                                        <i class="fa-solid fa-trash me-1"></i>Xóa ảnh
                                    </button>
                                </div>
                            </div>

                            <!-- Tên sản phẩm -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= htmlspecialchars($product->getName()) ?>" required>
                            </div>

                            <!-- Giá & Danh mục -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Giá sản phẩm <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="price" class="form-control"
                                               value="<?= htmlspecialchars($product->getPrice()) ?>" min="1" step="0.01" required>
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php
                                        $currentCat = $product->getCategory();
                                        if (!empty($categories)):
                                            foreach ($categories as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat->id) ?>"
                                                    <?= ((string)$currentCat === (string)$cat->id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat->name) ?>
                                                </option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Mô tả -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Mô tả sản phẩm</label>
                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product->getDescription() ?? '') ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>Cập nhật
                                </button>
                                <a href="/Product/list" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('imageInput').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('dropZone').classList.add('d-none');
                const preview = document.getElementById('imgPreview');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                document.getElementById('removeImg').classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });

        function showDropZone() {
            document.getElementById('imageInput').value = '';
            document.getElementById('imgPreview').classList.add('d-none');
            document.getElementById('imgPreview').src = '';
            document.getElementById('removeImg').classList.add('d-none');
            document.getElementById('dropZone').classList.remove('d-none');
        }
    </script>
</body>
</html>
