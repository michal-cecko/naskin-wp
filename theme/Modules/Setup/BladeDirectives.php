<?php

namespace Theme\Modules\Setup;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Theme\Models\Appointment;

class BladeDirectives
{
    public function __construct()
    {
        $this->setupDirectives();
    }

    private function setupDirectives(): void
    {
        $directives = [];

        /*$directives['menu'] = function ( $expression ) {
            return "
                   <?php
                \$menu_name = trim($expression, '\"\'');
                \$locations = get_nav_menu_locations();
                if (!isset(\$locations[\$menu_name])) {
                    echo 'Menu '.\$menu_name.' not exist 1';
                }
                \$menu = wp_get_nav_menu_object(\$locations[\$menu_name]);
                if (empty(\$menu)) {
                    echo 'Menu '.\$menu_name.' not exist 2';
                    return;
                }
                \$menuitems = wp_get_nav_menu_items(\$menu->term_id, ['order' => 'DESC']);
                \$ids = [];
                \$tree = [];
                foreach (\$menuitems as \$item){
                    \$ids[\$item->ID] = [
                        'title' => \$item->title,
                        'url' => \$item->url,
                        'target' => \$item->target,
                        'childrens' => []
                    ];
                }
                foreach (\$menuitems as \$item){
                    if (intval(\$item->menu_item_parent) === 0){
                        \$tree[\$item->ID] = &\$ids[\$item->ID];
                    }else{
                        \$ids[\$item->menu_item_parent]['childrens'][] = &\$ids[\$item->ID];
                    }
                }
                \$print = function(\$menuitems)use(&\$print){
                    if (count(\$menuitems) == 0) return;
                    echo '<ul>';
                    foreach (\$menuitems as \$item) {

                        echo '<li class=\"item\">';
                            echo '<a href=\"' . \$item[\"url\"] . '\" target=\"'. \$item[\"target\"] .'\" class=\"title ' . (!empty(\$item['childrens']) ? 'has-submenu' : ''). '\">';
                                echo \$item['title'];
                                if(!empty(\$item['childrens'])){
                               		echo '<svg style=\"margin-left: 7px;\" width=\"10\" height=\"7\" viewBox=\"0 0 10 7\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">
<path d=\"M1 1L5 5L9 1\" stroke=\"#181D27\" stroke-width=\"2\"/></svg>';                                }
                            echo '</a>';
                            \$print(\$item['childrens']);
                        echo '</li>';
                    }
                    echo '</ul>';
                };
                \$print(\$tree);
                ?>
            ";
        };*/

        // Register those custom directives
        templates()->provider()->registerDirectives($directives);
    }

}