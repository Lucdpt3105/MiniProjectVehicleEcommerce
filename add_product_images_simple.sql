-- =============================================
-- THÊM HÌNH ẢNH CHO SẢN PHẨM - PHIÊN BẢN ĐƠN GIẢN
-- Mini Shop - Product Images Import (Simple Version)
-- Tương thích với tất cả phiên bản MySQL
-- =============================================

-- BƯỚC 1: THÊM ẢNH CHO PRODUCTLINES (Dòng sản phẩm)
UPDATE productlines SET image = 'https://i.pinimg.com/736x/90/37/61/903761830591b5b8826f2b96d04b042e.jpg' WHERE productLine = 'Classic Cars';
UPDATE productlines SET image = 'https://ultimatemotorcycling.com/wp-content/uploads/2024/09/1969-swap-chopper-sportster-74-mystery-1.jpg' WHERE productLine = 'Motorcycles';
UPDATE productlines SET image = 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod' WHERE productLine = 'Vintage Cars';
UPDATE productlines SET image = 'https://www.carscoops.com/wp-content/uploads/2022/04/1968-dodge-charger-rt-hem.jpg' WHERE productLine = 'Muscle Cars';
UPDATE productlines SET image = 'https://www.sportscarmarket.com/wp-content/uploads/2021/05/1970-plymouth-hemi-cuda-front.jpg' WHERE productLine = 'Sports Cars';
UPDATE productlines SET image = 'https://www.motorious.com/content/images/2020/09/canepa4.jpg' WHERE productLine = 'Luxury Cars';
UPDATE productlines SET image = 'https://cdn.dealeraccelerate.com/saratoga/1/127/2309/790x1024/1999-yamaha-jet-boat' WHERE productLine = 'Boats';
UPDATE productlines SET image = 'https://media.defense.gov/2005/Dec/26/2000574565/1200/1200/0/050317-F-1234P-002.JPG' WHERE productLine = 'Planes';
UPDATE productlines SET image = 'https://www.americanairpowermuseum.com/wp-content/uploads/2025/04/Template-1920-x-1080-px.webp' WHERE productLine = 'Military Vehicles';
UPDATE productlines SET image = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPva-VGZxqiGtHYDtyV87hZe4BYvhZbtSdgQ&s' WHERE productLine = 'Trucks and Buses';
UPDATE productlines SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif/lossy-page1-1200px-Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif.jpg' WHERE productLine = 'Ships';
UPDATE productlines SET image = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s' WHERE productLine = 'Trains';

-- BƯỚC 2: THÊM ẢNH CHÍNH CHO CLASSIC CARS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://i.pinimg.com/736x/90/37/61/903761830591b5b8826f2b96d04b042e.jpg', 1
FROM products WHERE productLine = 'Classic Cars' LIMIT 10;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.thehenryford.org/linkedpub-image/qY8EE1_447shRg1F7LyjmbuscyLUg_WU7vXb3cTQvhfVeusjclNyhN-prpRluxhxAIYU8WihcMex2MhwXPsGOw', 1
FROM products WHERE productLine = 'Classic Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 10;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPva-VGZxqiGtHYDtyV87hZe4BYvhZbtSdgQ&s', 1
FROM products WHERE productLine = 'Classic Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 10;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://i.pinimg.com/736x/39/2d/ac/392dacf452882ca3bb9314f1195fa754.jpg', 1
FROM products WHERE productLine = 'Classic Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 3: THÊM ẢNH CHÍNH CHO MOTORCYCLES
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://ultimatemotorcycling.com/wp-content/uploads/2024/09/1969-swap-chopper-sportster-74-mystery-1.jpg', 1
FROM products WHERE productLine = 'Motorcycles' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s', 1
FROM products WHERE productLine = 'Motorcycles' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTggSniCM8NDwinrPtUf87tHpdidYXBbCViMA&s', 1
FROM products WHERE productLine = 'Motorcycles' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 4: THÊM ẢNH CHÍNH CHO VINTAGE CARS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://scalethumb.leparking.fr/unsafe/331x248/smart/https://cloud.leparking.fr/2020/10/12/00/12/alpine-a110-renault-alpine-a110-original-gordini-1300-va-bleu_7808574403.jpg', 1
FROM products WHERE productLine = 'Vintage Cars' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod', 1
FROM products WHERE productLine = 'Vintage Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://cdn.dealeraccelerate.com/survivor/1/1072/94997/1920x1440/1969-chevrolet-corvair-monza-convertible', 1
FROM products WHERE productLine = 'Vintage Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 5: THÊM ẢNH CHÍNH CHO MUSCLE CARS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.carscoops.com/wp-content/uploads/2022/04/1968-dodge-charger-rt-hem.jpg', 1
FROM products WHERE productLine = 'Muscle Cars' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod', 1
FROM products WHERE productLine = 'Muscle Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 3;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.sportscarmarket.com/wp-content/uploads/2021/05/1970-plymouth-hemi-cuda-front.jpg', 1
FROM products WHERE productLine = 'Muscle Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 6: THÊM ẢNH CHÍNH CHO SPORTS CARS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.sportscarmarket.com/wp-content/uploads/2021/05/1970-plymouth-hemi-cuda-front.jpg', 1
FROM products WHERE productLine = 'Sports Cars' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.motorious.com/content/images/2020/09/canepa4.jpg', 1
FROM products WHERE productLine = 'Sports Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85', 1
FROM products WHERE productLine = 'Sports Cars' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 7: THÊM ẢNH CHÍNH CHO LUXURY CARS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.motorious.com/content/images/2020/09/canepa4.jpg', 1
FROM products WHERE productLine IN ('Luxury Cars', 'Limousines') LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85', 1
FROM products WHERE productLine IN ('Luxury Cars', 'Limousines') AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s', 1
FROM products WHERE productLine IN ('Luxury Cars', 'Limousines') AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 8: THÊM ẢNH CHÍNH CHO BOATS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://cdn.dealeraccelerate.com/saratoga/1/127/2309/790x1024/1999-yamaha-jet-boat', 1
FROM products WHERE productLine = 'Boats' LIMIT 3;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWBMQAlsZRRJzNsnUpCUM7buhPnMK2ra3e2g&s', 1
FROM products WHERE productLine = 'Boats' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 3;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQipPRQbYHVsBrKsXCI0bdfSOub1Fstmp3K-g&s', 1
FROM products WHERE productLine = 'Boats' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 9: THÊM ẢNH CHÍNH CHO PLANES
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://media.defense.gov/2005/Dec/26/2000574565/1200/1200/0/050317-F-1234P-002.JPG', 1
FROM products WHERE productLine = 'Planes' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://www.americanairpowermuseum.com/wp-content/uploads/2025/04/Template-1920-x-1080-px.webp', 1
FROM products WHERE productLine = 'Planes' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSma_QC9BrXX_xV-QyD_DbGx3ooN_AL2Amz-w&s', 1
FROM products WHERE productLine = 'Planes' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://media.defense.gov/2024/Nov/05/2003578295/2000/2000/0/241105-F-IO108-001.JPG', 1
FROM products WHERE productLine = 'Planes' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 10: THÊM ẢNH CHÍNH CHO SHIPS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif/lossy-page1-1200px-Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif.jpg', 1
FROM products WHERE productLine = 'Ships' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNt68vxwjpRnrtJXiIHAuMmayte160e_Jc9g&s', 1
FROM products WHERE productLine = 'Ships' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 11: THÊM ẢNH CHÍNH CHO TRAINS
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s', 1
FROM products WHERE productLine = 'Trains' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfZvjwe--IdK1SmLuVlJGMIg5tdrtk1M5gSw&s', 1
FROM products WHERE productLine = 'Trains' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlOTTCaC8pEpPJexQ17ByHByVuWiqgLsIMrA&s', 1
FROM products WHERE productLine = 'Trains' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 12: THÊM ẢNH CHÍNH CHO TRUCKS AND BUSES
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPva-VGZxqiGtHYDtyV87hZe4BYvhZbtSdgQ&s', 1
FROM products WHERE productLine = 'Trucks and Buses' LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfZvjwe--IdK1SmLuVlJGMIg5tdrtk1M5gSw&s', 1
FROM products WHERE productLine = 'Trucks and Buses' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1) LIMIT 5;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlOTTCaC8pEpPJexQ17ByHByVuWiqgLsIMrA&s', 1
FROM products WHERE productLine = 'Trucks and Buses' AND productCode NOT IN (SELECT productCode FROM product_images WHERE is_main = 1);

-- BƯỚC 13: THÊM ẢNH PHỤ (Ảnh bổ sung cho gallery)
-- Thêm ảnh phụ cho Classic Cars
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT p.productCode, 'https://www.thehenryford.org/linkedpub-image/qY8EE1_447shRg1F7LyjmbuscyLUg_WU7vXb3cTQvhfVeusjclNyhN-prpRluxhxAIYU8WihcMex2MhwXPsGOw', 0
FROM products p
INNER JOIN product_images pi ON p.productCode = pi.productCode
WHERE p.productLine = 'Classic Cars' AND pi.is_main = 1
GROUP BY p.productCode
LIMIT 15;

INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT p.productCode, 'https://i.pinimg.com/736x/39/2d/ac/392dacf452882ca3bb9314f1195fa754.jpg', 0
FROM products p
INNER JOIN product_images pi ON p.productCode = pi.productCode
WHERE p.productLine = 'Classic Cars' AND pi.is_main = 1
GROUP BY p.productCode
LIMIT 10;

-- Thêm ảnh phụ cho Motorcycles
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT p.productCode, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTggSniCM8NDwinrPtUf87tHpdidYXBbCViMA&s', 0
FROM products p
INNER JOIN product_images pi ON p.productCode = pi.productCode
WHERE p.productLine = 'Motorcycles' AND pi.is_main = 1
GROUP BY p.productCode
LIMIT 10;

-- Thêm ảnh phụ cho các dòng khác
INSERT IGNORE INTO product_images (productCode, image_url, is_main)
SELECT p.productCode, 
    CASE 
        WHEN p.productLine LIKE '%Car%' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s'
        WHEN p.productLine = 'Boats' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQipPRQbYHVsBrKsXCI0bdfSOub1Fstmp3K-g&s'
        WHEN p.productLine = 'Planes' THEN 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSma_QC9BrXX_xV-QyD_DbGx3ooN_AL2Amz-w&s'
        ELSE 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNt68vxwjpRnrtJXiIHAuMmayte160e_Jc9g&s'
    END, 0
FROM products p
INNER JOIN product_images pi ON p.productCode = pi.productCode
WHERE pi.is_main = 1
GROUP BY p.productCode
LIMIT 50;

-- =============================================
-- HOÀN THÀNH!
-- =============================================

/*
✅ ĐÃ THÊM:
- Ảnh cho tất cả productlines (dòng sản phẩm)
- Ảnh chính cho tất cả products (phân theo dòng xe phù hợp)
- Ảnh phụ cho khoảng 50+ sản phẩm

📝 CÁCH SỬ DỤNG:
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Chọn database: classicmodels
3. Vào tab SQL
4. Copy toàn bộ script này và paste vào
5. Click "Go" để chạy

⚠️ LƯU Ý:
- Script sử dụng INSERT IGNORE để tránh lỗi duplicate key
- Tự động kiểm tra sản phẩm đã có ảnh chưa
- Phù hợp với MySQL 5.x và cao hơn

🎨 MÔ TẢ:
Script này đơn giản hơn, chạy từng nhóm sản phẩm một,
dễ debug và tương thích với mọi phiên bản MySQL.
*/
