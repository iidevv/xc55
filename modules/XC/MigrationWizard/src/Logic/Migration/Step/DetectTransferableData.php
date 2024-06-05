<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Migration Logic - Detect Transferable Data
 */
class DetectTransferableData extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    public const PAID_MOFULE_NAMES = [
        'MyWishlist' => 'My Wishlist',
        'ShopByBrand' => 'Shop By Brand',
        'GiftCertificates' => 'GiftCertificates',
    ];

    public const PAID_LOGIC = [
        'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\GiftCertificates',
        'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersValuesAdvancedFields',
        'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Wishlists',
    ];

    protected $demoMode = false;

    /**
     * Rules
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Save demo mode field value
     */
    public function saveDemoMode()
    {
        $request = \XLite\Core\Request::getInstance();

        if (isset($request->demo_mode)) {
            $this->demoMode = !empty($request->demo_mode);
        }
    }

    /**
     * Return True if should transfer only few entities
     *
     * @return boolean
     */
    public function isDemoMode()
    {
        return $this->demoMode;
    }

    protected $demoProductIds = [];
    protected $demoOrderIds = [];
    protected $demoUserIds = [];
    protected $demoCategoryId = null;

    /**
     * Get ids of products for demo migration
     *
     * @return array
     */
    public function getDemoProductIds()
    {
        return $this->demoProductIds;
    }

    /**
     * Get ids of orders for demo migration
     *
     * @return array
     */
    public function getDemoOrderIds()
    {
        return $this->demoOrderIds;
    }

    /**
     * Get ids of users for demo migration
     *
     * @return array
     */
    public function getDemoUserIds()
    {
        return $this->demoUserIds;
    }

    /**
     * Get id of demo category
     *
     * @return array
     */
    public function getDemoCategoryId()
    {
        return $this->demoCategoryId;
    }

    /**
     * Collect and save ids for demo migration
     */
    public function collectDemoIds()
    {
        $this->demoOrderIds = [];
        $this->demoProductIds = [];
        $this->demoUserIds = [];

        $connection = $this->getConnection();

        $tablePrefix = \XC\MigrationWizard\Logic\Import\Processor\AProcessor::getTablePrefix();

        $query = "SELECT o.orderid, o.email FROM {$tablePrefix}orders as o ORDER BY o.date DESC LIMIT 10";
        $records = $connection->query($query);
        $emails = [];
        while ($record = $records->fetch(\PDO::FETCH_NUM)) {
            $emails[] = $record[1];
            $this->demoOrderIds[] = $record[0];
        }

        if (!empty($this->demoOrderIds)) {
            $orderIdsString = implode(',', $this->demoOrderIds);
            $query = "SELECT DISTINCT(productid) FROM {$tablePrefix}order_details WHERE orderid in ({$orderIdsString})";
            $records = $connection->query($query);
            $records = $records->fetchAll(\PDO::FETCH_COLUMN);
            $this->demoProductIds = $records;
        }

        $query = "SELECT DISTINCT(productid) FROM {$tablePrefix}featured_products WHERE avail='Y' LIMIT 10";
        $records = $connection->query($query);
        $records = $records->fetchAll(\PDO::FETCH_COLUMN);
        $this->demoProductIds = array_merge($this->demoProductIds, $records);

        $query = "SELECT pc.categoryid FROM {$tablePrefix}products_categories as pc "
            . "INNER JOIN {$tablePrefix}categories as c ON pc.categoryid=c.categoryid "
            . "INNER JOIN {$tablePrefix}products as p ON p.productid=pc.productid "
            . "WHERE p.forsale = 'Y' AND c.avail = 'Y' GROUP BY pc.categoryid ORDER BY COUNT(pc.productid) DESC LIMIT 10";
        $records = $connection->query($query);
        $records = $records->fetchAll(\PDO::FETCH_COLUMN);
        $categoryId = null;
        if (!empty($records)) {
            $categoryId = min($records);

            $query = "SELECT p.productid FROM {$tablePrefix}products as p INNER JOIN {$tablePrefix}products_categories as pc ON p.productid=pc.productid WHERE p.forsale = 'Y' AND pc.categoryid = {$categoryId} ORDER BY pc.orderby LIMIT 10";
            $records = $connection->query($query);
            $records = $records->fetchAll(\PDO::FETCH_COLUMN);

            $this->demoProductIds = array_merge($this->demoProductIds, $records);
        }

        $this->demoProductIds = array_unique($this->demoProductIds);

        $emails = implode('\',\'', array_unique($emails));
        if (version_compare(\XC\MigrationWizard\Logic\Import\Processor\AProcessor::getPlatformVersion(), '4.4.0') < 0) {
            $query = "SELECT c.login FROM {$tablePrefix}customers as c WHERE c.usertype='C' AND c.email IN ('{$emails}')";
        } else {
            $query = "SELECT c.id FROM {$tablePrefix}customers as c WHERE c.usertype='C' AND c.email IN ('{$emails}')";
        }
        $records = $connection->query($query);
        $records = $records->fetchAll(\PDO::FETCH_COLUMN);

        $this->demoUserIds = $records;
        $this->demoCategoryId = $categoryId;
    }

    /**
     * Return list of categories with transferable data
     *
     * @return \XC\MigrationWizard\Model\MigrationCategory|bool
     */
    public function getTransferableCategories()
    {
        if (
            \XLite::getController()->getWizard()->getStep('CheckRequirements')
            && ($requirement = \XLite::getController()->getWizard()->getStep('CheckRequirements')->getRequirement())
        ) {
            return \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationCategory')
                ->getEnabledRequirementCategoryAndRules($requirement);
        }

        return false;
    }

    /**
     * Save selected rules
     *
     * @return void
     */
    public function saveSelectedRules()
    {
        $request = \XLite\Core\Request::getInstance();

        $this->rules = [];

        if ($this->isDemoMode()) {
            if (
                \XLite::getController()->getWizard()->getStep('CheckRequirements')
                && ($requirement = \XLite::getController()->getWizard()->getStep('CheckRequirements')->getRequirement())
            ) {
                $categories = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationCategory')
                    ->getEnabledRequirementCategoryAndRules($requirement);

                foreach ($categories as $category) {
                    foreach ($category->getRules() as $rule) {
                        $this->rules[] = $rule->getRule()->getLogic();
                    }
                }
            }
        } else {
            foreach ($request->getPostData() as $var => $value) {
                if ($value === '1' && class_exists($var)) {
                    $this->rules[] = $var;
                }
            }
        }
    }

    /**
     * Get selected rules
     *
     * @return array
     */
    public function getSelectedRules()
    {
        return $this->rules;
    }

    /**
     * Return True if rule is selected
     *
     * @return boolean
     */
    public function isRuleSelected($rule)
    {
        return in_array($rule, $this->rules, true);
    }

    /**
     * Return True if some required modules are not installed and enabled
     *
     * @return boolean
     */
    public function hasMissingModules()
    {
        foreach ($this->rules as $rule) {
            if ($rule::hasMissingModules()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return True if rules were selected
     *
     * @return boolean
     */
    public function hasSelectedRules()
    {
        $result = (bool) $this->rules;

        if (!$result) {
            \XLite\Core\TopMessage::addError('No selected data');
        }

        return $result;
    }

    /**
     * Return Array(Module_name, Url) If The Rule_Logic Is Required A Paid Module
     *
     * @return array
     */
    public function getRequiredPaidModule($rule_logic)
    {
        if (!in_array($rule_logic, static::PAID_LOGIC)) {
            return [];
        }

        $not_installed_modules = $rule_logic::getNotInstalledModules() ?: [];

        if (empty($not_installed_modules)) {
            return [];
        }

        $res = [];

        foreach ($not_installed_modules as $_module_data) {
            [$author, $name] = explode('\\', $_module_data);
            $url = \Includes\Utils\Module\Manager::getRegistry()->getModuleServiceURL($author, $name);

            if (!empty($url)) {
                $res = [
                    'url' => $url,
                    'name' => static::PAID_MOFULE_NAMES[$name] ?? "$author-$name",
                ];

                // Currently only the first module is supported
                break;
            }
        }

        return $res;
    }

    /**
     * Return True If The rule_logic Is Free Or It Is Paid And Has Data
     *
     * @return boolean
     */
    public function isFreeModuleOrHasData($rule_logic)
    {
        return !in_array($rule_logic, static::PAID_LOGIC) || $rule_logic::hasTransferableData();
    }

    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-Select';
    }
}
