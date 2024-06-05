<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\FormField\Label;

/**
 * Read-only field that displays the date when the Google taxonomy was updated the last time and a button to update it.
 */
class GoogleTaxonomyDate extends \XLite\View\FormField\Label
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/google_taxonomy_date.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/google_taxonomy_date.js';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'google_taxonomy_date.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ProductFeeds/form_field/label';
    }

    /**
     * Check if the Google taxonomy is too old.
     *
     * @return boolean
     */
    protected function isTaxonomyDateTooOld()
    {
        // Check if it is more than 3 months since the last update
        return 7776000 < time() - (int) \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_taxonomy_version;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        if ($this->isTaxonomyDateTooOld()) {
            $classes[] = 'old-google-taxonomy';
        }

        return $classes;
    }
}
