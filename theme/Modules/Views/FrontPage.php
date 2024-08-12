<?php

namespace Theme\Modules\Views;

use Saurus\App\Interfaces\IViewModule;
use Theme\Enum\AppointmentType;
use Theme\Helpers\ThemeHelper;
use Theme\Models\Appointment\Appointment;
use Theme\Services\Appointments\AppointmentService;
use Theme\Services\Services\ServiceCategoryService;
use Theme\Taxonomies\ServiceCategory;

class FrontPage implements IViewModule
{
    public function showDynamicNotifications() {
        if (isset($_GET['c'])) {
            if ($_GET['c'] == "1") {
                ThemeHelper::showNotification("Vaša rezervácia bola úspešne zrušená.", "success");
            } else {
                ThemeHelper::showNotification("Nastala chyba pri rušení Vašej rezervácie. Kontaktujte nás.", "error");
            }
        }
    }

    public function provideData() : array
    {
        $toReturn = [];

        $toReturn['view'] = $this;
        $toReturn['serviceCategories'] = ServiceCategoryService::getServiceCategories();
        $toReturn['trasaLink'] = 'https://www.google.com/maps/dir/49.3516707,18.7862853/M+park,+Centrum+8,+017+01+Pova%C5%BEsk%C3%A1+Bystrica/@49.2335643,18.4590289,11z/data=!3m1!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x47148bfc81801d01:0x3d79319a1a010a15!2m2!1d18.4449841!2d49.1155763!3e0?entry=ttu';

        return $toReturn;
    }
}