<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-thumb {
            width: 60px; height: 60px; object-fit: cover;
            border-radius: 8px; border: 1px solid #dee2e6;
        }
        .no-img {
            width: 60px; height: 60px; border-radius: 8px;
            background: #f1f3f5; display: flex; align-items: center;
            justify-content: center; color: #adb5bd;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-1"><i class="fa-solid fa-boxes-stacked me-2"></i>Quản lý sản phẩm</h2>
                <small class="text-muted">Tổng: <?= isset($products) ? count($products) : 0 ?> sản phẩm hiện có</small>
            </div>
            <div class="d-flex gap-2">
                <a href="/" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                    <i class="fa-solid fa-house me-1"></i>Trang chủ
                </a>
                <a href="/Category/list" class="btn btn-outline-info btn-sm d-flex align-items-center">
                    <i class="fa-solid fa-tags me-1"></i>Quản lý danh mục
                </a>
                <a href="/Product/add" class="btn btn-success d-flex align-items-center">
                    <i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm mới
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 70px">ID</th>
                                <th style="width: 90px">Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th class="text-end">Giá</th>
                                <th>Mô tả</th>
                                <th class="text-center" style="width: 150px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="text-muted fw-semibold">#<?= $product->getID() ?></td>
                                        <td>
                                            <?php if ($product->getImage()): ?>
                                                <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                                                     class="product-thumb" alt="<?= htmlspecialchars($product->getName()) ?>">
                                            <?php else: ?>
                                                <div class="no-img"><i class="fa-solid fa-image"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold"><?= htmlspecialchars($product->getName()) ?></td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                                                <?= htmlspecialchars($product->getCategoryName() ?: 'Khác') ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-danger fw-bold">
                                            <?= number_format($product->getPrice(), 0, ',', '.') ?> đ
                                        </td>
                                        <td class="text-muted small" style="max-width: 250px;">
                                            <?= htmlspecialchars(mb_strimwidth($product->getDescription() ?? '', 0, 80, '...')) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="/Product/edit/<?= $product->getID() ?>"
                                               class="btn btn-sm btn-warning me-1 text-white"
                                               title="Sửa sản phẩm">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="/Product/delete/<?= $product->getID() ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')"
                                               title="Xóa sản phẩm">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-box-open fa-3x mb-3 d-block text-secondary"></i>
                                        Chưa có sản phẩm nào.
                                        <a href="/Product/add" class="text-success fw-semibold d-block mt-2">Thêm sản phẩm đầu tiên!</a>
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
