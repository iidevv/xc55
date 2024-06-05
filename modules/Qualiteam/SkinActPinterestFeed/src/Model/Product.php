<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestFeed\Model;

use Doctrine\ORM\Mapping as ORM;
use QSL\ProductFeeds\Model\GoogleShoppingCategory;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Linked Pinterest category.
     *
     * @var GoogleShoppingCategory
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ProductFeeds\Model\GoogleShoppingCategory")
     * @ORM\JoinColumn (name="pinterestCategory", referencedColumnName="google_id", nullable=true, onDelete="SET NULL")
     */
    protected $pinterestCategory;

    public function setPinterestId($id)
    {
        $this->setPinterestCategory(Database::getRepo(GoogleShoppingCategory::class)->find($id));
    }

    public function setPinterestCat($name)
    {
        $category = \XLite\Core\Database::getRepo(GoogleShoppingCategory::class)
            ->findOneByName($name);

        $this->setPinterestCategory($category);
    }

    public function setPinterestCategory(GoogleShoppingCategory $googleShoppingCategory = null)
    {
        $this->pinterestCategory = $googleShoppingCategory;

        return $this;
    }

    public function getPinterestCategory()
    {
        return $this->pinterestCategory;
    }

    protected function cloneEntityModels(\XLite\Model\Product $newProduct)
    {
        parent::cloneEntityModels($newProduct);

        $newProduct->setPinterestCategory($this->getPinterestCategory());
    }
}