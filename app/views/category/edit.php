<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh sửa danh mục #<?= htmlspecialchars($category->id) ?></h4>
                        <a href="/Category/list" class="btn btn-dark btn-sm">
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

                        <form action="/Category/edit/<?= htmlspecialchars($category->id) ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= htmlspecialchars($category->name) ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category->description ?? '') ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>Cập nhật
                                </button>
                                <a href="/Category/list" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
