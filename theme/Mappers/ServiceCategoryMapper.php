<?php

namespace Theme\Mappers;

use Saurus\App\Modules\Mapper\Mapper;
use Theme\Users\Employee;

class ServiceCategoryMapper extends Mapper {
    public static function toArray($model) : array {
        $data = [];

        $data['id'] = (int) $model->term_id;
        $data['name'] = $model->term->name;
        $data['slug'] = $model->term->slug;
        $data['image'] = $model->image;
        $data['shortDesc'] = $model->shortDesc;
        $data['services'] = ServiceMapper::collection($model->publishedPostsPluginOrdered->filter(fn($category) => !$category->is_hidden_from_website));

        return $data;
    }
}