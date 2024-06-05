<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\TranslationLanguage;

/**
 * Abstract translation language
 */
abstract class ATranslationLanguage extends \XLite\Base
{
    /**
     * Label handlers (cache)
     *
     * @var   array
     */
    protected $labelHandlers;

    /**
     * Define label handlers
     *
     * @return array
     */
    protected function defineLabelHandlers()
    {
        return [
            '_X_ items'                                   => 'XItemsMinicart',
            'X items in bag'                              => 'XItemsInBag',
            'X items'                                     => 'XItems',
            'X items available'                           => 'XItemsAvailable',
            'Your shopping bag - X items'                 => 'YourShoppingBagXItems',
            'X modules will be upgraded'                  => 'XModulesWillBeUpgraded',
            'X modules will be disabled'                  => 'XModulesWillBeDisabled',
            'X-Cart trial will expire in X days'          => 'TrialWillExpireInXDays',
            'X days left'                                 => 'XDaysLeft',
            'Items in your cart: X'                       => 'XItemsInYourCart',

            'Your X-Cart trial expires in X days' => 'AccessToBusinessFeaturesExpiresInXDays',

            'X addons'              => 'XAddons',
            'new core and X addons' => 'NewCoreAndXAddons',

            'X orders' => 'XOrders',
            '{{count}} days' => 'XDays',
        ];
    }

    /**
     * Get label handler
     *
     * @param string $name Label name
     *
     * @return string
     */
    public function getLabelHandler($name)
    {
        $handler  = null;
        $handlers = $this->getLabelHandlers();

        $name = (string) $name;

        if (!empty($handlers[$name])) {
            $handler = $handlers[$name];

            if (is_string($handler)) {
                if (method_exists($this, $handler)) {
                    $handler = [$this, $handler];
                } elseif (method_exists($this, 'translateLabel' . ucfirst($handler))) {
                    $handler = [$this, 'translateLabel' . ucfirst($handler)];
                }
            }

            if (!is_callable($handler)) {
                $handler = null;
            }
        }

        return $handler;
    }

    /**
     * Get label handlers
     *
     * @return array
     */
    protected function getLabelHandlers()
    {
        if (!isset($this->labelHandlers)) {
            $this->labelHandlers = $this->defineLabelHandlers();
        }

        return $this->labelHandlers;
    }

    /**
     * Returns the plural position to use for the given locale and number.
     *
     * The plural rules are derived from code of the Zend Framework (2010-09-25),
     * which is subject to the new BSD license (http://framework.zend.com/license/new-bsd).
     * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     *
     * @param integer $number
     * @param string  $locale OPTIONAL
     *
     * @return int
     */
    protected function getPluralizationRule($number, $locale = \XLite\Core\Translation::DEFAULT_LANGUAGE)
    {
        $number = abs($number);

        switch ($locale !== 'pt_BR' && $locale !== 'en_US_POSIX' && \strlen($locale) > 3 ? substr($locale, 0, strrpos($locale, '_')) : $locale) {
            case 'af':
            case 'bn':
            case 'bg':
            case 'ca':
            case 'da':
            case 'de':
            case 'el':
            case 'en':
            case 'en_US_POSIX':
            case 'eo':
            case 'es':
            case 'et':
            case 'eu':
            case 'fa':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
            case 'gl':
            case 'gu':
            case 'ha':
            case 'he':
            case 'hu':
            case 'is':
            case 'it':
            case 'ku':
            case 'lb':
            case 'ml':
            case 'mn':
            case 'mr':
            case 'nah':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'oc':
            case 'om':
            case 'or':
            case 'pa':
            case 'pap':
            case 'ps':
            case 'pt':
            case 'so':
            case 'sq':
            case 'sv':
            case 'sw':
            case 'ta':
            case 'te':
            case 'tk':
            case 'ur':
            case 'zu':
                return ($number == 1) ? 0 : 1;

            case 'am':
            case 'bh':
            case 'fil':
            case 'fr':
            case 'gun':
            case 'hi':
            case 'hy':
            case 'ln':
            case 'mg':
            case 'nso':
            case 'pt_BR':
            case 'ti':
            case 'wa':
                return ($number < 2) ? 0 : 1;

            case 'be':
            case 'bs':
            case 'hr':
            case 'ru':
            case 'sh':
            case 'sr':
            case 'uk':
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);

            case 'cs':
            case 'sk':
                return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);

            case 'ga':
                return ($number == 1) ? 0 : (($number == 2) ? 1 : 2);

            case 'lt':
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);

            case 'sl':
                return ($number % 100 == 1) ? 0 : (($number % 100 == 2) ? 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3));

            case 'mk':
                return ($number % 10 == 1) ? 0 : 1;

            case 'mt':
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3));

            case 'lv':
                return ($number == 0) ? 0 : ((($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2);

            case 'pl':
                return ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2);

            case 'cy':
                return ($number == 1) ? 0 : (($number == 2) ? 1 : ((($number == 8) || ($number == 11)) ? 2 : 3));

            case 'ro':
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);

            case 'ar':
                return ($number == 0) ? 0 : (($number == 1) ? 1 : (($number == 2) ? 2 : ((($number % 100 >= 3) && ($number % 100 <= 10)) ? 3 : ((($number % 100 >= 11) && ($number % 100 <= 99)) ? 4 : 5))));

            default:
                return 0;
        }
    }

    /**
     * @param array   $list
     * @param integer $number
     * @param string  $code
     *
     * @return mixed
     */
    protected function getLabelByRule(array $list, $number, $code = \XLite\Core\Translation::DEFAULT_LANGUAGE)
    {
        $index = (abs($number) === 1 ? 0 : 1);

        return $list[$index] ?? $list[0];
    }

    // {{{ Label translators

    /**
     * Translate label 'X items' in minicart
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsMinicart(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                '_X_ item',
                '_X_ items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X items in your cart' in minicart
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsInYourCart(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'Item in your cart: X',
                'Items in your cart: X',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X items in bag'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsInBag(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X item in bag',
                'X items in bag',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X items'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItems(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X item',
                'X items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X items available'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXItemsAvailable(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X item available',
                'X items available',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'Your shopping bag - X items'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelYourShoppingBagXItems(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'Your shopping bag - X item',
                'Your shopping bag - X items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X modules will be upgraded'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXModulesWillBeUpgraded(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X module will be upgraded',
                'X modules will be upgraded',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X modules will be disabled'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXModulesWillBeDisabled(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X module will be disabled',
                'X modules will be disabled',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X-Cart trial will expire in X days'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelTrialWillExpireInXDays(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X-Cart trial will expire in X day',
                'X-Cart trial will expire in X days',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X-Cart trial will expire in X days'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelAccessToBusinessFeaturesExpiresInXDays(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'Your X-Cart trial expires in X day',
                'Your X-Cart trial expires in X days',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X days left'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXDaysLeft(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X day left',
                'X days left',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X addons'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXAddons(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X addon',
                'X addons',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'new core and X addons'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelNewCoreAndXAddons(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'new core and X addon',
                'new core and X addons',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label 'X items'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXOrders(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X order',
                'X orders',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    /**
     * Translate label '{{count}} days'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXDays(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                '{{count}} day',
                '{{count}} days',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }

    // }}}
}
