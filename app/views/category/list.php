<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-1"><i class="fa-solid fa-tags me-2"></i>Quản lý danh mục</h2>
                <small class="text-muted">Tổng: <?= isset($categories) ? count($categories) : 0 ?> danh mục hiện có</small>
            </div>
            <div class="d-flex gap-2">
                <a href="/" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fa-solid fa-house me-1"></i>Trang chủ
                </a>
                <a href="/Product/list" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <i class="fa-solid fa-boxes-stacked me-1"></i>Quản lý SP
                </a>
                <a href="/Category/add" class="btn btn-success d-flex align-items-center">
                    <i class="fa-solid fa-plus me-1"></i>Thêm danh mục mới
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 80px">ID</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th class="text-center" style="width: 150px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td class="text-muted fw-semibold">#<?= htmlspecialchars($category->id) ?></td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                                <?= htmlspecialchars($category->name) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <?= htmlspecialchars($category->description ?? 'Không có mô tả.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="/Category/edit/<?= $category->id ?>"
                                               class="btn btn-sm btn-warning me-1 text-white"
                                               title="Sửa danh mục">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="/Category/delete/<?= $category->id ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Xóa danh mục này sẽ xóa luôn các sản phẩm thuộc danh mục (ON DELETE CASCADE). Bạn chắc chắn muốn xóa?')"
                                               title="Xóa danh mục">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-folder-open fa-3x mb-3 d-block text-secondary"></i>
                                        Chưa có danh mục nào được khởi tạo.
                                        <a href="/Category/add" class="text-success fw-semibold d-block mt-2">Tạo danh mục đầu tiên ngay!</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
