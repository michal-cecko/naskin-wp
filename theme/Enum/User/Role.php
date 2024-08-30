<?php

namespace Theme\Enum\User;

enum Role : string
{
    case ADMIN = 'administrator';
    case EMPLOYEE = 'employee';
    case TOGETHER_EMPLOYEE = 'together_employee';
    case MANAGER = 'manager';
    case OWNER = 'owner';
}
