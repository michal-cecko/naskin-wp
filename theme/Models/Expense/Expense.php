<?php

namespace Theme\Models\Expense;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Taxonomies\ExpenseCategory;

class Expense extends Model {
    protected $table = 'expenses';

    protected $fillable = [
        'category_id',
        'description',
        'note',
        'supplier',
        'price',
    ];

    public function category(): BelongsTo {
        return $this->belongsTo(ExpenseCategory::class, "category_id", "term_id", "terms");
    }
}

