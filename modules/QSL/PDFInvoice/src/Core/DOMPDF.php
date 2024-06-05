<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\Core;

/**
 * DOM PDF
 */
class DOMPDF extends \XLite\Base\Singleton
{
    /**
     * Catalog name for storing CSS filename
     */
    public const CSSPDFINVOICE_FILE_DIR = 'pdfinvoice';

    /**
     * Catalog name for invoices cache inside the files dir
     */
    public const PDFINVOICE_FILE_DIR = 'invoices';

    /**
     * Text to replace with the order number
     */
    public const ORDER_NUMBER_PATTERN = '[orderNumber]';

    /**
     * Prefix to know if the order number is actually the order id
     */
    public const ID_PREFIX = 'orderid-';

    /**
     * DOMPDF lib object
     *
     * @var null|\Dompdf\Dompdf
     */
    protected static $DOMPDF = null;

    /**
     * Invoice view class cache
     *
     * @var \QSL\PDFInvoice\View\Invoice|null
     */
    protected static $view = null;

    /**
     * Initialize the DOMPDF static object to use
     *
     * @return \QSL\PDFInvoice\Core\DOMPDF
     */
    public static function getInstance()
    {
        $result = parent::getInstance();

        if (is_null(static::$DOMPDF)) {
            static::resetPdf();
        }

        return $result;
    }

    /**
     * Reset PDF handler
     */
    protected static function resetPdf()
    {
        $dompdfHandler = new \XLite\Core\Pdf\Handler\DomPdf();

        static::$DOMPDF = $dompdfHandler->getDompdfInstance();

        static::$DOMPDF->setOptions(new \Dompdf\Options([
            'isRemoteEnabled'      => true,
            'isHtml5ParserEnabled' => true,
            'tempDir'              => LC_DIR_TMP,
        ]));
    }

    protected static function isArabic()
    {
        return \XLite\Core\Session::getInstance()->getLanguage()
            && \XLite\Core\Session::getInstance()->getLanguage()->getCode() === 'ar';
    }

    /**
     * Defines the relative filename of the custom CSS
     *
     * Is NOT used actually right NOW
     *
     * @param boolean $isAdminZone Flag to define admin/customer zone
     *
     * @return string
     */
    public function getCustomCSSFileName($isAdminZone)
    {
        $dir = substr(LC_DIR_CACHE_RESOURCES, strlen(LC_DIR_ROOT));

        return $dir . static::CSSPDFINVOICE_FILE_DIR . LC_DS . ($isAdminZone ? 'admin' : 'customer') . LC_DS . 'invoice.css';
    }

    /**
     * Download PDF file of the invoice with orderNumber order number.
     *
     * @param array $orderIds Order number
     *
     * @return void
     */
    public function streamPDFInvoice($orderIds, $lng)
    {
        $data = $this->generatePDFInvoice($orderIds, $lng, true);

        // Start downloading
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="' . $this->getPDFInvoiceFileName($orderIds, $lng) . '"');
        header('Content-Length: ' . strlen($data));

        echo $data;

        exit(0);
    }

    public function generatePDFInvoice($orderIds, $lng = '', $force = false)
    {
        $orderIds = is_array($orderIds) ? $orderIds : [$orderIds];

        $page = new \QSL\PDFInvoice\View\Invoices();
        $page->setWidgetParams([
            \QSL\PDFInvoice\View\Invoices::PARAM_ORDER    => \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderIds[0]),
            \QSL\PDFInvoice\View\Invoices::PARAM_ORDERIDS => $orderIds,
            'zone'                                                     => \XLite\Core\Layout::getInstance()->getZone(),
        ]);

        if (!empty($lng)) {
            $page->setLanguageCode($lng);
        }

        $handler = \XLite\Core\Pdf\Handler::getDefault();

        $handler->handlePdfPage($page);

        return $handler->output();
    }

    /**
     * Removes the PDF cache file with the order number
     *
     * @return void
     */
    public function clearPDFInvoices()
    {
        // Remove both admin and customer PDF invoice
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_FILES . static::PDFINVOICE_FILE_DIR . LC_DS . 'admin');
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_FILES . static::PDFINVOICE_FILE_DIR . LC_DS . 'customer');
    }

    /**
     * Defines the PDF invoice file name for downloading.
     * We use translated file name from fileNamePattern
     *
     * @param mixed $orderId Order ID
     *
     * @return string
     */
    public function getPDFInvoiceFileName($orderIds, $lng = '')
    {
        $orderIds = is_array($orderIds) ? $orderIds : [$orderIds];

        $orderNumber = implode('_', array_map([$this, 'getOrderNumber'], $orderIds));

        return
            str_replace(
                static::ORDER_NUMBER_PATTERN,
                $orderNumber,
                \XLite\Core\Config::getInstance()->QSL->PDFInvoice->fileNamePattern
            ) . '.pdf';
    }

    /**
     * Defines the PDF invoice cache filename on the file system
     *
     * @param string  $orderId     Order ID
     * @param boolean $isAdminZone Flag to define admin zone
     *
     * @return string
     */
    public function getPDFFileName($orderIds, $isAdminZone = null, $lng = '')
    {
        $isAdminZone = is_null($isAdminZone) ? \XLite::isAdminZone() : $isAdminZone;

        $orderIds = is_array($orderIds) ? $orderIds : [$orderIds];

        $orderNumber = implode('_', array_map([$this, 'getOrderNumber'], $orderIds));

        return LC_DIR_FILES . static::PDFINVOICE_FILE_DIR . LC_DS . ($isAdminZone ? 'admin' : 'customer') . LC_DS . $orderNumber . '-' . $lng . '.pdf';
    }

    /**
     * Returns PDF invoice content generated by DOMPDF lib
     *
     * @param array $orderIds Order numbers
     *
     * @return string
     */
    protected function getPDFInvoice($orderIds, $lng, $force = false)
    {
        $this->prepareView($this->getInvoiceHTML($orderIds, $lng, $force));

        $result = static::$DOMPDF->output();

        static::resetPdf();

        return $result;
    }

    /**
     * Defines the CSS files to design invoice in PDF content
     *
     * @param boolean $isAdminZone Admin zone flag
     *
     * @return array
     */
    protected function getInvoiceCSSFiles($isAdminZone = null)
    {
        $isAdminZone = is_null($isAdminZone) ? \XLite::isAdminZone() : $isAdminZone;

        $result = [];

        foreach ($this->getInvoiceCSSFilesList() as $style) {
            $result[] = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                $style,
                \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                \XLite::INTERFACE_WEB,
                $isAdminZone ? \XLite::ZONE_ADMIN : \XLite::ZONE_CUSTOMER
            );
        }

        return $result;
    }

    protected function getInvoiceCSSFilesList()
    {
        return [
            'modules/QSL/PDFInvoice/invoice.css',
        ];
    }

    /**
     * Defines the CSS stylesheet HTML code inside the HEAD tag in the PDF content
     *
     * @return string
     */
    protected function getInvoiceCSSFilesBlock()
    {
        $cssFiles = '';

        foreach ($this->getInvoiceCSSFiles() as $file) {
            if ($file && is_string($file)) {
                $cssFiles .= '<link href="' . $file . '" rel="stylesheet" type="text/css"/>';
            }
        }

        return $cssFiles;
    }

    /**
     * Defines the \XLite\View\Invoice object to use inside the invoice HTML content
     *
     * @param string $orderId Order ID
     *
     * @return \XLite\View\Invoice
     */
    protected function getInvoiceView($orderId)
    {
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        $view = new \QSL\PDFInvoice\View\Invoice();

        $view->setWidgetParams([
            \XLite\View\Invoice::PARAM_ORDER => $order,
        ]);

        return $view;
    }

    public function getOrderNumber($orderId)
    {
        $result = '';
        $order  = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order) {
            $result = $order->getOrderNumber() ?: (self::ID_PREFIX . $orderId);
        }

        return $result;
    }

    protected function isOrder($orderId)
    {
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        return $order ? (bool) $order->getOrderNumber() : false;
    }

    /**
     * Defines the HTML content of the invoice for specific order
     *
     * @param array $orderIds Order number
     *
     * @return string
     */
    protected function getInvoiceHTML($orderIds, $lng, $force = false)
    {
        $baseHREF = \XLite::getInstance()->getShopURL('', false);

        $html = <<<EOF
<html>
    <head>
  <base href="{$baseHREF}" />
{$this->getInvoiceCSSFilesBlock()}
    </head>
<body>
EOF;

        foreach ($orderIds as $orderId) {
            $html .= $this->getInvoiceViewCache($orderId, $lng, $force);
        }

        return $html . '</body></html>';
    }

    /**
     * Get invoice view via cache
     *
     * @param string $orderId Order ID
     *
     * @return string
     */
    protected function getInvoiceViewCache($orderId, $lng, $force = false)
    {
        $filename = $this->getInvoiceViewCacheFilename($orderId, null, $lng);

        if (!is_file($filename) || $force) {
            $data = $this->getInvoiceView($orderId)->getContent();
            \Includes\Utils\FileManager::write(
                $filename,
                $data,
                0,
                0777
            );
        } else {
            $data = \Includes\Utils\FileManager::read($filename);
        }

        return $data;
    }

    /**
     * Defines the filename for invoice view cache
     *
     * @param string  $orderId     Order ID
     * @param boolean $isAdminZone Admin zone flag
     *
     * @return string
     */
    protected function getInvoiceViewCacheFilename($orderId, $isAdminZone, $lng)
    {
        $isAdminZone = is_null($isAdminZone) ? \XLite::isAdminZone() : $isAdminZone;

        $orderNumber = $this->getOrderNumber($orderId);

        return LC_DIR_FILES . static::PDFINVOICE_FILE_DIR . LC_DS . ($isAdminZone ? 'admin' : 'customer') . LC_DS . 'view' . LC_DS . $orderNumber . '-' . $lng . '.html';
    }

    /**
     * Initialize the DOMPDF lib object with the html content
     *
     * @param string $html
     *
     * @return void
     */
    protected function prepareView($html)
    {
        static::$DOMPDF->loadHtml($html);
        static::$DOMPDF->render();
    }
}
