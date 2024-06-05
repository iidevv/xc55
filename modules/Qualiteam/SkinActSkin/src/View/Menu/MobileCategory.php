<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu;

use XLite\Core\Database;
use XLite\Core\Translation\Label;
use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\AView;

class MobileCategory extends AView
{
    public const PARAM_CATEGORY = 'category';

    public const PARAM_LEVEL = 'level';

    protected ?array $children = null;

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CATEGORY => new TypeObject('Category', new \XLite\Model\Category()),
            static::PARAM_LEVEL    => new TypeInt('Category level', 0)
        ];
    }

    public function getChildren(): array
    {
        if ($this->children === null) {
            $this->children = Database::getRepo(\XLite\Model\Category::class)->findBy(
                [ 'parent' => $this->getCategoryId() ],
                [ 'pos' => 'ASC' ]
            );
        }

        return $this->children;
    }

    public function hasChildren(): bool
    {
        return count($this->getChildren()) > 0;
    }

    public function getLevel(): int
    {
        return (int)$this->getParam(static::PARAM_LEVEL);
    }

    public function getCategory(): ?\XLite\Model\Category
    {
        return $this->getParam(static::PARAM_CATEGORY);
    }

    public function getURL(): string
    {
        return $this->buildURL('category', '', ['category_id' => $this->getCategoryId()]);
    }

    /**
     * @return string|Label
     */
    public function getTitle()
    {
        return $this->getCategory()->getName();
    }

    public function getCategoryId(): int
    {
        return $this->getCategory()->getCategoryId();
    }

    protected function getDefaultTemplate(): string
    {
        return 'layout/header/mobile_header_parts/category.twig';
    }

    public function getCSSClass(): string
    {
        $class = 'mobile-menu__categories-item mobile-menu__categories-item--level-' . $this->getLevel();

        if ($this->hasChildren()) {
            $class .= ' mobile-menu__categories-item--has-children';
        }

        return $class;
    }

    public function getIcon(): string
    {
        return $this->getCategory()->getIcon();
    }

    protected function isCacheAvailable()
    {
        return true;
    }

    protected function getCacheParameters()
    {
        $list= parent::getCacheParameters();
        $list[] = $this->getCategoryId();
        return $list;
    }

}
