<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class AppConfig
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class AppConfig implements ResolverInterface
{
    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $appConfig = [
            'store_platform'   => 'XCart5',
            'store_version'    => \XLite::getInstance()->getVersion(),
            'currency'         => Currencies::mapToDto(
                \XLite::getInstance()->getCurrency()
            ),
            'default_language' => Languages::mapToDto(
                $this->getDefaultLanguage()
            ),
            'default_country'  => Countries::mapToDto(
                $this->getDefaultCountry()
            ),

            'date_format' => static::transformDateTimeFormat(
                \XLite\Core\Config::getInstance()->Units->date_format
            ),
            'time_format' => static::transformDateTimeFormat(
                \XLite\Core\Config::getInstance()->Units->time_format
            ),

            // To be filled in modules
            'terms_and_conditions' => '',
            'contact_email'        => '',
            'contact_phone'        => '',
            'contact_address'      => '',
            'is_webview_checkout_flow' => $this->isWebviewCheckoutFlow()
        ];

        return $appConfig;
    }

    protected static function transformDateTimeFormat($format)
    {
        $result = $format;

        $translations = array(
            '%A'    => 'dddd',
            '%b'    => 'MMM',
            '%B'    => 'MMMM',
            '%d'    => 'DD',
            '%e'    => 'D',
            '%H'    => 'HH',
            '%I'    => 'hh',
            '%m'    => 'MM',
            '%M'    => 'mm',
            '%p'    => 'A',
            '%r'    => 'hh:mm:ss A',
            '%T'    => 'HH:mm:ss',
            '%Y'    => 'YYYY',
        );

        foreach ($translations as $from => $to) {
            if (strpos($format, $from) !== false) {
                $result = str_replace($from, $to, $result);
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    protected function getDefaultLanguage()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Language')
            ->findOneByCode(
                \XLite::getDefaultLanguage()
            );
    }

    /**
     * TODO Should it be from default customer address?
     *
     * @return mixed
     */
    protected function getDefaultCountry()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode(
                \XLite\Core\Config::getInstance()->Company->location_country
            );
    }

    /**
     * @return bool
     */
    protected function isWebviewCheckoutFlow()
    {
        return false;
    }
}
