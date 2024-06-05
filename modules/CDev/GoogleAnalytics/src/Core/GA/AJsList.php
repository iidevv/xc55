<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA;

use CDev\GoogleAnalytics\Core\GA\Interfaces\ILibrary;

abstract class AJsList
{
    /**
     * @var array
     */
    public $common = [];

    /**
     * @var array
     */
    public $ecommerce = [];

    /**
     * @var array
     */
    public $search = [];

    /**
     * @var ILibrary
     */
    protected $library;

    public function __construct(ILibrary $library)
    {
        $this->library = $library;

        $this->defineLists();
    }

    protected function defineLists(): void
    {
        $ecommerce_common = [];
        $ecommerce        = [];

        if ($this->library->isEcommerceEnabled()) {
            $ecommerce_common = $this->defineEcommerceCommonJsList();
            $ecommerce        = $this->defineEcommerceJsList();
        }

        if ($ecommerce_common || $this->library->isSendPageviewActive()) {
            $this->common = $this->defineCoreCommonJsList();
        }

        if ($this->common = array_merge_recursive($this->common, $ecommerce_common)) {
            if ($ecommerce) {
                $this->ecommerce = $ecommerce;
            }

            $this->search = $this->defineSearchJsList();
        }
    }

    protected function defineEcommerceCommonJsList(): array
    {
        $list = [];

        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-core.js';

        return $list;
    }

    protected function defineEcommerceJsList(): array
    {
        $list = [];

        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-impressions.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-shopping-cart.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-change-item.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-product-details-shown.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/ga-ec-product-click.js';

        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/checkout/ga-change-shipping.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/checkout/ga-change-payment.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/checkout/ga-ec-checkout.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/checkout/checkout-complete.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ecommerce/checkout/ga-ec-purchase.js';

        if ($this->needsCheckoutTracking()) {
            $list = array_merge($list, $this->getCheckoutTrackingList());
        }

        return $list;
    }

    protected function needsCheckoutTracking(): bool
    {
        return in_array(
            \XLite::getController()->getTarget(),
            ['checkout', 'checkoutPayment'],
            true
        );
    }

    protected function getCheckoutTrackingList(): array
    {
        return [
            'modules/CDev/GoogleAnalytics/adapters/adapters/base.js',
            'modules/CDev/GoogleAnalytics/adapters/adapters/one-page.js',
        ];
    }

    protected function defineCoreCommonJsList(): array
    {
        $list = [];

        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ga-core.js';
        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ga-event.js';

        return $list;
    }

    protected function defineSearchJsList(): array
    {
        $list = [];

        $list[] = 'modules/CDev/GoogleAnalytics/library/src/ga-search.js';

        return $list;
    }
}
