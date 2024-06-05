<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Close storefront action controller
 */
class Storefront extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Close storefront
     *
     * @return void
     */
    protected function doActionClose()
    {
        \XLite\Core\Auth::getInstance()->closeStorefront();
        $this->fireEvent();
    }

    /**
     * Close storefront (secure token is not needed for this action)
     *
     * @return void
     */
    protected function doActionCloseWithoutFormIdCheck()
    {
        $this->doActionClose();
    }

    /**
     * Open storefront
     *
     * @return void
     */
    protected function doActionOpen()
    {
        \XLite\Core\Auth::getInstance()->openStorefront();
        $this->fireEvent();
    }

    /**
     * Open storefront (secure token is not needed for this action)
     *
     * @return void
     */
    protected function doActionOpenWithoutFormIdCheck()
    {
        $this->doActionOpen();
    }

    /**
     * Fire event
     *
     * @return void
     */
    protected function fireEvent()
    {
        \XLite\Core\Event::switchStorefront(
            [
                'opened' => !\XLite\Core\Auth::getInstance()->isClosedStorefront(),
                'link'   => $this->buildURL(
                    'storefront',
                    '',
                    [
                        'action'    => (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'open' : 'close'),
                    ]
                ),
                'privatelink' => $this->getAccessibleShopURL(false),
            ]
        );

        if ($this->isAJAX()) {
            $this->silent = true;
            $this->setSuppressOutput(true);
        }
    }
}
