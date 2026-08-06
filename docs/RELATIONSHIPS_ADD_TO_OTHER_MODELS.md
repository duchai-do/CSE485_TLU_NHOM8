# Relationship cần thêm vào Model của thành viên khác

Không copy đè toàn bộ model của thành viên khác. Chỉ thêm các hàm tương ứng.

## `Student.php`

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function allocations(): HasMany
{
    return $this->hasMany(Allocation::class);
}

public function violationRecords(): HasMany
{
    return $this->hasMany(ViolationRecord::class);
}
```

## `RoomRegistration.php`

```php
use Illuminate\Database\Eloquent\Relations\HasOne;

public function allocation(): HasOne
{
    return $this->hasOne(Allocation::class, 'registration_id');
}
```

## `Bed.php`

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function allocations(): HasMany
{
    return $this->hasMany(Allocation::class);
}
```

## `User.php`

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function createdAllocations(): HasMany
{
    return $this->hasMany(Allocation::class, 'allocated_by');
}

public function recordedViolations(): HasMany
{
    return $this->hasMany(ViolationRecord::class, 'recorded_by');
}
```
