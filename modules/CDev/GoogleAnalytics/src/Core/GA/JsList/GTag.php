<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\JsList;

use CDev\GoogleAnalytics\Core\GA\AJsList;

class GTag extends AJsList
{
    public function defineCoreCommonJsList(): array
    {
        $list = parent::defineCoreCommonJsList();

        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ga-core.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ga-mapper.js';

        return $list;
    }

    public function defineSearchJsList(): array
    {
        $list = parent::defineSearchJsList();

        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ga-search.js';

        return $list;
    }

    protected function defineEcommerceCommonJsList(): array
    {
        $list = parent::defineEcommerceCommonJsList();

        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-core.js';

        return $list;
    }

    protected function defineEcommerceJsList(): array
    {
        $list = parent::defineEcommerceJsList();

        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-impressions.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-shopping-cart.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-change-item.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-product-details-shown.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/ga-ec-product-click.js';

        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/checkout/ga-change-shipping.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/checkout/ga-change-payment.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/checkout/ga-ec-checkout.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/gtag/ecommerce/checkout/ga-ec-purchase.js';

        return $list;
    }
}
