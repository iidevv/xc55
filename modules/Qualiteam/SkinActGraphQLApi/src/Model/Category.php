<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActGraphQLApi\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{

    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default" : true})
     */
    protected $showInMobileApp = true;

    /**
     * @return bool
     */
    public function getShowInMobileApp()
    {
        return $this->showInMobileApp;
    }

    /**
     * @param bool $showInMobileApp
     */
    public function setShowInMobileApp($showInMobileApp)
    {
        $this->showInMobileApp = $showInMobileApp;
    }


}