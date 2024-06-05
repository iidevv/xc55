<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActMain\Core;


use XCart\Container;

class Logger
{




    public function logCustom($name, $data, $trace)
    {
        $container = Container::getContainer();
        $logger = $container->get('xcart.logger');


        var_dump($logger);die;

        \XLite\Logger::getLogger($name)->warning(var_export($data, true));

    }
}
