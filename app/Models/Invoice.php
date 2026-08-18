<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
class Invoice extends Model {
    protected $guarded = [];
    protected function casts(): array { return ['due_date' => 'date', 'paid_at' => 'datetime', 'total_amount' => 'decimal:2']; }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
}
