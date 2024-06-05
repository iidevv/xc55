<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core\Mail;

use XLite\Core\Mailer;
use QSL\BackInStock\Model\Record;

/**
 * NotificationStock
 */
class NotificationStock extends AMail
{
    public const MESSAGE_DIR = 'modules/QSL/BackInStock/notification';

    /**
     * Constructor
     *
     * @param Record $record
     */
    public function __construct(Record $record = null)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());

        if ($record) {
            $this->setTo($record->getCustomerEmail());

            $profile = $record->getProfile();
            $this->setReplyTo([
                'name'    => $profile ? $profile->getName() : '',
                'address' => $record->getCustomerEmail() ?? '',
            ]);

            $this->tryToSetLanguageCode($profile ? $profile->getLanguage() : $record->getLanguage());

            $variables = [
                'product_name' => $record->getExtendedRecordProductName() ?? '',
                'product_desired_quantity' => implode('', \XLite::getInstance()->getCurrency()->formatParts($record->getQuantity())),
            ];

            if ($product = $record->getProduct()) {
                $variables['product_url']  = $product->getFrontURL() ?? '';
                $variables['product_link'] = '<a href="' . htmlentities($product->getFrontURL()) . '">' . $product->getName() . '</a>';
            }

            if (
                $product
                && ($image = $product->getImage())
            ) {
                $variables['product_image'] = '<img src="' . htmlentities($image->getFrontURL()) . '" alt="' . htmlentities($product->getName()) . '" class="product-image" />';
            }

            $this->populateVariables($variables);
        }

        $this->appendData([
            'record' => $record
        ]);
    }

    /**
     * @inheritDoc
     */
    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'product_desired_quantity' => static::t('Desired quantity'),
        ]);
    }
}
