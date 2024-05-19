<?php

namespace Theme\Mappers;

use Saurus\App\Modules\Mapper\Mapper;

class EmployeeMapper extends Mapper {
    public static function toArray($model) : array {
        $data = [];

        $data['id'] = (int) $model->ID;
        $data['first_name'] = $model->first_name;
        $data['profile_picture'] = $model->profile_picture;

        return $data;
    }
}