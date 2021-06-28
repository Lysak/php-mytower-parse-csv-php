<?php

namespace components;

/**
 * Class View
 * @package components
 */
class View
{
    /**
     * @param       $partial_path
     * @param array $data
     * @return mixed
     */
    public function render($partial_path, array $data = []): mixed
    {
        $full_path = APPLICATION_ROOT . $partial_path . Helpers::PHP_EXTENSION;

        if (file_exists($full_path)) {
            return require_once($full_path); // ??
        } else {
           self::render('/views/site/error', ['view not found']);
        }

        return false;
    }
}
