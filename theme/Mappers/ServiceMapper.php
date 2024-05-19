<?php

namespace Theme\Mappers;

use Saurus\App\Modules\Mapper\Mapper;
use Theme\Taxonomies\ServiceCategory;
use Theme\Users\Employee;

class ServiceMapper extends Mapper {
    public static function toArray($model) : array {
        $data = [];

        $data['id'] = (int) $model->id;
        $data['title'] = $model->title;
        $data['description'] = $model->description;
        $data['price'] = $model->price;
        $data['duration'] = $model->duration;
        $data['image'] = $model->image;
        $data['employees'] = EmployeeMapper::collection($model->employees);

        return $data;
    }
}