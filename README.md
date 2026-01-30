## Giới thiệu

Hệ thống quản lý thiết bị công ty đường mía Việt Nam - Đài Loan

php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan config:cache

rm -rf public/build/* node_modules package-lock.json

npm install
npx vite build --config vite.config.backend.js --debug
npx vite build --config vite.config.frontend.js


npx vite build --config vite.config.backend.js --debug
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan config:cache

## Nếu chỉ build mỗi cho front end

php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan config:cache

rm -rf public/build/frontend

npx vite build --config vite.config.frontend.js

php artisan optimize:clear


## Xóa cache
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan config:cache
php artisan optimize:clear

php artisan cache:clear && php artisan route:clear && php artisan config:clear && php artisan config:cache && php artisan optimize:clear


## Đồng bộ database khi cập nhật ModuleConst
* php artisan db:seed --class=ModuleSeeder

## Đồng bộ bảng permissions với action mới (Tự động hóa migration cho action mới)
* php artisan permissions:sync-actions

## Cập nhật quyền admin sau khi bổ sung module hoặc action mới
* php artisan db:seed --class=AdminPermissionSeeder

## Chạy Seeder để Áp dụng Quyền Mặc Định: php artisan db:seed --class=RolePermissionSeeder

## Chạy Command để Cập Nhật Quyền Khi HT Phát Triển: 
* Đồng bộ quyền mà không ghi đè dữ liệu hiện có: php artisan permissions:sync
* Ghi đè quyền hiện có (dùng khi muốn reset về mặc định): php artisan permissions:sync --force

## Chỉnh sửa cấu trúc db / Update migrations

 - Chỉnh sửa trong file ./render_migration_file.json
 - Sau khi chỉnh sửa xong chạy lệnh ```php artisan migration:generate``` để build. Kiểm tra các file migration trong database/migrations Các file được tự động tạo trong thư mục ./database/migrations
 - Các file tạo cấu trúc db sẽ tạo trong lệnh trên 
 - Không chinh sửa trong thư mục ./database/migrations
 - Chạy migration: php artisan migrate:fresh --force

## Thiết lập thư mục ngôn ngữ (lang): php artisan lang:publish
## Check kiểm tra key thiếu dịch: php artisan lang:check-missing

## Hợp nhất các file JSON trong thư mục lang với đầu ra là en.json - vi.json: php artisan translations:merge

## Chỉ chạy lệnh này khi muốn trở về git gần nhất trước đó, bỏ qua các thay đổi đang làm hiện tại
 git restore .
 git clean -fd

## Chạy lệnh chèn data country: php artisan import:countries --force --chunk=1000

## Stoage link trên server: ln -s /home/checkvn.vn/asset.checkvn.vn/storage/app/public/ /home/checkvn.vn/asset.checkvn.vn/public/storage