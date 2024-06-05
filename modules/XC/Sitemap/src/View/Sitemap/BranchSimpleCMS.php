<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Sitemap\View\Sitemap;

use CDev\SimpleCMS\Model\Page;
use XCart\Extender\Mapping\Extender;

/**
 *  This widget draws a tree's branch
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class BranchSimpleCMS extends \XC\Sitemap\View\Sitemap\Branch
{
    /**
     * Page types
     */
    public const PAGE_STATIC_PAGE = 'A';

    /**
     * Return existence of children of this category
     *
     * @param string  $type Page type
     * @param integer $id   Page ID
     *
     * @return boolean
     */
    protected function hasChild($type, $id)
    {
        if ($type == static::PAGE_STATIC_PAGE) {
            $cnt = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')
                ->countBy(['enabled' => true]);
            $result = $cnt > 0;
        } else {
            $result = parent::hasChild($type, $id);
        }

        return $result;
    }

    /**
     * Get children
     *
     * @param string  $type Page type
     * @param integer $id   Page ID
     *
     * @return array
     */
    protected function getChildren($type, $id)
    {
        if ($type === static::PAGE_STATIC_PAGE) {
            $result = [];
            if (!$id) {
                /** @var Page[]|null $pages */
                $pages = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->getSitemapPages();

                foreach ($pages as $page) {
                    $url = $page->type === Page::TYPE_DEFAULT
                        ? $this->buildURL('page', null, ['id' => $page->getId()])
                        : \XLite\Core\URLManager::getShopURL($page->frontUrl);
                    $result[] = [
                        'type' => static::PAGE_STATIC_PAGE,
                        'id'   => $page->getId(),
                        'name' => $page->getName(),
                        'url'  => $url,
                    ];
                }
            }
        } else {
            $result = parent::getChildren($type, $id);

            if (empty($type)) {
                $result[] = [
                    'type' => static::PAGE_STATIC_PAGE,
                    'id'   => 0,
                    'name' => static::t('Information'),
                ];
            }
        }

        return $result;
    }
}
