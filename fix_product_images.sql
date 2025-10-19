-- =============================================
-- SỬA LỖI VÀ CẬP NHẬT HÌNH ẢNH SẢN PHẨM
-- Mini Shop - Fix Product Images
-- =============================================

-- BƯỚC 1: DỌN DẸP CÁC ẢNH CHÍNH TRÙNG LẶP
-- Giữ lại ảnh có imageID nhỏ nhất, xóa các ảnh chính còn lại

-- Tìm các sản phẩm có nhiều hơn 1 ảnh chính
CREATE TEMPORARY TABLE temp_duplicate_main_images AS
SELECT productCode, MIN(imageID) as keep_imageID
FROM product_images
WHERE is_main = 1
GROUP BY productCode
HAVING COUNT(*) > 1;

-- Cập nhật các ảnh chính trùng lặp thành ảnh phụ (trừ ảnh được giữ lại)
UPDATE product_images pi
INNER JOIN temp_duplicate_main_images td ON pi.productCode = td.productCode
SET pi.is_main = 0
WHERE pi.is_main = 1 AND pi.imageID != td.keep_imageID;

DROP TEMPORARY TABLE temp_duplicate_main_images;

-- BƯỚC 2: CẬP NHẬT ẢNH ĐẸP CHO CÁC SẢN PHẨM
-- Danh sách ảnh đẹp từ bạn cung cấp
SET @img1 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s';
SET @img2 = 'https://www.motorious.com/content/images/2020/09/canepa4.jpg';
SET @img3 = 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85';
SET @img4 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s';
SET @img5 = 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod';
SET @img6 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfZvjwe--IdK1SmLuVlJGMIg5tdrtk1M5gSw&s';

-- BƯỚC 3: CẬP NHẬT ẢNH CHO CÁC DÒNG SẢN PHẨM CỤ THỂ

-- Classic Cars (Xe cổ điển)
UPDATE product_images pi
INNER JOIN products p ON pi.productCode = p.productCode
SET pi.image_url = @img2
WHERE p.productLine = 'Classic Cars' 
AND pi.is_main = 1 
AND p.productCode IN (
    SELECT productCode FROM (
        SELECT p2.productCode 
        FROM products p2 
        WHERE p2.productLine = 'Classic Cars' 
        AND p2.quantityInStock > 0
        ORDER BY RAND()
        LIMIT 15
    ) as temp
);

-- Motorcycles (Xe máy)
UPDATE product_images pi
INNER JOIN products p ON pi.productCode = p.productCode
SET pi.image_url = @img1
WHERE p.productLine = 'Motorcycles' 
AND pi.is_main = 1 
AND p.productCode IN (
    SELECT productCode FROM (
        SELECT p2.productCode 
        FROM products p2 
        WHERE p2.productLine = 'Motorcycles' 
        AND p2.quantityInStock > 0
        ORDER BY RAND()
        LIMIT 10
    ) as temp
);

-- Vintage Cars (Xe vintage)
UPDATE product_images pi
INNER JOIN products p ON pi.productCode = p.productCode
SET pi.image_url = @img5
WHERE p.productLine = 'Vintage Cars' 
AND pi.is_main = 1 
AND p.productCode IN (
    SELECT productCode FROM (
        SELECT p2.productCode 
        FROM products p2 
        WHERE p2.productLine = 'Vintage Cars' 
        AND p2.quantityInStock > 0
        ORDER BY RAND()
        LIMIT 10
    ) as temp
);

-- Sports Cars (Xe thể thao)
UPDATE product_images pi
INNER JOIN products p ON pi.productCode = p.productCode
SET pi.image_url = @img3
WHERE p.productLine IN ('Sports Cars', 'Luxury Cars')
AND pi.is_main = 1 
AND p.productCode IN (
    SELECT productCode FROM (
        SELECT p2.productCode 
        FROM products p2 
        WHERE p2.productLine IN ('Sports Cars', 'Luxury Cars')
        AND p2.quantityInStock > 0
        ORDER BY RAND()
        LIMIT 15
    ) as temp
);

-- Muscle Cars (Xe cơ bắp)
UPDATE product_images pi
INNER JOIN products p ON pi.productCode = p.productCode
SET pi.image_url = @img4
WHERE p.productLine = 'Muscle Cars' 
AND pi.is_main = 1 
AND p.productCode IN (
    SELECT productCode FROM (
        SELECT p2.productCode 
        FROM products p2 
        WHERE p2.productLine = 'Muscle Cars' 
        AND p2.quantityInStock > 0
        ORDER BY RAND()
        LIMIT 10
    ) as temp
);

-- Trucks and Buses
UPDATE product_images pi
INNER JOIN products p ON pi.productCode = p.productCode
SET pi.image_url = @img6
WHERE p.productLine = 'Trucks and Buses' 
AND pi.is_main = 1 
AND p.productCode IN (
    SELECT productCode FROM (
        SELECT p2.productCode 
        FROM products p2 
        WHERE p2.productLine = 'Trucks and Buses' 
        AND p2.quantityInStock > 0
        ORDER BY RAND()
        LIMIT 10
    ) as temp
);

-- BƯỚC 4: THÊM ẢNH PHỤ CHO CÁC SẢN PHẨM CHƯA ĐỦ ẢNH
-- Thêm ảnh phụ cho các sản phẩm có ít hơn 3 ảnh

INSERT INTO product_images (productCode, image_url, is_main)
SELECT p.productCode, @img2, 0
FROM products p
WHERE p.quantityInStock > 0
AND (SELECT COUNT(*) FROM product_images pi WHERE pi.productCode = p.productCode) < 3
AND NOT EXISTS (SELECT 1 FROM product_images pi2 WHERE pi2.productCode = p.productCode AND pi2.image_url = @img2)
LIMIT 20;

INSERT INTO product_images (productCode, image_url, is_main)
SELECT p.productCode, @img3, 0
FROM products p
WHERE p.quantityInStock > 0
AND (SELECT COUNT(*) FROM product_images pi WHERE pi.productCode = p.productCode) < 3
AND NOT EXISTS (SELECT 1 FROM product_images pi2 WHERE pi2.productCode = p.productCode AND pi2.image_url = @img3)
LIMIT 20;

-- BƯỚC 5: KIỂM TRA KẾT QUẢ

-- Kiểm tra sản phẩm có nhiều hơn 1 ảnh chính
SELECT '=== SẢN PHẨM CÓ NHIỀU HƠN 1 ẢNH CHÍNH ===' as Status;
SELECT productCode, COUNT(*) as main_image_count 
FROM product_images 
WHERE is_main = 1 
GROUP BY productCode 
HAVING main_image_count > 1;

-- Kiểm tra sản phẩm không có ảnh chính
SELECT '=== SẢN PHẨM KHÔNG CÓ ẢNH CHÍNH ===' as Status;
SELECT p.productCode, p.productName 
FROM products p 
WHERE p.quantityInStock > 0 
AND NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.productCode = p.productCode AND pi.is_main = 1)
LIMIT 10;

-- Thống kê tổng quan
SELECT '=== THỐNG KÊ TỔNG QUAN ===' as Status;
SELECT 
    (SELECT COUNT(DISTINCT productCode) FROM products WHERE quantityInStock > 0) as total_products,
    (SELECT COUNT(DISTINCT productCode) FROM product_images WHERE is_main = 1) as products_with_main_image,
    (SELECT COUNT(*) FROM product_images) as total_images,
    (SELECT AVG(img_count) FROM (SELECT COUNT(*) as img_count FROM product_images GROUP BY productCode) as t) as avg_images_per_product;

SELECT '✅ ĐÃ HOÀN THÀNH SỬA LỖI VÀ CẬP NHẬT ẢNH!' as Message;
