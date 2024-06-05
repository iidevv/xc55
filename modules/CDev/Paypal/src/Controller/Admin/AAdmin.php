<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return bool
     */
    public function isLocalHost(): bool
    {
        $host = \Includes\Utils\ConfigParser::getOptions([
            'host_details',
            $this->isHTTPS() ? 'https_host' : 'http_host'
        ]);

        $isIPv4 = preg_match('/^([0-9]{1,3}[\.]){3}[0-9]{1,3}$/', $host);
        $isIPv6 = preg_match('/^((^|:)([0-9a-fA-F]{0,4})){1,8}$/', $host);

        if ($isIPv4 || $isIPv6) {
            return !filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );
        }

        $isTestDomain = preg_match('/\.test/i', $host);

        return ($host === 'localhost') || $isTestDomain;
    }
}
