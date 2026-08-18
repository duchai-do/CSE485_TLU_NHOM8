

# 25. Quy trình test nhanh sau khi clone

Có thể chạy lần lượt:

```powershell
composer install --prefer-dist

Copy-Item .env.example .env
php artisan key:generate

npm install

php artisan optimize:clear

php artisan migrate:fresh --seed

php artisan route:list
php artisan route:list --path=member3

php artisan test

composer run dev
```

Sau đó mở:

```text
http://127.0.0.1:8000/member3/registrations
http://127.0.0.1:8000/member3/allocations
http://127.0.0.1:8000/member3/contracts
http://127.0.0.1:8000/member3/violations
```

```powershell
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
php artisan serve
```
