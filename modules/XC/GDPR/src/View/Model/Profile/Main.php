<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Profile;
use XC\GDPR\Core\DataRemover;

/**
 * @Extender\Mixin
 */
class Main extends \XLite\View\Model\Profile\Main
{
    public const SECTION_COOKIES = 'cookies';

    /**
     * Schema of the "E-mail & Password" section
     *
     * @var array
     */
    protected $cookiesSchema = [
        'allCookiesConsent' => [
            self::SCHEMA_CLASS => 'XC\GDPR\View\FormField\Input\CookiesConsent',
            self::SCHEMA_LABEL => 'I consent to the processing of all cookies',
            self::SCHEMA_HELP  => 'Cookie profile consent text',
        ],
    ];

    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->mainSchema += [
            'gdprConsent' => [
                self::SCHEMA_CLASS => 'XC\GDPR\View\FormField\Input\GdprConsent',
                self::SCHEMA_LABEL => 'I consent to the collection and processing of my personal data (profile form)',
            ],
        ];
    }

    protected function performActionDelete()
    {
        if ($this->getModelObject() instanceof Profile) {
            DataRemover::getInstance()->removeByProfile($this->getModelObject());
        }

        return parent::performActionDelete();
    }

    /**
     * Return list of the class-specific sections
     *
     * @return array
     */
    protected function getProfileMainSections()
    {
        return parent::getProfileMainSections()
            + [
                static::SECTION_COOKIES => 'Cookies settings',
            ];
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionCookies()
    {
        return $this->getFieldsBySchema($this->cookiesSchema);
    }
}
