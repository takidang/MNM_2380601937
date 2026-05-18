<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm danh mục mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0"><i class="fa-solid fa-plus-circle me-2"></i>Thêm danh mục mới</h4>
                        <a href="/Category/list" class="btn btn-light btn-sm">
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

                        <form action="/Category/add" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                                       placeholder="Nhập tên danh mục" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="3"
                                          placeholder="Mô tả ngắn về danh mục..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>Lưu danh mục
                                </button>
                                <a href="/Category/list" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i>Hủy bỏ
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
