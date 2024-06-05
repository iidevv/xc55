<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core\Mail;

use XLite\Core\Mailer;
use QSL\BackInStock\Model\RecordPrice;

/**
 * NotificationPrice
 */
class NotificationPrice extends AMail
{
    public const MESSAGE_DIR = 'modules/QSL/BackInStock/price_drop_notification';

    /**
     * Constructor
     *
     * @param RecordPrice $recordPrice OPTIONAL
     */
    public function __construct(RecordPrice $recordPrice = null)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());

        if ($recordPrice) {
            $this->setTo($recordPrice->getCustomerEmail());

            $profile = $recordPrice->getProfile();
            $this->setReplyTo([
                'name'    => $profile ? $profile->getName() : '',
                'address' => $recordPrice->getCustomerEmail() ?? '',
            ]);

            $this->tryToSetLanguageCode($profile ? $profile->getLanguage() : null);

            if ($product = $recordPrice->getProduct()) {
                $variables = [
                    'product_name'          => $product->getName() ?? '',
                    'product_url'           => $product->getFrontURL() ?? '',
                    'product_link'          => '<a href="' . htmlentities($product->getFrontURL()) . '">' . $product->getName() . '</a>',
                    'product_dropped_price' => \XLite\View\AView::formatPrice($product->getDisplayPrice()),
                ];

                if ($image = $product->getImage()) {
                    $variables['product_image'] = '<img src="' . htmlentities($image->getFrontURL()) . '" alt="' . htmlentities($product->getName()) . '" class="product-image" />';
                }
            } else {
                $variables = [];
            }

            $this->populateVariables($variables);
        }

        $this->appendData([
            'record' => $recordPrice,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'product_dropped_price' => static::t('Product dropped price'),
        ]);
    }
}
