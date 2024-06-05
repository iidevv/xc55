<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Pdf page template
 */
abstract class APdfPage extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_ZONE = 'zone';

    /**
     * PDF page settings keys
     *
     * ORIENTATION: Page orientation (portrait or album).
     * UNIT: Page dimension unit.
     * FORMAT: Page paper format (e.g. A4).
     * ENCODING: Page encoding (e.g. UTF-8).
     * MARGINS: Array of page margins in given order: LEFT, TOP, RIGHT, BOTTOM margin.
     */
    public const ORIENTATION = 'orientation';
    public const UNIT        = 'unit';
    public const FORMAT      = 'format';
    public const ENCODING    = 'encoding';
    public const MARGINS     = 'margins';

    /**
     * Get pdf interface
     *
     * @return string
     */
    public function getZone()
    {
        return $this->getParam(self::PARAM_ZONE);
    }

    /**
     * Get pdf language
     *
     * @return string
     */
    public function getLanguageCode()
    {
        if ($this->getZone()) {
            $code = ($this->getZone() === \XLite::ZONE_CUSTOMER)
                ? \XLite\Core\Config::getInstance()->General->default_language
                : \XLite\Core\Config::getInstance()->General->default_admin_language;
        }

        return $code;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ZONE => new \XLite\Model\WidgetParam\TypeString(
                'Pdf zone',
                \XLite::ZONE_CUSTOMER
            ),
        ];
    }

    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        return '';
    }

    /**
     * Returns PDF specific styles
     *
     * @return array
     */
    public function getPdfStylesheets()
    {
        return [
            'reset.css',
        ];
    }

    /**
     * Returns PDF-specific stylesheets
     *
     * @return array
     */
    public function getStylesheetPaths()
    {
        $styles = $this->getPdfStylesheets();

        $paths = array_map(
            static function ($style) {
                if ($style) {
                    $path = \XLite\Core\Layout::getInstance()
                        ->getResourceFullPath($style, \XLite::INTERFACE_PDF, \XLite::ZONE_COMMON);

                    return $path;
                }
            },
            $styles
        );

        return $paths;
    }

    /**
     * Compiles template to HTML
     *
     * @return string
     */
    public function compile()
    {
        $layout = \XLite\Core\Layout::getInstance();

        $baseTmpTranslation = \XLite\Core\Translation::getTmpTranslationCode();
        $baseLangCode = \XLite\Core\Session::getInstance()->getCurrentLanguage();
        $pageLangCode = $this->getLanguageCode();
        $this->setCompileLocale($pageLangCode, $pageLangCode);

        $text = $layout->callInInterfaceZone(function () {
            $this->init();

            return $this->getContent();
        }, \XLite::INTERFACE_PDF, $this->getZone());

        $this->setCompileLocale($baseTmpTranslation, $baseLangCode);

        return $text;
    }

    /**
     * @return void
     */
    protected function setCompileLocale($translationCode, $languageCode)
    {
        \XLite\Core\Translation::setTmpTranslationCode($translationCode);
        \XLite\Core\Converter::resetLocaleSet();
        \XLite\Core\Session::getInstance()->setLanguage($languageCode);
    }

    /**
     * Compiles body, title and stylesheets in complete html string
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->buildHtml($this->compile(), $this->mergeStylesheets(), $this->getDocumentTitle());
    }

    /**
     * Loads each stylesheet file and merges them together
     *
     * @return string
     */
    protected function mergeStylesheets()
    {
        $stylesheet = '';

        foreach ($this->getStylesheetPaths() as $path) {
            $pathinfo = pathinfo($path);

            $text = '';
            if (
                isset($pathinfo['extension'])
                && $pathinfo['extension'] === 'less'
            ) {
                $lessRaw = \XLite\Core\LessParser::getInstance()
                    ->makeCSS(
                        [
                            [
                                'file'      => $path,
                                'original'  => $path,
                                'less'      => true,
                                'media'     => 'all',
                                'interface' => \XLite::INTERFACE_PDF,
                            ],
                        ]
                    );
                if ($lessRaw && isset($lessRaw['file'])) {
                    $text = \Includes\Utils\FileManager::read($lessRaw['file']);
                }
            } else {
                $text = \Includes\Utils\FileManager::read($path);
            }
            if ($text) {
                $stylesheet .= $text . PHP_EOL;
            }
        }

        return $stylesheet;
    }

    /**
     * @return string
     */
    protected function getBodyFont()
    {
        $code = \XLite\Core\Translation::getTmpTranslationCode() ?: $this->getLanguageCode();
        $code = strtoupper($code);

        $cjkList = [
            'ZH' => 'TC',
            'KO' => 'KR',
            'JA' => 'JP',
        ];

        if (isset($cjkList[$code])) {
            return 'Noto Sans ' . $cjkList[$code];
        }

        return 'DejaVu Sans';
    }

    /**
     * Inline styles
     *
     * @param string $styles CSS code
     *
     * @return string
     */
    protected function buildHtml($body, $styles, $title)
    {
        $bodyFont = $this->getBodyFont();

        $html =
            "<html>
    <head>
        <title>$title</title>
        <style type='text/css'>
            body {
                font-family: $bodyFont !important;
                letter-spacing: -.5px;
            }
            $styles
        </style>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
    </head>
    <body>
        $body
    </body>
</html>";

        return $html;
    }

    /**
     * Default page settings
     *
     * @return array
     */
    public static function getDocumentSettings()
    {
        return [
            static::FORMAT      => 'A4',
            static::ORIENTATION => 'P',
        ];
    }
}
