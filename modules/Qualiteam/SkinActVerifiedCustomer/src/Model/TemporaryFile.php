<?php


namespace Qualiteam\SkinActVerifiedCustomer\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class TemporaryFile extends \XLite\Model\TemporaryFile
{
    public function getExtensionByMIME()
    {
        if (Request::getInstance()->verification_file > 0) {
            Request::getInstance()->is_image = null;
            return pathinfo($this->getFileName(), PATHINFO_EXTENSION);
        }

        return parent::getExtensionByMIME();
    }

}