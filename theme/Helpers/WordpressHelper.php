<?php

namespace Theme\Helpers;

class WordpressHelper {
    public static function isAutoSave(): bool
    {
        return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
    }
}