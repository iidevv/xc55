<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Controller\Admin;

/**
 * Order reports controller
 */
class OrderReports extends \XLite\Controller\Admin\AAdmin
{
    protected $params = ['target', 'page'];

    public function getTitle()
    {
        return static::t('Order reports');
    }

    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('view order reports');
    }

    protected function isModuleEnabled($name)
    {
        return \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled($name);
    }

    protected function getSchemas()
    {
        return [
            'product'  => [
                'param_name'    => 'Product name',
                'quantity_name' => 'Products sold',
                'url'           => '/admin/?target=product&product_id=',
                'no_id_name'    => '!SALES OF DELETED PRODUCTS',
            ],
            'category' => [
                'param_name'    => 'Category name',
                'quantity_name' => 'Products sold',
                'url'           => '/admin/?target=category&id=',
            ],
            'country'  => [
                'param_name'    => 'Shipping country',
                'quantity_name' => 'Orders placed',
                'url'           => '',
            ],
            'state'    => [
                'param_name'    => 'Shipping state',
                'quantity_name' => 'Orders placed',
                'url'           => '',
            ],
            'users'    => [
                'param_name'    => 'Customer name',
                'quantity_name' => 'Orders placed',
                'url'           => '/admin/?target=profile&profile_id=',
            ],
        ];
    }

    public function displayName($item)
    {
        $url = false;

        if ($this->getSchemaParam('url') && !empty($item['id'])) {
            $url = $this->getSchemaParam('url') . $item['id'];
        }

        if (empty($item['id']) && $this->getSchemaParam('url')) {
            $t    = $this->getSchemaParam('no_id_name');
            $name = empty($t)
                ? $this->getSchemaParam('no_id_name')
                : 'n/a';
        } else {
            $name = $item['name'];
        }

        return $url
            ? '<a href="' . $url . '">' . htmlspecialchars($name) . '</a>'
            : $name;
    }

    public function getSchemaParam($name)
    {
        $schemas = $this->getSchemas();
        $schema  = $schemas[$this->getPage()] ?? [];

        if (empty($schema)) {
            return '';
        }

        return $schema[$name] ?? '';
    }

    public function getPage()
    {
        if (!\XLite\Core\Request::getInstance()->page) {
            \XLite\Core\Request::getInstance()->page = 'total';
        }

        return \XLite\Core\Request::getInstance()->page;
    }

    public function getData()
    {
        $return = $this->{'getSegmentBy' . ucfirst($this->getPage())}();

        return !empty($return) ? $return : [];
    }

    public static function getAllowedOrderStatuses()
    {
        return [
            \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
            \XLite\Model\Order\Status\Payment::STATUS_PAID,
        ];
    }

    protected function getQueryBuilderByRepo(string $entity)
    {
        return \XLite\Core\Database::getRepo($entity)->createQueryBuilder('o');
    }

    protected function getSegmentByTotal()
    {
        $dateRange = $this->getDateRangeValue();

        $result = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('SUM(o.total) AS Total', 'SUM(o.subtotal) AS Subtotal')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->getArrayResult();

        if (isset($result[0]) && is_array($result[0])) {
            $return['Total']    = $result[0]['Total'] ?? 0;
            $return['Subtotal'] = $result[0]['Subtotal'] ?? 0;
        }

        $result = \XLite\Core\Database::getRepo('XLite\Model\Order')
            ->createQueryBuilder('o')
            ->linkInner('o.paymentStatus', 'ps')
            ->leftJoin('o.surcharges', 's', 'WITH', 's.type = :type')
            ->setParameter('type', \XLite\Model\Base\Surcharge::TYPE_SHIPPING)
            ->addSelect('SUM(s.value) AS Shipping')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o.payment_method_name != :gift_certificates')
            ->setParameter('gift_certificates', 'Gift Certificates')
            ->getArrayResult();

        if (isset($result[0]) && is_array($result[0])) {
            $return['Shipping cost'] = $result[0]['Shipping'] ?? 0;
        }

        $result = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->linkInner('o.paymentStatus', 'ps')
            ->leftJoin('o.surcharges', 's')
            ->addSelect('SUM(ROUND(s.value, 2)) AS Tax')
            ->andWhere('s.type = :type')
            ->setParameter('type', \XLite\Model\Base\Surcharge::TYPE_TAX)
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o.payment_method_name != :gift_certificates')
            ->setParameter('gift_certificates', 'Gift Certificates')
            ->getArrayResult();

        if (isset($result[0]) && is_array($result[0])) {
            $return['Tax'] = $result[0]['Tax'] ?? 0;
        }

        $result = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->linkInner('o.paymentStatus', 'ps')
            ->leftJoin('o.surcharges', 's')
            ->addSelect('SUM(s.value) AS Discount')
            ->andWhere('s.type = :type')
            ->setParameter('type', \XLite\Model\Base\Surcharge::TYPE_DISCOUNT)
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o.payment_method_name != :gift_certificates')
            ->setParameter('gift_certificates', 'Gift Certificates')
            ->getArrayResult();

        if (isset($result[0]) && is_array($result[0])) {
            $discount           = isset($result[0]['Discount']) ? substr($result[0]['Discount'], 1) : 0;
            $return['Subtotal'] = $return['Subtotal'] - $discount;
        }

        $result = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('sum(o.total) AS total', 'count(o) AS number')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o.payment_method_name != :gift_certificates')
            ->setParameter('gift_certificates', 'Gift Certificates')
            ->getArrayResult();

        if (isset($result[0]['number']) && !empty($result[0]['number'])) {
            $return['Average order value'] = $result[0]['total'] / $result[0]['number'];
        } else {
            $return['Average order value'] = 'n/a';
        }

        $result = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('sum(o.total) AS total', 'COUNT(o.total) AS number')
            ->linkInner('o.profile', 'profile')
            ->addSelect('profile.login AS login')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o.payment_method_name != :gift_certificates')
            ->setParameter('gift_certificates', 'Gift Certificates')
            ->groupBy('profile.login')
            ->getArrayResult();

        $totalSales   = 0;
        $totalNumber  = 0;
        $repeatNumber = 0;

        foreach ($result as $record) {
            $totalSales   += (float) $record['total'];
            $totalNumber  += $record['number'];
            $repeatNumber += $record['number'] - 1;
        }

        $return['Customer lifetime value']          = count($result) ? $totalSales / count($result) : 0;
        $return['Total number of repeat purchases'] =
            $totalNumber ?
                $repeatNumber . ' (' . sprintf('%d', $repeatNumber * 100 / $totalNumber) . '%' . ')'
                : 0;

        $return['Repeat purchases per customer'] =
            count($result) ?
                sprintf('%01.2f', $repeatNumber / count($result))
                : 0;

        $result = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('o.total AS total', 'o.date AS date')
            ->linkInner('o.profile', 'profile')
            ->addSelect('profile.login AS login')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->andWhere('o.payment_method_name != :gift_certificates')
            ->setParameter('gift_certificates', 'Gift Certificates')
            ->getArrayResult();

        $tree = [];
        foreach ($result as $record) {
            if (!isset($tree[$record['login']])) {
                $tree[$record['login']] = [
                    'last'          => $record['date'],
                    'totalInterval' => 0,
                ];
            } else {
                $tree[$record['login']]['totalInterval'] += $record['date'] - $tree[$record['login']]['last'];
                $tree[$record['login']]['last']          = $record['date'];
            }
        }

        $totalInterval = 0;
        foreach ($tree as $record) {
            $totalInterval += $record['totalInterval'];
        }

        $return['Average time between purchases (in days)'] =
            $repeatNumber ?
                sprintf('%01.1f', $totalInterval / ($repeatNumber * 60 * 60 * 24)) . ' days'
                : 0;

        return $return;
    }

    protected function getSegmentByUsers()
    {
        $dateRange = $this->getDateRangeValue();

        $return = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('sum(o.total) AS sales', 'count(o.total) AS amount')
            ->linkInner('o.orig_profile', 'profile')
            ->addSelect('profile.login AS name', 'profile.profile_id as id')
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->groupBy('id')
            ->orderBy('sales', 'DESC')
            ->getArrayResult();

        return $return;
    }

    /**
     * More flexibility in using a repo with a separated queryBuilder
     *
     * @return array
     */
    protected function getSegmentByProduct()
    {
        $dateRange = $this->getDateRangeValue();

        return \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->getSegmentByProduct($dateRange);
    }

    protected function getSegmentByCategory()
    {
        $dateRange = $this->getDateRangeValue();

        return \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->getSegmentByCategory($dateRange);
    }

    protected function getSegmentByCountry()
    {
        $dateRange = $this->getDateRangeValue();

        return $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('COUNT(o.total) AS amount', 'SUM(o.total) AS sales')
            ->linkInner('o.profile', 'profile')
            ->linkInner('profile.addresses', 'address')
            ->linkInner('address.country', 'acountry')
            ->linkInner('acountry.translations', 'actranslations')
            ->addSelect('actranslations.country AS name')
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses) AND actranslations.code = :code AND address.is_shipping = :true')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->setParameter('code', \XLite\Core\Session::getInstance()->getLanguage()->getCode())
            ->setParameter('true', true)
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->groupBy('address.country')
            ->orderBy('sales', 'DESC')
            ->getResult();
    }

    protected function getSegmentByState()
    {
        $dateRange = $this->getDateRangeValue();

        $return = $this->getQueryBuilderByRepo('XLite\Model\Order')
            ->select('SUM(o.total) AS sales, COUNT(o.total) AS amount')
            ->linkInner('o.profile', 'profile')
            ->linkInner('profile.addresses', 'address')
            ->linkInner('address.country', 'acountry')
            ->linkInner('acountry.translations', 'acountry_translations')
            ->andWhere('acountry_translations.code = :code')
            ->setParameter('code', \XLite\Core\Session::getInstance()->getLanguage()->getCode())
            ->leftJoin('address.state', 'state')
            ->linkLeft('XLite\Model\AddressField', 'aField', 'WITH', 'aField.serviceName = :state')
            ->linkLeft('address.addressFields', 'addressFieldValue', 'WITH', 'addressFieldValue.addressField = aField')
            ->setParameter('state', 'custom_state')
            ->addSelect('CONCAT(IFELSE(state.state IS NULL, addressFieldValue.value, state.state), \' (\', acountry_translations.country,\')\') AS name')
            ->andWhere('address.is_shipping = :true')
            ->setParameter('true', true)
            ->andWhere('o.date > :start_date AND o.date < :end_date')
            ->setParameter('start_date', $dateRange[0])
            ->setParameter('end_date', $dateRange[1])
            ->linkInner('o.paymentStatus', 'ps')
            ->andWhere('ps.code IN (:order_statuses)')
            ->setParameter('order_statuses', $this->getAllowedOrderStatuses())
            ->groupBy('name')
            ->orderBy('sales', 'DESC')
            ->getArrayResult();

        foreach ($return as $key => $item) {
            if (empty($item['name'])) {
                $return[$key]['name'] = 'No state specified';
            }
        }

        return $return;
    }

    public function doActionDate()
    {
        \XLite\Core\Session::getInstance()->orderReportsDateRange = $_POST['dateRange'] ?? [];

        $this->setDefaultReturnURL();
    }

    public function getDateRangeValue()
    {
        return empty(\XLite\Core\Session::getInstance()->orderReportsDateRange)
            ? ['0', '2000000000']
            : \XLite\View\FormField\Input\Text\DateRange::convertToArray(
                \XLite\Core\Session::getInstance()->orderReportsDateRange
            );
    }

    public function getDateRange()
    {
        return \XLite\Core\Session::getInstance()->orderReportsDateRange;
    }

    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['date', 'export']);
    }

    public function getCurrencySymbol()
    {
        $currency = \XLite::getInstance()->getCurrency();

        $return = [];

        if ($currency->getPrefix()) {
            $return[] = $currency->getPrefix();
        }

        if ($currency->getSuffix()) {
            $return[] = $currency->getSuffix();
        }

        return implode(' ', $return);
    }

    public function formatPriceForReports($value)
    {
        $currency = \XLite::getInstance()->getCurrency();

        $value = $currency->roundValue($value);

        $parts = [];

        $parts['integer'] = number_format(floor(abs($value)), 0, '', $currency->getThousandDelimiter());

        if (0 < $currency->getE()) {
            $parts['decimalDelimiter'] = $currency->getDecimalDelimiter();
            $parts['decimal']          = str_pad(
                substr(
                    strval(abs($value != 0 ? $value : 1) * pow(10, $currency->getE())),
                    -1 * $currency->getE()
                ),
                $currency->getE(),
                '0',
                STR_PAD_LEFT
            );
        }

        return implode($parts);
    }

    public function doActionExport()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $this->getPage() . '-report.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, $this->getSchemaColumns());

        $data = $this->getData();

        foreach ($data as $row) {
            $output = $this->getSchemaValues($row);
            fputcsv($out, $output);
        }

        fclose($out);

        exit;
    }

    /**
     * @return array
     */
    protected function getSchemaColumns()
    {
        return [
            $this->getSchemaParam('param_name'),
            'Sales (' . $this->getCurrencySymbol() . ')',
            $this->getSchemaParam('quantity_name'),
        ];
    }

    /**
     * @param array $rowData
     *
     * @return array
     */
    protected function getSchemaValues(array $rowData)
    {
        return [
            $rowData['name'],
            $this->formatPriceForReports($rowData['sales']),
            $rowData['amount']
        ];
    }

    protected function setDefaultReturnURL()
    {
        $this->setReturnURL(
            $this->buildURL(
                'order_reports',
                '',
                ['page' => $_POST['page']]
            )
        );
    }

    public function isExportShown()
    {
        return ($this->getPage() != 'total') ? true : false;
    }
}
