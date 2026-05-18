<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopHUTECH - Mua sắm thông minh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e53935;
            --primary-dark: #b71c1c;
            --accent: #ff7043;
        }

        /* ===== NAVBAR ===== */
        .navbar-brand .brand-name { font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px; }
        .navbar-brand .brand-dot  { color: var(--accent); }
        .nav-search .form-control { border-radius: 20px 0 0 20px; border-right: none; }
        .nav-search .btn          { border-radius: 0 20px 20px 0; background: var(--primary); border: none; }
        .cart-badge { position: relative; }
        .cart-badge .badge { position: absolute; top: -6px; right: -8px; font-size: .65rem; }
        
        /* Dropdown Custom Style */
        .dropdown-menu-dark .dropdown-item:hover { background: var(--primary); color: #fff; }

        /* ===== BANNER ===== */
        .banner-slide { height: 380px; display: flex; align-items: center; justify-content: flex-start; padding: 0 60px; color: #fff; }
        .banner-slide h1 { font-size: 2.5rem; font-weight: 800; line-height: 1.2; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        .banner-slide p  { font-size: 1.1rem; opacity: .9; }
        .banner-1 { background: linear-gradient(135deg, #1a237e 0%, #283593 40%, #3949ab 100%); }
        .banner-2 { background: linear-gradient(135deg, #b71c1c 0%, #c62828 40%, #e53935 100%); }
        .banner-3 { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 40%, #43a047 100%); }
        .carousel-control-prev-icon,
        .carousel-control-next-icon { filter: drop-shadow(0 0 4px rgba(0,0,0,.5)); }
        .carousel-indicators [data-bs-target] { width: 10px; height: 10px; border-radius: 50%; }

        /* ===== CATEGORY BUTTONS ===== */
        .cat-btn {
            border-radius: 25px;
            padding: 8px 22px;
            font-weight: 600;
            font-size: .875rem;
            transition: all .25s;
            border: 2px solid #dee2e6;
            background: #fff;
            color: #495057;
        }
        .cat-btn:hover, .cat-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229,57,53,.35);
        }

        /* ===== PRODUCT CARD ===== */
        .product-card {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            transition: transform .2s, box-shadow .2s;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,.12);
        }
        .product-img-wrap {
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-img-wrap img  { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
        .product-card:hover .product-img-wrap img { transform: scale(1.05); }
        .product-img-wrap .no-img-icon { font-size: 3rem; color: #ced4da; }
        .product-price { color: var(--primary); font-size: 1.1rem; font-weight: 700; }
        .product-old-price { color: #adb5bd; font-size: .85rem; text-decoration: line-through; }
        .badge-cat { font-size: .7rem; }
        .btn-cart {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            transition: background .2s;
        }
        .btn-cart:hover { background: var(--primary-dark); color: #fff; }
        .btn-detail { border-radius: 8px; }

        /* ===== SECTION TITLES ===== */
        .section-title { font-weight: 800; font-size: 1.35rem; position: relative; padding-bottom: 10px; }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 48px; height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* ===== PROMO BARS ===== */
        .promo-bar { background: linear-gradient(135deg, #fff3e0, #ffe0b2); border-radius: 14px; border-left: 5px solid var(--accent); }
        .promo-bar2 { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-radius: 14px; border-left: 5px solid #43a047; }

        /* ===== FOOTER ===== */
        footer { background: #1a1a2e; color: #aaa; }
        footer h6 { color: #fff; font-weight: 700; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: #fff; }
        .footer-brand { font-size: 1.6rem; font-weight: 900; color: #fff; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background:#1a1a2e;">
    <div class="container">
        <a class="navbar-brand" href="#">
            <span class="brand-name">Shop<span class="brand-dot">HUTECH</span></span>
        </a>

        <div class="nav-search d-none d-md-flex flex-grow-1 mx-4">
            <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
            <button class="btn text-white px-3"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown d-none d-md-block">
                <a class="text-white text-decoration-none dropdown-toggle d-flex align-items-center gap-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-gear"></i>
                    <span class="small">Quản lý</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-secondary">
                    <li>
                        <a class="dropdown-item small py-2" href="/Product/list">
                            <i class="fa-solid fa-cubes-stacked me-2 text-warning"></i>Quản lý Sản phẩm
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small py-2" href="/Category/list">
                            <i class="fa-solid fa-tags me-2 text-info"></i>Quản lý Danh mục
                        </a>
                    </li>
                </ul>
            </div>

            <a href="/Product/add" class="btn btn-sm text-white" style="background:var(--primary);">
                <i class="fa-solid fa-plus me-1"></i>Thêm SP
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navMenu">
            <div class="nav-search d-flex d-md-none mt-3 mb-2 flex-grow-1">
                <input type="text" class="form-control" placeholder="Tìm kiếm...">
                <button class="btn text-white px-3" style="background:var(--primary);"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            
            <ul class="navbar-nav d-md-none border-top border-secondary pt-2 gap-2">
                <li class="nav-item">
                    <a class="nav-link small" href="/Product/list">
                        <i class="fa-solid fa-cubes-stacked me-2 text-warning"></i>Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link small" href="/Category/list">
                        <i class="fa-solid fa-tags me-2 text-info"></i>Quản lý Danh mục
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="heroBanner" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="banner-slide banner-1">
                <div>
                    <p class="mb-2 small text-uppercase opacity-75 fw-semibold">🔥 Ưu đãi hôm nay</p>
                    <h1>Siêu Sale<br>Điện Thoại</h1>
                    <p>Giảm đến <strong>50%</strong> cho tất cả điện thoại thông minh</p>
                    <a href="#products" class="btn btn-light fw-bold px-4 mt-2 rounded-pill">Mua ngay</a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-slide banner-2">
                <div>
                    <p class="mb-2 small text-uppercase opacity-75 fw-semibold">💻 Laptop mới về</p>
                    <h1>Laptop Gaming<br>Cấu Hình Cao</h1>
                    <p>Hiệu năng vượt trội, thiết kế siêu mỏng</p>
                    <a href="#products" class="btn btn-light fw-bold px-4 mt-2 rounded-pill">Khám phá</a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="banner-slide banner-3">
                <div>
                    <p class="mb-2 small text-uppercase opacity-75 fw-semibold">🎧 Phụ kiện</p>
                    <h1>Phụ Kiện Chính Hãng<br>Giá Tốt Nhất</h1>
                    <p>Tai nghe, ốp lưng, sạc dự phòng và nhiều hơn nữa</p>
                    <a href="#products" class="btn btn-light fw-bold px-4 mt-2 rounded-pill">Xem ngay</a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroBanner" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroBanner" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<div class="bg-white border-bottom py-2">
    <div class="container">
        <div class="row text-center g-0">
            <div class="col-6 col-md-3 py-1 border-end">
                <i class="fa-solid fa-truck-fast text-primary me-1"></i>
                <small class="fw-semibold">Miễn phí vận chuyển</small>
            </div>
            <div class="col-6 col-md-3 py-1 border-end">
                <i class="fa-solid fa-shield-halved text-success me-1"></i>
                <small class="fw-semibold">Bảo hành chính hãng</small>
            </div>
            <div class="col-6 col-md-3 py-1 border-end">
                <i class="fa-solid fa-rotate-left text-warning me-1"></i>
                <small class="fw-semibold">Đổi trả 30 ngày</small>
            </div>
            <div class="col-6 col-md-3 py-1">
                <i class="fa-solid fa-headset text-danger me-1"></i>
                <small class="fw-semibold">Hỗ trợ 24/7</small>
            </div>
        </div>
    </div>
</div>

<div class="container py-4" id="products">

    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <button class="cat-btn active" data-cat="all">
            <i class="fa-solid fa-border-all me-1"></i>Tất cả
        </button>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $catItem): ?>
                <button class="cat-btn" data-cat="<?= htmlspecialchars($catItem->id) ?>">
                    <?= htmlspecialchars($catItem->name) ?>
                </button>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="section-title mb-0">
            <i class="fa-solid fa-fire text-danger me-2"></i>Sản Phẩm Nổi Bật
        </h5>
        <a href="/Product/add" class="btn btn-sm btn-outline-danger rounded-pill">
            <i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm
        </a>
    </div>

    <?php if (!empty($products)): ?>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3" id="productGrid">
            <?php foreach ($products as $product): ?>
                <?php $catId = $product->getCategory() ?: 'other'; ?>
                <div class="col product-item" data-cat="<?= htmlspecialchars($catId) ?>">
                    <div class="card h-100 product-card shadow-sm">
                        <div class="product-img-wrap">
                            <?php if ($product->getImage()): ?>
                                <img src="/public/images/products/<?= htmlspecialchars($product->getImage()) ?>"
                                     alt="<?= htmlspecialchars($product->getName()) ?>">
                            <?php else: ?>
                                <i class="fa-solid fa-image no-img-icon"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <span class="badge bg-secondary badge-cat mb-2 align-self-start">
                                <?= htmlspecialchars($product->getCategoryName() ?: 'Khác') ?>
                            </span>
                            <h6 class="card-title fw-semibold mb-1" style="font-size:.9rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= htmlspecialchars($product->getName()) ?>
                            </h6>
                            <p class="text-muted small mb-2" style="display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= htmlspecialchars($product->getDescription()) ?>
                            </p>
                            <div class="mt-auto">
                                <div class="product-price mb-2">
                                    <?= number_format($product->getPrice(), 0, ',', '.') ?> đ
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="/Product/edit/<?= $product->getID() ?>"
                                       class="btn btn-outline-secondary btn-sm btn-detail flex-grow-1">
                                        <i class="fa-solid fa-pen fa-sm me-1"></i>Sửa
                                    </a>
                                    <button class="btn btn-cart btn-sm px-3">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-3 shadow-sm border">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3 d-block"></i>
            <p class="text-muted mb-2">Hệ thống chưa có sản phẩm nào đăng bán.</p>
            <a href="/Product/add" class="btn btn-sm btn-success rounded-pill">
                <i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm đầu tiên
            </a>
        </div>
    <?php endif; ?>

    <div id="noResults" class="text-center py-5 d-none">
        <i class="fa-solid fa-box-open fa-3x text-muted mb-3 d-block"></i>
        <p class="text-muted">Không có sản phẩm nào trong danh mục này.</p>
    </div>

    <div class="row g-3 mt-4">
        <div class="col-md-6">
            <div class="promo-bar p-4 d-flex align-items-center gap-3">
                <i class="fa-solid fa-bolt fa-2x text-warning"></i>
                <div>
                    <div class="fw-bold">Flash Sale mỗi ngày</div>
                    <small class="text-muted">Ưu đãi giảm đến 70% — số lượng có hạn!</small>
                </div>
                <a href="#" class="btn btn-sm btn-warning ms-auto rounded-pill fw-semibold">Xem ngay</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="promo-bar2 p-4 d-flex align-items-center gap-3">
                <i class="fa-solid fa-award fa-2x text-success"></i>
                <div>
                    <div class="fw-bold">Hàng chính hãng 100%</div>
                    <small class="text-muted">Bảo hành theo tiêu chuẩn nhà sản xuất</small>
                </div>
                <a href="#" class="btn btn-sm btn-success ms-auto rounded-pill fw-semibold">Tìm hiểu</a>
            </div>
        </div>
    </div>

</div>

<footer class="mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-brand mb-2">Shop<span style="color:var(--accent)">HUTECH</span></div>
                <p style="font-size:.9rem;">Hệ thống bán lẻ điện tử uy tín. Chất lượng chính hãng, giá cả cạnh tranh.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#"><i class="fa-brands fa-facebook fa-lg"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok fa-lg"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube fa-lg"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram fa-lg"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="mb-3">Danh mục</h6>
                <ul class="list-unstyled" style="font-size:.9rem;">
                    <li class="mb-1"><a href="#">Điện thoại</a></li>
                    <li class="mb-1"><a href="#">Laptop</a></li>
                    <li class="mb-1"><a href="#">Máy tính bảng</a></li>
                    <li class="mb-1"><a href="#">Phụ kiện</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="mb-3">Hỗ trợ</h6>
                <ul class="list-unstyled" style="font-size:.9rem;">
                    <li class="mb-1"><a href="#">Chính sách đổi trả</a></li>
                    <li class="mb-1"><a href="#">Bảo hành</a></li>
                    <li class="mb-1"><a href="#">Hướng dẫn mua hàng</a></li>
                    <li class="mb-1"><a href="#">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="mb-3">Liên hệ</h6>
                <p style="font-size:.9rem;">
                    <i class="fa-solid fa-location-dot me-2 text-danger"></i>475A Điện Biên Phủ, P.25, Q. Bình Thạnh, TP.HCM<br>
                    <i class="fa-solid fa-phone me-2 text-success mt-2"></i>1800 1234<br>
                    <i class="fa-solid fa-envelope me-2 text-info mt-2"></i>support@shophutech.vn
                </p>
            </div>
        </div>
        <hr style="border-color:#333;">
        <div class="text-center" style="font-size:.85rem;">
            &copy; 2025 ShopHUTECH. Được xây dựng với ❤️ bởi sinh viên HUTECH.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Front-end categories sorting
    const catBtns = document.querySelectorAll('.cat-btn');
    const items   = document.querySelectorAll('.product-item');
    const noRes   = document.getElementById('noResults');

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cat = btn.dataset.cat;
            let visible = 0;

            items.forEach(item => {
                const match = cat === 'all' || item.dataset.cat === cat;
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            noRes.classList.toggle('d-none', visible > 0);
        });
    });

    // Add to cart visual effect
    document.querySelectorAll('.btn-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            this.innerHTML = '<i class="fa-solid fa-check"></i>';
            this.style.background = '#43a047';
            setTimeout(() => {
                this.innerHTML = '<i class="fa-solid fa-cart-plus"></i>';
                this.style.background = '';
            }, 1200);
        });
    });
</script>
</body>
</html>