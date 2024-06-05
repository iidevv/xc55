<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute option
 * @Extender\Mixin
 */
class AttributeProperty extends \XLite\Model\AttributeProperty
{
    /**
     * Show selector with color swatches
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default" : false})
     */
    protected $show_selector = false;

    /**
     * Show color swatches on product list
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default" : false})
     */
    protected $show_on_list = false;

    /**
     * @return bool
     */
    public function isShowSelector()
    {
        return $this->show_selector;
    }

    /**
     * @param bool $show_selector
     */
    public function setShowSelector($show_selector)
    {
        $this->show_selector = $show_selector;
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
}
