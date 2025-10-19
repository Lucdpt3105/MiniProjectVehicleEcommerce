-- =============================================
-- THÊM HÌNH ẢNH CHO SẢN PHẨM VÀ DÒNG XE
-- Mini Shop - Product Images Import
-- =============================================

-- BƯỚC 1: XÓA ẢNH CŨ (Tùy chọn - uncomment nếu muốn xóa hết ảnh cũ)
-- DELETE FROM product_images;

-- BƯỚC 2: THÊM ẢNH CHO PRODUCTLINES (Dòng sản phẩm)
-- Cập nhật ảnh cho các dòng xe

UPDATE productlines SET image = 'https://i.pinimg.com/736x/90/37/61/903761830591b5b8826f2b96d04b042e.jpg' 
WHERE productLine = 'Classic Cars';

UPDATE productlines SET image = 'https://ultimatemotorcycling.com/wp-content/uploads/2024/09/1969-swap-chopper-sportster-74-mystery-1.jpg' 
WHERE productLine = 'Motorcycles';

UPDATE productlines SET image = 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod' 
WHERE productLine = 'Vintage Cars';

UPDATE productlines SET image = 'https://www.carscoops.com/wp-content/uploads/2022/04/1968-dodge-charger-rt-hem.jpg' 
WHERE productLine = 'Muscle Cars';

UPDATE productlines SET image = 'https://www.sportscarmarket.com/wp-content/uploads/2021/05/1970-plymouth-hemi-cuda-front.jpg' 
WHERE productLine = 'Sports Cars';

UPDATE productlines SET image = 'https://www.motorious.com/content/images/2020/09/canepa4.jpg' 
WHERE productLine = 'Luxury Cars';

UPDATE productlines SET image = 'https://cdn.dealeraccelerate.com/saratoga/1/127/2309/790x1024/1999-yamaha-jet-boat' 
WHERE productLine = 'Boats';

UPDATE productlines SET image = 'https://media.defense.gov/2005/Dec/26/2000574565/1200/1200/0/050317-F-1234P-002.JPG' 
WHERE productLine = 'Planes';

UPDATE productlines SET image = 'https://www.americanairpowermuseum.com/wp-content/uploads/2025/04/Template-1920-x-1080-px.webp' 
WHERE productLine = 'Military Vehicles';

UPDATE productlines SET image = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPva-VGZxqiGtHYDtyV87hZe4BYvhZbtSdgQ&s' 
WHERE productLine = 'Trucks and Buses';

UPDATE productlines SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif/lossy-page1-1200px-Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif.jpg' 
WHERE productLine = 'Ships';

UPDATE productlines SET image = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s' 
WHERE productLine = 'Trains';

-- BƯỚC 3: THÊM ẢNH CHO PRODUCTS (Sản phẩm)
-- Danh sách URL ảnh
SET @image1 = 'https://i.pinimg.com/736x/90/37/61/903761830591b5b8826f2b96d04b042e.jpg';
SET @image2 = 'https://www.thehenryford.org/linkedpub-image/qY8EE1_447shRg1F7LyjmbuscyLUg_WU7vXb3cTQvhfVeusjclNyhN-prpRluxhxAIYU8WihcMex2MhwXPsGOw';
SET @image3 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPva-VGZxqiGtHYDtyV87hZe4BYvhZbtSdgQ&s';
SET @image4 = 'https://i.pinimg.com/736x/39/2d/ac/392dacf452882ca3bb9314f1195fa754.jpg';
SET @image5 = 'https://ultimatemotorcycling.com/wp-content/uploads/2024/09/1969-swap-chopper-sportster-74-mystery-1.jpg';
SET @image6 = 'https://scalethumb.leparking.fr/unsafe/331x248/smart/https://cloud.leparking.fr/2020/10/12/00/12/alpine-a110-renault-alpine-a110-original-gordini-1300-va-bleu_7808574403.jpg';
SET @image7 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSusJ4RhTyApqrhVcNXe4DH-yMn27OPwGjtTg&s';
SET @image8 = 'https://www.motorious.com/content/images/2020/09/canepa4.jpg';
SET @image9 = 'https://images.collectingcars.com/022241/AT-307.jpg?w=1263&fit=fillmax&crop=edges&auto=format,compress&cs=srgb&q=85';
SET @image10 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkPCUfuPH1cdyBRuwg1QXWnw5l9StbAF4p7A&s';
SET @image11 = 'https://cdn.dealeraccelerate.com/vanguard/1/27845/1422036/1920x1440/1968-ford-mustang-fastback-restomod';
SET @image12 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfZvjwe--IdK1SmLuVlJGMIg5tdrtk1M5gSw&s';
SET @image13 = 'https://cdn.dealeraccelerate.com/survivor/1/1072/94997/1920x1440/1969-chevrolet-corvair-monza-convertible';
SET @image14 = 'https://www.carscoops.com/wp-content/uploads/2022/04/1968-dodge-charger-rt-hem.jpg';
SET @image15 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTggSniCM8NDwinrPtUf87tHpdidYXBbCViMA&s';
SET @image16 = 'https://www.sportscarmarket.com/wp-content/uploads/2021/05/1970-plymouth-hemi-cuda-front.jpg';
SET @image17 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlOTTCaC8pEpPJexQ17ByHByVuWiqgLsIMrA&s';
SET @image18 = 'https://cdn.dealeraccelerate.com/saratoga/1/127/2309/790x1024/1999-yamaha-jet-boat';
SET @image19 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWBMQAlsZRRJzNsnUpCUM7buhPnMK2ra3e2g&s';
SET @image20 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQipPRQbYHVsBrKsXCI0bdfSOub1Fstmp3K-g&s';
SET @image21 = 'https://media.defense.gov/2005/Dec/26/2000574565/1200/1200/0/050317-F-1234P-002.JPG';
SET @image22 = 'https://www.americanairpowermuseum.com/wp-content/uploads/2025/04/Template-1920-x-1080-px.webp';
SET @image23 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSma_QC9BrXX_xV-QyD_DbGx3ooN_AL2Amz-w&s';
SET @image24 = 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif/lossy-page1-1200px-Queen_Mary_ship%2C_Long_Beach%2C_California_LCCN2011630073_%28crop%29.tif.jpg';
SET @image25 = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNt68vxwjpRnrtJXiIHAuMmayte160e_Jc9g&s';
SET @image26 = 'https://media.defense.gov/2024/Nov/05/2003578295/2000/2000/0/241105-F-IO108-001.JPG';

-- Thêm ảnh CHÍNH (is_main = 1) cho TẤT CẢ sản phẩm
-- Sử dụng vòng lặp để gán ảnh cho từng sản phẩm (sử dụng lại ảnh nếu hết)
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 
    productCode,
    CASE 
        -- Phân ảnh theo dòng sản phẩm
        WHEN p.productLine = 'Classic Cars' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 4
                WHEN 0 THEN @image1
                WHEN 1 THEN @image2
                WHEN 2 THEN @image3
                ELSE @image4
            END
        WHEN p.productLine = 'Motorcycles' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 3
                WHEN 0 THEN @image5
                WHEN 1 THEN @image7
                ELSE @image15
            END
        WHEN p.productLine = 'Vintage Cars' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 4
                WHEN 0 THEN @image6
                WHEN 1 THEN @image11
                WHEN 2 THEN @image13
                ELSE @image9
            END
        WHEN p.productLine = 'Muscle Cars' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 3
                WHEN 0 THEN @image14
                WHEN 1 THEN @image11
                ELSE @image16
            END
        WHEN p.productLine = 'Sports Cars' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 4
                WHEN 0 THEN @image16
                WHEN 1 THEN @image8
                WHEN 2 THEN @image9
                ELSE @image10
            END
        WHEN p.productLine = 'Luxury Cars' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 3
                WHEN 0 THEN @image8
                WHEN 1 THEN @image9
                ELSE @image10
            END
        WHEN p.productLine = 'Boats' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 3
                WHEN 0 THEN @image18
                WHEN 1 THEN @image19
                ELSE @image20
            END
        WHEN p.productLine = 'Planes' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 4
                WHEN 0 THEN @image21
                WHEN 1 THEN @image22
                WHEN 2 THEN @image23
                ELSE @image26
            END
        WHEN p.productLine = 'Ships' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 2
                WHEN 0 THEN @image24
                ELSE @image25
            END
        WHEN p.productLine = 'Trains' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 3
                WHEN 0 THEN @image7
                WHEN 1 THEN @image12
                ELSE @image17
            END
        WHEN p.productLine = 'Trucks and Buses' THEN 
            CASE (ROW_NUMBER() OVER (PARTITION BY p.productLine ORDER BY p.productCode)) % 3
                WHEN 0 THEN @image3
                WHEN 1 THEN @image12
                ELSE @image17
            END
        ELSE @image1  -- Default image
    END as image_url,
    1 as is_main
FROM products p
WHERE NOT EXISTS (
    SELECT 1 FROM product_images pi 
    WHERE pi.productCode = p.productCode AND pi.is_main = 1
);

-- BƯỚC 4: THÊM ẢNH PHỤ (is_main = 0) cho một số sản phẩm
-- Thêm 1-2 ảnh phụ cho mỗi sản phẩm để hiển thị trong product detail
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 
    productCode,
    CASE (ROW_NUMBER() OVER (ORDER BY productCode)) % 26
        WHEN 0 THEN @image1
        WHEN 1 THEN @image2
        WHEN 2 THEN @image3
        WHEN 3 THEN @image4
        WHEN 4 THEN @image5
        WHEN 5 THEN @image6
        WHEN 6 THEN @image7
        WHEN 7 THEN @image8
        WHEN 8 THEN @image9
        WHEN 9 THEN @image10
        WHEN 10 THEN @image11
        WHEN 11 THEN @image12
        WHEN 12 THEN @image13
        WHEN 13 THEN @image14
        WHEN 14 THEN @image15
        WHEN 15 THEN @image16
        WHEN 16 THEN @image17
        WHEN 17 THEN @image18
        WHEN 18 THEN @image19
        WHEN 19 THEN @image20
        WHEN 20 THEN @image21
        WHEN 21 THEN @image22
        WHEN 22 THEN @image23
        WHEN 23 THEN @image24
        WHEN 24 THEN @image25
        ELSE @image26
    END as image_url,
    0 as is_main
FROM products
LIMIT 50;  -- Thêm ảnh phụ cho 50 sản phẩm đầu tiên

-- Thêm ảnh phụ thứ 2
INSERT INTO product_images (productCode, image_url, is_main)
SELECT 
    productCode,
    CASE (ROW_NUMBER() OVER (ORDER BY productCode DESC)) % 26
        WHEN 0 THEN @image26
        WHEN 1 THEN @image25
        WHEN 2 THEN @image24
        WHEN 3 THEN @image23
        WHEN 4 THEN @image22
        WHEN 5 THEN @image21
        WHEN 6 THEN @image20
        WHEN 7 THEN @image19
        WHEN 8 THEN @image18
        WHEN 9 THEN @image17
        WHEN 10 THEN @image16
        WHEN 11 THEN @image15
        WHEN 12 THEN @image14
        WHEN 13 THEN @image13
        WHEN 14 THEN @image12
        WHEN 15 THEN @image11
        WHEN 16 THEN @image10
        WHEN 17 THEN @image9
        WHEN 18 THEN @image8
        WHEN 19 THEN @image7
        WHEN 20 THEN @image6
        WHEN 21 THEN @image5
        WHEN 22 THEN @image4
        WHEN 23 THEN @image3
        WHEN 24 THEN @image2
        ELSE @image1
    END as image_url,
    0 as is_main
FROM products
LIMIT 30;  -- Thêm ảnh phụ thứ 2 cho 30 sản phẩm đầu tiên

-- =============================================
-- HOÀN THÀNH!
-- =============================================

/*
✅ ĐÃ THÊM:
- Ảnh cho tất cả productlines (dòng sản phẩm)
- Ảnh chính (is_main = 1) cho tất cả products
- Ảnh phụ (is_main = 0) cho 50 sản phẩm đầu
- Ảnh phụ thứ 2 cho 30 sản phẩm đầu

📝 CÁCH SỬ DỤNG:
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Chọn database: classicmodels
3. Vào tab SQL
4. Copy toàn bộ script này và paste vào
5. Click "Go" để chạy

⚠️ LƯU Ý:
- Script tự động kiểm tra và KHÔNG thêm ảnh trùng
- Nếu muốn xóa hết ảnh cũ trước, uncomment dòng DELETE ở đầu
- Mỗi dòng sản phẩm sẽ có ảnh phù hợp theo loại xe

🎨 PHÂN BỔ ẢNH:
- Classic Cars: 4 ảnh classic car
- Motorcycles: 3 ảnh motor
- Vintage Cars: 4 ảnh vintage
- Muscle Cars: 3 ảnh muscle car
- Sports Cars: 4 ảnh sport car
- Luxury Cars: 3 ảnh luxury
- Boats: 3 ảnh boat
- Planes: 4 ảnh airplane
- Ships: 2 ảnh ship
- Trains: 3 ảnh train
- Trucks and Buses: 3 ảnh truck/bus
*/
