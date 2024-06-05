<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

use XLite\Logic\Import\Processor\AProcessor;

/**
 * Requirements processor
 */
class Requirements extends \XC\MigrationWizard\Logic\Import\Processor\ARequirement
{
    public const XCART_EDITION_GOLD = 'Gold/GoldPlus';
    public const XCART_EDITION_PRO  = 'Pro/Platinum';

    protected $platform;

    /**
     * Supported platforms
     *
     * @var string[]|array[]
     */
    protected $supported_platforms = [
        'X-Cart (Gold/GoldPlus)' => '4.1.x',
        //        'X-Cart (Pro/Platinum)' => '4.4.x',
    ];

    /**
     * Get XC4 platform name
     */
    public function getPlatform(): string
    {
        $template = 'X-Cart (%EDITION%)';
        $edition  = self::XCART_EDITION_GOLD;

        if ($this->platform === null) {
            if (
                0 && // the condition is always false as there are no 'name' field in xcart_modules table. Cannot be changed to module_name wo changing supported_platforms (see above Requirements::$supported_platforms)
                $this->getConnection()
                && $this->getConnection()->query("SELECT count(*) FROM {$this->getTablePrefix()}modules WHERE name='Simple_Mode'")
            ) {
                $edition = self::XCART_EDITION_PRO;
            }

            $this->platform = str_replace('%EDITION%', $edition, $template);
        }

        return $this->platform;
    }

    /**
     * Get platform version
     *
     * @return string
     */
    public function getVersion()
    {
        return Configuration::getConfigurationOptionValue('version');
    }

    /**
     * Qualify the Site Path
     *
     * @return string
     */
    public function getLocalOrRemote()
    {
        static $res;

        if (isset($res)) {
            return $res;
        }
        $res = self::getStepConnect()->isSourceSiteLocal() ? 'Local' : 'Remote';
        $res = \XLite\Core\Translation::getInstance()->translate($res);

        $__path = trim(self::getStepConnect()->getSitePath());
        if (empty($__path)) {
            return $res;
        }

        if (self::getStepConnect()->isSourceSiteLocal()) {
            $xc4_host = parse_url(self::getStepConnect()->getSiteUrl(), PHP_URL_HOST);
            $xc5_host = parse_url(\XLite\Core\URLManager::getShopURL(), PHP_URL_HOST);

            if ($xc4_host != $xc5_host && !in_array($xc4_host, \XLite\Core\URLManager::getShopDomains(), true)) {
                $res .= "<br />X-Cart4 : $xc4_host<br />X-Cart5 : $xc5_host";
            }
        }

        return $res;
    }

    /**
     * Return True If Xc4 Is Not Discovered
     *
     * @return boolean
     */
    public function isWrongSitePath()
    {
        static $res;

        if (isset($res)) {
            return $res;
        }
        $res = false;

        $_path = trim(self::getStepConnect()->getSitePath());
        if (empty($_path)) {
            return $res;
        }

        $_languages = \XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration::getAvailableLanguages();

        $paths2check = [
            $_path . '/images/',
        ];

        if (!empty($_languages)) {
            foreach ($_languages as $_lng) {
                $paths2check[] = $_path . '/skin/common_files/pages/' . $_lng . '/';
                $paths2check[] = $_path . '/skin1/common_files/pages/' . $_lng . '/';
                $paths2check[] = $_path . '/skin/pages/' . $_lng . '/';
                $paths2check[] = $_path . '/skin1/pages/' . $_lng . '/';
            }
        }

        foreach ($paths2check as $_file) {
            if (\Includes\Utils\FileManager::isExists($_file)) {
                return $res;
            }
        }

        $res = true;

        return $res;
    }

    /**
     * Get categories count
     *
     * @return integer
     */
    public function getCategoriesCount()
    {
        return $this->getCountFromTable('categories');
    }

    /**
     * Get categories images preview
     *
     * @return array
     */
    public function getCategoriesCountImagesPreview()
    {
        $images = [];

        //if ($this->getCategoriesCount() > 0) {
        //    $IDs = $this->getRandomRecordsFromTable('id', 'images_C');
        //    foreach ($IDs as $id) {
        //        $images[] = Configuration::getCategoryImageURL($id);
        //    }
        //}

        return $images;
    }

    /**
     * Get products count
     *
     * @return integer
     */
    public function getProductsCount()
    {
        return $this->getCountFromTable('products');
    }

    /**
     * Get images size count
     *
     * @return string
     */
    public function getImagesSizeCount()
    {
        $count = 0;

        if ($this->getConnection()) {
            if ($record = $this->getConnection()->query("SELECT sum(image_size) FROM {$this->getTablePrefix()}images_P")) {
                $count += $record->fetchColumn();
            }
            if ($record = $this->getConnection()->query("SELECT sum(image_size) FROM {$this->getTablePrefix()}images_D")) {
                $count += $record->fetchColumn();
            }
            if ($record = $this->getConnection()->query("SELECT sum(image_size) FROM {$this->getTablePrefix()}images_C")) {
                $count += $record->fetchColumn();
            }
            if ($record = $this->getConnection()->query("SELECT sum(image_size) FROM {$this->getTablePrefix()}images_W")) {
                $count += $record->fetchColumn();
            }
        }

        return round($count / (1024 * 1024), 2) . 'Mb';
    }

    /**
     * Get products images preview
     *
     * @return array
     */
    public function getProductsCountImagesPreview()
    {
        $images = [];

        //if ($this->getProductsCount() > 0) {
        //    $IDs = $this->getRandomRecordsFromTable('id', 'images_P');
        //    foreach ($IDs as $id) {
        //        $images[] = Configuration::getProductImageURL($id);
        //    }
        //}

        return $images;
    }

    /**
     * Get categories count
     *
     * @return integer
     */
    public function getOrdersCount()
    {
        return $this->getCountFromTable('orders');
    }

    /**
     * Get extra fields count
     *
     * @return integer
     */
    public function getExtraFieldsCount()
    {
        return $this->getCountFromTable('extra_fields');
    }

    /**
     * Get features count
     *
     * @return integer
     */
    public function getFeaturesCount()
    {
        return $this->getCountFromTable('feature_classes');
    }

    /**
     * Get options count
     *
     * @return integer
     */
    public function getOptionsCount()
    {
        return $this->getCountFromTable('classes');
    }

    /**
     * Get variants count
     *
     * @return integer
     */
    public function getVariantsCount()
    {
        return $this->getCountFromTable('variants');
    }

    /**
     * Get variants images preview
     *
     * @return array
     */
    public function getVariantsCountImagesPreview()
    {
        $images = [];

        //if ($this->getVariantsCount() > 0) {
        //    $IDs = $this->getRandomRecordsFromTable('id', 'images_W');
        //    foreach ($IDs as $id) {
        //        $images[] = Configuration::getProductVariantImageURL($id);
        //    }
        //}

        return $images;
    }

    /**
     * Get Egoods count
     *
     * @return integer
     */
    public function getEgoodsCount()
    {
        $count = '';

        if (
            $this->getConnection()
            && ($record = $this->getConnection()->query("SELECT count(*) FROM {$this->getTablePrefix()}products WHERE distribution <> ''"))
        ) {
            $count = $record->fetchColumn();
        }

        return $count;
    }

    /**
     * Get reviews count
     *
     * @return integer
     */
    public function getReviewsCount()
    {
        return $this->getCountFromTable('product_reviews');
    }

    /**
     * Get votes count
     *
     * @return integer
     */
    public function getVotesCount()
    {
        return $this->getCountFromTable('product_votes');
    }

    /**
     * Get manufacturers count
     *
     * @return integer
     */
    public function getManufacturersCount()
    {
        return $this->getCountFromTable('manufacturers');
    }

    /**
     * Get pages count
     *
     * @return integer
     */
    public function getPagesCount()
    {
        $count = '';

        if (
            $this->getConnection()
            && ($record = $this->getConnection()->query("SELECT count(*) FROM {$this->getTablePrefix()}pages WHERE level = 'E'"))
        ) {
            $count = $record->fetchColumn();
        }

        return $count;
    }

    /**
     * Get membership levels count
     *
     * @return string
     */
    public function getCustomerMembershipLevelsCount()
    {
        $prefix = $this->getTablePrefix();

        $count = AProcessor::getCellData("SELECT count(*) FROM {$prefix}memberships m WHERE m.area = 'C'");

        return $count === false ? '' : $count;
    }

    /**
     * Get destination zones count
     *
     * @return string
     */
    public function getDestinationZonesCount()
    {
        return $this->getCountFromTable('zones');
    }

    //    /**
    //     * Get active modules
    //     *
    //     * @return string
    //     */
    //    public function getActiveModules()
    //    {
    //        $modules = '';
    //
    //        if ($this->getConnection()) {
    //
    //            $query = $this->getConnection()->query("SELECT module_name AS name FROM {$this->getTablePrefix()}modules WHERE active='Y' ORDER BY module_name");
    //
    //            $modules = $this->prepareTextList($query);
    //        }
    //
    //        return $modules;
    //    }

    /**
     * Get payment methods
     *
     * @return string
     */
    public function getPaymentMethods()
    {
        $methods = '';

        if ($this->getConnection()) {
            $query = $this->getConnection()->query("SELECT payment_method AS name FROM {$this->getTablePrefix()}payment_methods WHERE active='Y' ORDER BY payment_method");

            $methods = $this->prepareTextList($query);
        }

        return $methods;
    }

    /**
     * Get shipping methods
     *
     * @return string
     */
    public function getShippingMethods()
    {
        $methods = '';

        if ($this->getConnection()) {
            $query = $this->getConnection()->query("SELECT shipping AS name FROM {$this->getTablePrefix()}shipping WHERE active='Y' ORDER BY shipping");

            $methods = $this->prepareTextList($query);
        }

        return $methods;
    }

    /**
     * Get languages
     *
     * @return string
     */
    public function getLanguages()
    {
        $languages = \XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration::getAvailableLanguages();

        $lngString = implode(', ', array_map(static function ($lng) {
            return strtoupper($lng);
        }, $languages));

        return $lngString;
    }

    /**
     * Check if decryption is available or not
     *
     * @return boolean
     */
    public function isDecryptable()
    {
        $result = false;

        if ($connectStep = static::getStepConnect()) {
            $key    = $connectStep->getSecret();
            $result = \XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration::isSecretKeyValid($key);
            $result = true;
        }

        if (!$result) {
            \XLite\Core\TopMessage::addError('The provided secret key is not valid');
        }

        return $result;
    }

    private function getCountFromTable(string $featureTable)
    {
        $count = '';

        try {
            if (
                $this->getConnection()
                && ($record = $this->getConnection()->query("SELECT count(*) FROM {$this->getTablePrefix()}$featureTable"))
            ) {
                $count = $record->fetchColumn();
            }
        } catch (\PDOException $e) {
            if ($e->getCode() !== '42S02' && stripos($e->getMessage(), 'Base table or view not found: 1146 Table ') === false) {
                throw $e;
            }
        }

        return $count;
    }
}
