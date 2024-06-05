<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Classes;

use XLite\Base\Singleton;
use XLite\Core\Database;
use Qualiteam\SkinActMagicImages\Model\Config;

/**
 * Helper class
 *
 */
class Helper extends Singleton
{
    /**
     * Magic360 module core class
     *
     * @var \Qualiteam\SkinActMagicImages\Classes\Magic360ModuleCoreClass
     *
     */
    protected $primaryTool = null;

    /**
     * Protected constructor.
     * Load and set up Magic360 module core class
     *
     * @return void
     */
    protected function __construct()
    {
        $this->primaryTool = new Magic360ModuleCoreClass();

        $repo = Database::getRepo(Config::class);

        $config = $repo->getEditableAndActiveOptions();
        foreach ($config as $profile => $options) {
            foreach ($options as $id => $value) {
                $this->primaryTool->params->setValue($id, $value, $profile);
            }
        }
    }

    /**
     * Method to get Magic360 module core class
     *
     * @return \Qualiteam\SkinActMagicImages\Classes\Magic360ModuleCoreClass
     */
    public function getPrimaryTool()
    {
        return $this->primaryTool;
    }
}
