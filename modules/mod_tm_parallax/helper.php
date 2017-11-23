<?php
/**
 * Helper class for TM Parallax module
 */
class ModTMParallaxHelper
{

    public static function getContainer($params)
    {
        // Theme Layouts
        $themeLayout = $params->get('themeLayout');

        switch ($themeLayout) {
            case '0':
                $containerClass = 'container';
                break;

            case '1':
                $containerClass = 'container-fluid';
                break;

            default:
                $containerClass = 'container';
                break;
        }
        return $containerClass;
    }
    public static function getRow($params)
    {
        // Theme Layouts
        $themeLayout = $params->get('themeLayout');

        switch ($themeLayout) {
            case '0':
                $rowClass = 'row';
                break;

            case '1':
                $rowClass = 'row-fluid';
                break;

            default:
                $rowClass = 'row';
                break;
        }
        return $rowClass;
    }
}

