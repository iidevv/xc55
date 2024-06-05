<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Tags list box widget
 *
 * @ListChild (list="sidebar.first", zone="customer", weight="200")
 */
class TagsBox extends \XLite\View\SideBarBox
{
    /**
     * Cached tags list
     *
     * @var array
     */
    protected $tags = null;

    /**
     * Get allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = \XLite::TARGET_DEFAULT;
        $result[] = 'search';
        $result[] = 'category';

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $result = parent::getCSSFiles();

        $result[] = 'modules/XC/ProductTags/tags_box/style.css';

        return $result;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Tags';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductTags/tags_box';
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getTags()
    {
        if (!isset($this->tags)) {
            $this->tags = \XLite\Core\Database::getRepo('XC\ProductTags\Model\Tag')->findAll();
        }

        return $this->tags;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->XC->ProductTags->show_tags_sidebar
            && $this->getTags();
    }

    /**
     * getActionURL
     *
     * @param array $params Params to modify OPTIONAL
     *
     * @return string
     */
    protected function getActionURL(array $params = [])
    {
        return $this->buildURL(
            'search',
            '',
            [
                'mode' => 'search',
                'substring' => $params['tag'],
                'including' => \XLite\Model\Repo\Product::INCLUDING_PHRASE,
                'by_tag' => 'Y'
            ]
        );
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-tags-list';
    }
}
