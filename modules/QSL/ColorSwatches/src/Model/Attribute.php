<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
abstract class Attribute extends \XLite\Model\Attribute
{
    public const COLOR_SWATCHES_MODE = 'C';

    /**
     * Show selector with color swatches
     *
     * @var bool
     *
     * @ORM\Column (type="boolean", options={"default" : false})
     */
    protected $show_selector = false;

    /**
     * Show color swatches on product list
     *
     * @var bool
     *
     * @ORM\Column (type="boolean", options={"default" : false})
     */
    protected $show_on_list = false;

    /**
     * @return $this
     */
    public function setIsColorSwatchesAttribute($isColorSwatchesAttribute)
    {
        $this->setDisplayMode(self::COLOR_SWATCHES_MODE);

        return $this;
    }

    /**
     * @return bool
     */
    public function isColorSwatchesAttribute()
    {
        return $this->getDisplayMode() === self::COLOR_SWATCHES_MODE;
    }

    /**
     * @return bool
     */
    public function isShowSelector($product = null)
    {
        if ($product) {
            $result = $this->getProperty($product);
            $result = $result ? $result->isShowSelector() : 0;
        }
        if (!$result) {
            $result = $this->show_selector;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function getShowSelector($product = null)
    {
        $result = false;

        if ($product) {
            $result = $this->getProperty($product);
            $result = $result ? $result->isShowSelector() : false;
        }

        if (!$result) {
            $result = $this->show_selector;
        }

        return $result;
    }

    /**
     * @param bool $show_selector
     */
    public function setShowSelector($show_selector)
    {
        if (is_array($show_selector)) {
            $property = $this->getProperty($show_selector['product']);
            $property->setShowSelector($show_selector['show_selector'] ? true : false);
            \XLite\Core\Database::getEM()->persist($property);
        } else {
            $this->show_selector = $show_selector;
        }
    }

    /**
     * @return bool
     */
    public function isShowOnList()
    {
        return $this->show_on_list;
    }

    /**
     * @param bool $show_on_list
     */
    public function setShowOnList($show_on_list)
    {
        $this->show_on_list = $show_on_list;
    }

    /**
     * @return array
     */
    public static function getDisplayModes()
    {
        $list = parent::getDisplayModes();

        // Because specification should be last
        $prev = $list[static::SPECIFICATION_MODE] ?? null;
        unset($list[static::SPECIFICATION_MODE]);

        $list[static::COLOR_SWATCHES_MODE] = static::t('Color swatches');

        if ($prev) {
            $list[static::SPECIFICATION_MODE] = $prev;
        }

        return $list;
    }

    /**
     * @param \XLite\Model\Repo\ARepo $repo
     * @param \XLite\Model\Product    $product
     * @param array                   $data
     * @param int                     $id
     * @param mixed                   $value
     *
     * @return array
     */
    protected function setAttributeValueSelectItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $id,
        $value
    ) {
        $result = parent::setAttributeValueSelectItem($repo, $product, $data, $id, $value);

        if (isset($data['swatch']) && ($swatchId = $data['swatch'][$id]) && $attributeValue = $result[1]) {
            $repo = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');
            $swatch = $repo->find($swatchId);
            $attributeValue->setSwatch($swatch);
        }

        return $result;
    }
}
