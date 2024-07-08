<?php

namespace Theme\Models\Other;

use Saurus\App\Modules\Wordpress\Models\Model;

class NameDay extends Model
{
    protected $table = 'name_days';

    protected $fillable = [
        'name',
        'date',
    ];
}