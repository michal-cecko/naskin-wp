<?php

namespace Theme\Enum;

enum AppointmentEmailType: string {

    case CREATED = "created";
    case UPDATED = "updated";
    case CANCELLED = "cancelled";
    case REMIND = "remind";

}