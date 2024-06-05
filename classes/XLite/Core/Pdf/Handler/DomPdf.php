<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Pdf\Handler;

/**
 * Pdf handler based on dompdf library
 * https://github.com/dompdf/dompdf/wiki
 */
class DomPdf extends \XLite\Core\Pdf\Handler
{
    /**
     * Dompdf instance
     *
     * @var \Dompdf\Dompdf
     */
    protected $dompdfInstance;

    /**
     * Returns Dompdf document instance
     *
     * @return \Dompdf\Dompdf
     */
    public function getDompdfInstance()
    {
        if (!$this->dompdfInstance) {
            $this->dompdfInstance = new \Dompdf\Dompdf();
        }

        return $this->dompdfInstance;
    }

    /**
     * Resets handler state to be ready to next input
     *
     * @return void
     */
    public function reset()
    {
        $this->dompdfInstance = null;
    }

    /**
     * Prepares string input to outputting
     *
     * @param  string   $input  Input data
     * @param  string   $title  Document title OPTIONAL
     * @param  boolean  $html   Is HTML content? OPTIONAL
     * @return void
     */
    public function handleText($input, $title = '', $html = false)
    {
        parent::handleText($input, $title, $html);

        $this->prepareDocument($this->getDefaultDocumentSettings());
        $pdf = $this->getDompdfInstance();

        $pdf->loadHtml($input);
        $pdf->render();
    }

    /**
     * Prepares pdf page to outputting
     *
     * @param  \XLite\View\APdfPage $input Pdf page to output
     * @return void
     */
    public function handlePdfPage(\XLite\View\APdfPage $input)
    {
        parent::handlePdfPage($input);

        if ($input->getLanguageCode() === 'ar') {
            $this->getDompdfInstance()
                ->setOptions(new \Dompdf\Options(
                    [
                        'useArabicConverter' => true,
                    ]
                ));
        }

        $this->prepareDocument($input->getDocumentSettings());

        $html = $input->getHtml();
        if ($input->getLanguageCode() === 'ar') {
            // WA for XCB-835 dompdf currently doesn't support bold arabian font
            $html = str_replace('.invoice-box .items .subitem .name{font-weight:600}', '.invoice-box .items .subitem .name{font-weight:normal}', $html);
        } else {
            // DomPDF doesn't have semi-bold fonts included.
            $html = str_replace('.invoice-box .items .subitem .name{font-weight:600}', '.invoice-box .items .subitem .name{font-weight:bold}', $html);
        }

        $pdf = $this->getDompdfInstance();

        $pdf->loadHtml($html);
        $pdf->render();
    }

    /**
     * Prepares page settings
     *
     * @param  array  $settings Hashmap of settings
     * @return void
     */
    protected function prepareDocument(array $settings)
    {
        $pdf = $this->getDompdfInstance();
        switch ($settings['orientation']) {
            case 'L':
                $orientation = 'landscape';
                break;

            case 'P':
            default:
                $orientation = 'portrait';
                break;
        }
        $pdf->setBasePath(LC_DIR_ROOT);
        $pdf->setPaper($settings['format'], $orientation);
        $pdf->setOptions(new \Dompdf\Options(
            [
                'chroot' => LC_DIR_PUBLIC
            ]
        ));
    }

    /**
     * Outputs the document as string from handler
     *
     * @return mixed
     */
    public function output()
    {
        return $this->getDompdfInstance()->output();
    }
}
