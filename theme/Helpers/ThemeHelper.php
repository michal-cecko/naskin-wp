<?php

namespace Theme\Helpers;

class ThemeHelper
{
    public static function showNotification($text, $status = "success"): string
    {
        ob_start(); ?>
        <div class="notification <?= $status ?> shown">
            <?= main()->assets()->svg("icons/icon-check.svg") ?>
            <span><?= $text ?></span>
        </div>
        <?php return ob_get_clean();
    }
}