<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Mini Shop'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/mini_shop/assets/css/style.css">
    
    <?php if (isset($extraCSS)): ?>
        <?php echo $extraCSS; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/mini_shop/customer/home.php">
                <i class="fas fa-store"></i> Mini Shop
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/mini_shop/customer/home.php">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/mini_shop/customer/products.php">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/mini_shop/customer/orders.php">
                                <i class="fas fa-receipt"></i> Đơn hàng
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" action="/mini_shop/customer/search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Tìm sản phẩm..." required>
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/mini_shop/customer/cart.php">
                            <i class="fas fa-shopping-cart"></i> Giỏ hàng
                            <?php
                            $cartCount = 0;
                            if (isset($_SESSION['cart_sessionID'])) {
                                $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE sessionID = ?");
                                $stmt->bind_param("s", $_SESSION['cart_sessionID']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                $cartCount = $row['total'] ?? 0;
                            }
                            if ($cartCount > 0):
                            ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cartCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars(getUsername()); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/mini_shop/customer/profile.php">
                                    <i class="fas fa-user-circle"></i> Hồ sơ
                                </a></li>
                                <?php if (isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/mini_shop/admin/dashboard.php">
                                        <i class="fas fa-cog"></i> Admin Panel
                                    </a></li>
                                <?php elseif (isEmployee()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/mini_shop/employee/dashboard.php">
                                        <i class="fas fa-user-tie"></i> Employee Panel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/mini_shop/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/mini_shop/login.php">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/mini_shop/register.php">
                                <i class="fas fa-user-plus"></i> Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="container-fluid py-4">
