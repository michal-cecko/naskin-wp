<?php

namespace Theme\Mappers;

use Saurus\App\Modules\Mapper\Mapper;
use Theme\Users\Employee;

class ServiceCategoryMapper extends Mapper {
    public static function toArray($model) : array {
        $data = [];

        $data['id'] = (int) $model->term_id;
        $data['name'] = $model->term->name;
        $data['image'] = $model->image;
        $data['services'] = ServiceMapper::collection($model->publishedPosts);

        return $data;
    }
}