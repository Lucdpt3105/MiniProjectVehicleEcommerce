-- =============================================
-- THÊM HÌNH ẢNH CHO SẢN PHẨM NỔI BẬT
-- Mini Shop - Featured Product Images
-- =============================================

-- Xóa ảnh cũ nếu có (tùy chọn)
-- DELETE FROM product_images;

-- Thêm ảnh cho các sản phẩm nổi bật
-- Lấy 8 sản phẩm đầu tiên trong database

-- Sản phẩm 1 - 1969 Harley Davidson Ultimate Chopper
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_1678', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s', 1
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_1678' AND is_main = 1);

-- Sản phẩm 2 - 1952 Alpine Renault 1300
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_1949', 'https://www.motorious.com/content/images/2020/09/canepa4.jpg', 1
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_1949' AND is_main = 1);

-- Sản phẩm 3 - 1996 Moto Guzzi 1100i
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_2016', 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85', 1
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_2016' AND is_main = 1);

-- Sản phẩm 4 - 2003 Harley-Davidson Eagle Drag Bike
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_4698', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s', 1
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_4698' AND is_main = 1);

-- Sản phẩm 5 - 1972 Alfa Romeo GTA
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_4757', 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod', 1
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_4757' AND is_main = 1);

-- Sản phẩm 6 - 1962 LanciaA Delta 16V
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_4962', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfZvjwe--IdK1SmLuVlJGMIg5tdrtk1M5gSw&s', 1
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_4962' AND is_main = 1);

-- Thêm ảnh phụ cho sản phẩm 1
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_1678', 'https://www.motorious.com/content/images/2020/09/canepa4.jpg', 0
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_1678' AND image_url = 'https://www.motorious.com/content/images/2020/09/canepa4.jpg');

-- Thêm ảnh phụ cho sản phẩm 2
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_1949', 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85', 0
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_1949' AND image_url = 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85');

-- Thêm ảnh phụ cho sản phẩm 3
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 'S10_2016', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s', 0
WHERE NOT EXISTS (SELECT 1 FROM product_images WHERE productCode = 'S10_2016' AND image_url = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s');

-- Kiểm tra kết quả
SELECT p.productCode, p.productName, pi.image_url, pi.is_main
FROM products p
LEFT JOIN product_images pi ON p.productCode = pi.productCode
WHERE p.productCode IN ('S10_1678', 'S10_1949', 'S10_2016', 'S10_4698', 'S10_4757', 'S10_4962')
ORDER BY p.productCode, pi.is_main DESC;

-- Thông báo hoàn thành
SELECT 'Đã thêm ảnh cho sản phẩm nổi bật thành công!' as Message;
