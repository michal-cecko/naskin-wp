<?php

namespace Theme\Models\Expense;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Saurus\App\Modules\Log\ILoggable;
use Saurus\App\Modules\Wordpress\Models\Model;
use Theme\Enum\User\Role;
use Theme\PostTypes\Product;
use Theme\Taxonomies\ExpenseCategory;

class Expense extends Model implements ILoggable {
    protected $table = 'expenses';

    protected $fillable = [
        'category_id',
        'product_id',
        'description',
        'note',
        'supplier',
        'price',
        'bought_at',
    ];

    protected $casts = [
        'bought_at' => 'date',
    ];

    public function newQuery() : Builder
    {
        $q = parent::newQuery();

        $user = main()->wpHelper()->getCurrentUser();
        if(!in_array($user->roles[0], [Role::OWNER->value, Role::ADMIN->value])) {
            $q->whereHas('category', function($sq) {
                $sq->whereHas('term', function($ssq) {
                    $ssq->whereHas('meta', function($sssq) {
                        $sssq->where('meta_key', "owner_only")->where('meta_value', 0);
                    })->orWhereDoesntHave('meta', function($sssq) {
                        $sssq->where('meta_key', "owner_only");
                    });
                });
            });
        }

        return $q;
    }

    public function category(): BelongsTo {
        return $this->belongsTo(ExpenseCategory::class, "category_id", "term_id");
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, "product_id", "ID");
    }

    public function getEditLinkAttribute(): ?string
    {
        return admin_url("admin.php?page=expense_detail&id={$this->id}");
    }

    public function getLogStringAttribute(): ?string
    {
        return "#{$this->id} {$this->description} za ({$this->price} €) ". ($this->supplier ? "od dodávateľa {$this->supplier}" : "")  . ($this->bought_at ? " z dňa {$this->bought_at->format("d.m.Y")}." : "") . ($this->note ? " Poznámka: {$this->note}" : "");
    }

    public function getLogLinkAttribute(): ?string
    {
        return $this->edit_link;
    }

    public function getLogTitleAttribute(): string
    {
        return "{$this->description} ({$this->price} €)";
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'product_id' => $this->product_id,
            'description' => $this->description,
            'note' => $this->note,
            'supplier' => $this->supplier,
            'price' => $this->price,
            'bought_at' => $this->bought_at,
        ];
    }
}

