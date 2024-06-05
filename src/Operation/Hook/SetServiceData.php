<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Hook;

use XCart\Domain\ServiceDataDomain;
use XLite\Core\Config;

final class SetServiceData
{
    private ServiceDataDomain $serviceDataDomain;

    public function __construct(ServiceDataDomain $serviceDataDomain)
    {
        $this->serviceDataDomain = $serviceDataDomain;
    }

    public function __invoke(): void
    {
        $siteAdministrator = Config::getInstance()->Company->site_administrator;
        $emails = @unserialize($siteAdministrator, ['allowed_classes' => false]);

        $this->serviceDataDomain->setData('xcart.marketplace.admin-email', $emails[0] ?? '');

        $locationCountry = Config::getInstance()->Company->location_country;
        $this->serviceDataDomain->setData('xcart.marketplace.shop-country', $locationCountry);
    }
}
