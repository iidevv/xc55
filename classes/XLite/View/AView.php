<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Utils\ConfigParser;
use Twig\Markup;
use XLite\Core\Cache\CacheKeyPartsGenerator;
use XLite\Core\View\DTO\Assets;
use XLite\Core\View\DTO\RenderedWidget;
use XLite\Core\View\DynamicWidgetInterface;
use XLite\Core\View\DynamicWidgetRenderer;
use XLite\Core\View\RenderingContextFactory;
use XLite\Core\View\RenderingContextInterface;
use XLite\Core\WidgetCache;
use XLite\InjectLoggerTrait;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\Core\Handler
{
    use OutputHelpersTrait;
    use InjectLoggerTrait;

    /**
     * Resource types
     */
    public const RESOURCE_JS  = 'js';
    public const RESOURCE_CSS = 'css';

    /**
     * Common widget parameter names
     */
    public const PARAM_TEMPLATE = 'template';
    public const PARAM_METADATA = 'metadata';
    public const PARAM_MODES    = 'modes';

    /**
     *  View list insertation position
     */
    public const INSERT_BEFORE = 'before';
    public const INSERT_AFTER  = 'after';
    public const REPLACE       = 'replace';

    /**
     * Favicon resource short path
     */
    public const FAVICON = 'favicon.ico';

    /**
     * View lists (cache)
     *
     * @var \XLite\View\AView[][]
     */
    protected $viewLists = [];

    /**
     * isCloned
     *
     * @var boolean
     */
    protected $isCloned = false;

    /**
     * Runtime cache for widgets initialization
     */
    protected static $initFlags = [];

    /**
     * Return widget default template
     *
     * @return string
     */
    abstract protected function getDefaultTemplate();

    /**
     * Return list of allowed targets
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return [];
    }

    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return [];
    }

    /**
     * Use current controller context
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $value = parent::__get($name);

        return $value ?? \XLite::getController()->$name;
    }

    /**
     * Use current controller context
     *
     * @param string $method Method name
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }

        $tmpMethod = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($method);

        if (method_exists($this, $tmpMethod)) {
            return call_user_func_array([$this, $tmpMethod], $args);
        }

        if (property_exists($this, $method)) {
            return $this->{$method};
        }

        if ($value = $this->getParam($method)) {
            return $value;
        }

        return call_user_func_array([\XLite::getController(), $method], $args);
    }

    /**
     * Copy widget params
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->getWidgetParams() as $name => $param) {
            $this->widgetParams[$name] = clone $param;
        }

        $this->isCloned = true;
    }

    /**
     * Return widget object
     *
     * @param array  $params Widget params OPTIONAL
     * @param string $class  Widget class OPTIONAL
     *
     * @return \XLite\View\AView
     */
    public function getWidget(array $params = [], $class = null)
    {
        // Create/clone current widget
        $widget = $this->getChildWidget($class, $params);

        // Set param values
        $widget->setWidgetParams($params);

        // Initialize
        $widget->init();

        return $widget;
    }

    /**
     * Get widget by parameters
     *
     * @param array $params Parameters
     *
     * @return \XLite\View\AView
     */
    public function getWidgetByParams(array $params)
    {
        $class = null;
        if (isset($params['class'])) {
            $class = $params['class'];
            unset($params['class']);
        }

        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
            unset($params['name']);
        }

        return $this->getWidget($params, $class, $name);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    public function checkVisibility()
    {
        return $this->isCloned || $this->isVisible();
    }

    /**
     * Mark string value as safe so it won't be (double)escaped in templates
     *
     * @param $string
     *
     * @return object
     */
    protected function getSafeValue($string)
    {
        return new Markup($string, 'UTF-8');
    }

    /**
     * Return unique-guaranteed string to be used as id attr.
     * Returns given string in case of the first call with such argument.
     * Any subsequent calls return as <string>_<prefix>
     *
     * @param string $id Given id string
     *
     * @return object
     */
    protected function getUniqueId($id)
    {
        return \XLite\Core\Layout::getInstance()->getUniqueIdFor($id);
    }

    /**
     * Check visibility, initialize and display widget or fetch it from cache.
     *
     * TODO: remove the ability to override template, use twig's {{ include }} instead.
     *
     * @param string $template Override default template OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        $overrideTemplate = isset($template);
        $originalWidget   = !$this->isCloned && !$overrideTemplate;

        if (
            $this->getRenderingContext()->isBuffering()
            && $this instanceof DynamicWidgetInterface
            && $originalWidget
        ) {
            /** @var AView|DynamicWidgetInterface $this */
            echo $this->getDynamicWidgetRenderer()->getWidgetPlaceholder($this);

            return;
        }

        if ($overrideTemplate || $this->checkVisibility()) {
            if ($originalWidget) {
                $this->initView();
            }

            $cacheEnabled   = $this->isCacheAllowed() && $originalWidget;
            $renderedWidget = $cacheEnabled ? $this->getRenderedWidgetFromCache() : null;

            if ($renderedWidget !== null) {
                $this->displayRenderedWidget($renderedWidget);
            } else {
                if ($cacheEnabled) {
                    $this->getRenderingContext()->startBuffering();
                }

                $this->doDisplay($template);

                if ($cacheEnabled) {
                    $renderedWidget = $this->getRenderingContext()->stopBuffering();

                    $this->storeRenderedWidgetInCache($renderedWidget);

                    $this->displayRenderedWidget($renderedWidget);
                }
            }
        }
    }

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        $templateName = $this->getTemplateName($template);

        $twig = \XCart\Container::getServiceLocator()->getTwig();

        $loader = $twig->getLoader();

        $templatePath = false;
        if ($templateName) {
            $templatePath = $loader->getSourceContext($templateName)->getPath();
        }

        if ($templatePath !== false) {
            // Collect the specific data to send it to the finalizeTemplateDisplay method
            $profilerData = $this->prepareTemplateDisplay($templatePath);

            $globals = $twig->getGlobals();
            $oldThis = array_key_exists('this', $globals) ? $globals['this'] : null;

            $twig->addGlobal('this', $this);

            $twig->display($templateName, ['this' => $this]);

            $twig->addGlobal('this', $oldThis);

            $this->finalizeTemplateDisplay($templatePath, $profilerData);
        } elseif ($this->getDefaultTemplate() !== null) {
            $this->getLogger()->debug(
                sprintf(
                    'Empty compiled template. View class: %s, view main template: %s',
                    get_class($this),
                    $this->getTemplate()
                )
            );
        }
    }

    /**
     * Return viewer output
     *
     * @return string
     */
    public function getContent()
    {
        ob_start();
        $this->display();
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Check for current target
     *
     * @param array $targets List of allowed targets
     *
     * @return boolean
     */
    public static function isDisplayRequired(array $targets)
    {
        return in_array(\XLite\Core\Request::getInstance()->target, $targets, true);
    }

    /**
     * Check for current target
     *
     * @param array $targets List of disallowed targets
     *
     * @return boolean
     */
    public static function isDisplayRestricted(array $targets)
    {
        return in_array(\XLite\Core\Request::getInstance()->target, $targets, true);
    }

    /**
     * Check for current mode
     *
     * @param array $modes List of allowed modes
     *
     * @return boolean
     *
     */
    public function isDisplayRequiredForMode(array $modes)
    {
        return in_array(\XLite\Core\Request::getInstance()->mode, $modes, true);
    }

    /**
     * Get current language
     *
     * @return \XLite\Model\Language
     */
    public function getCurrentLanguage()
    {
        return \XLite\Core\Session::getInstance()->getLanguage();
    }

    /**
     * FIXME - backward compatibility
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function get($name)
    {
        $value = parent::get($name);

        return $value ?? \XLite::getController()->get($name);
    }

    /**
     * Return instance of the child widget
     *
     * @param string $class  Child widget class OPTIONAL
     * @param array  $params Params OPTIONAL
     *
     * @return \XLite\View\AView
     */
    public function getChildWidget($class = null, array $params = [])
    {
        if ($class !== null) {
            /** @var AView $widget */
            $widget = new $class($params);

            $widget->setRenderingContext($this->getRenderingContext());
        } else {
            $widget = clone $this;
        }

        return $widget;
    }

    /**
     * Display rendered widget (html and assets).
     *
     * Dynamic widget placeholders are replaced with actual widget content when widget buffering level is zero.
     *
     * @param RenderedWidget $widget
     */
    protected function displayRenderedWidget(RenderedWidget $widget)
    {
        $renderingContext = $this->getRenderingContext();

        foreach ($widget->assets as $assets) {
            $renderingContext->registerAssets($assets);
        }

        $renderingContext->registerMetaTags($widget->metaTags);

        echo !$renderingContext->isBuffering()
            ? $this->getDynamicWidgetRenderer()->reifyWidgetPlaceholders($this, $widget->content)
            : $widget->content;
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getParam(self::PARAM_TEMPLATE);
    }

    /**
     * Get template name (aka short path)
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return string
     */
    protected function getTemplateName($template = null)
    {
        return $template ?: $this->getTemplate();
    }

    /**
     * Prepare template display
     *
     * @param string $template Template short path
     *
     * @return array
     */
    protected function prepareTemplateDisplay($template)
    {
        return [];
    }

    /**
     * Finalize template display
     *
     * @param string $template     Template short path
     * @param array  $profilerData Profiler data
     *
     * @return void
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        if (!$this->isCloned && $template === null) {
            $this->closeView();
        }
    }

    /**
     * Return list of the modes allowed by default
     *
     * @return array
     */
    protected function getDefaultModes()
    {
        return [];
    }

    /**
     * Flag if the favicon is displayed in the customer area
     * By default the favicon is not displayed
     *
     * @return boolean
     */
    protected function displayFavicon()
    {
        return false;
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_TEMPLATE => new \XLite\Model\WidgetParam\TypeFile('Template', $this->getDefaultTemplate()),
            self::PARAM_METADATA => new \XLite\Model\WidgetParam\TypeCollection('Widget metadata', []),
            self::PARAM_MODES    => new \XLite\Model\WidgetParam\TypeCollection('Modes', $this->getDefaultModes()),
        ];
    }

    /**
     * Check visibility according to the current target
     * todo: must be static
     * todo: move to public section
     *
     * @return boolean
     */
    public static function checkTarget()
    {
        $targets = static::getAllowedTargets();

        return (!((bool) $targets) || static::isDisplayRequired($targets)) && !static::isDisplayRestricted(static::getDisallowedTargets());
    }

    /**
     * Check if current mode is allowable
     *
     * @return boolean
     */
    protected function checkMode()
    {
        $modes = $this->getParam(self::PARAM_MODES);

        return empty($modes) || $this->isDisplayRequiredForMode($modes);
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     */
    protected function initView()
    {
        $cachekey = get_class($this);

        if (!isset(static::$initFlags[$cachekey])) {
            // Add widget resources to the static array
            $this->registerResourcesForCurrentWidget();
            static::$initFlags[$cachekey] = true;
        }

        if ($this instanceof \XLite\Core\PreloadedLabels\ProviderInterface) {
            $data = $this->getPreloadedLanguageLabels();
            \XLite\Core\PreloadedLabels\Registrar::getInstance()->register($data);
        }
    }

    /**
     * Called after the includeCompiledFile()
     *
     * @return void
     */
    protected function closeView()
    {
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return static::checkTarget()
            && $this->checkMode()
            && $this->checkACL();
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return true;
    }

    /**
     * FIXME - must be removed
     *
     * @param string $name Param name
     *
     * @return mixed
     */
    protected function getRequestParamValue($name)
    {
        return \XLite\Core\Request::getInstance()->$name;
    }

    // {{{ Resources (CSS and JS)

    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        return [
            ['getCommonFiles', 100, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON],
            ['getResources', 200, null, null],
            ['getThemeFiles', 300, null, null],
        ];
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * The method must return the array of the CSS file paths:
     *
     * return array(
     *      'modules/Developer/Module/style.css',
     *      'styles/css/main.css',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getCSSFiles(),
     *      array(
     *          'modules/Developer/Module/style.css',
     *          'styles/css/main.css',
     *          ...
     *      )
     * );
     *
     * LESS resource usage:
     * You can also use the less resources along with the CSS ones.
     * The LESS resources will be compiled into CSS.
     * However you can merge your LESS resource with another one using 'merge' parameter.
     * 'merge' parameter must contain the file path to the parent LESS file.
     * In this case the resources will be linked into one LESS file with the '@import' LESS instruction.
     *
     * !Important note!
     * Right now only one parent is supported, so you cannot link the resources in LESS chain.
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its CSS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * The best practice is to merge LESS resources with 'bootstrap/css/bootstrap.less' file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return [];
    }

    /**
     * Via this method the widget registers the JS files which it uses.
     * During the viewers initialization the JS files are collecting into the static storage.
     *
     * The method must return the array of the JS file paths:
     *
     * return array(
     *      'modules/Developer/Module/script.js',
     *      'script/js/main.js',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getJSFiles(),
     *      array(
     *          'modules/Developer/Module/script.js',
     *          'script/js/main.js',
     *          ...
     *      )
     * );
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its JS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * @return array
     */
    public function getJSFiles()
    {
        return [];
    }

    /**
     * Via this method the widget registers the meta tags which it uses.
     * During the viewers initialization the meta tags are collecting into the static storage.
     *
     * The method must return the array of the full meta tag definitions:
     *
     * return array(
     *      '<meta name="name1" content="Content1" />',
     *      '<meta http-equiv="Content-Style-Type" content="text/css">',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getMetaTags(),
     *      array(
     *          '<meta name="name1" content="Content1" />',
     *          '<meta http-equiv="Content-Style-Type" content="text/css">',
     *          ...
     *      )
     * );
     *
     * @return array
     */
    public function getMetaTags()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getVueLibraries()
    {
        return [
            [
                'file'      => LC_DEVELOPER_MODE ? 'vue/vue.js' : 'vue/vue.min.js',
                'no_minify' => true,
            ],
            [
                'file'      => LC_DEVELOPER_MODE ? 'vue/vuex.js' : 'vue/vuex.min.js',
                'no_minify' => true,
            ],
            'vue/vue.loadable.js',
            'vue/vue.registerComponent.js',
            'js/vue/vue.js',
            'js/vue/component.js',
            'js/vue/event_bus.js',
        ];
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        return [
            static::RESOURCE_JS  => static::getVueLibraries(),
            static::RESOURCE_CSS => [],
        ];
    }

    /**
     * Return theme common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        return [
            static::RESOURCE_JS  => [],
            static::RESOURCE_CSS => [],
        ];
    }

    /**
     * Return list of widget resources
     *
     * @return array
     */
    protected function getResources()
    {
        return [
            static::RESOURCE_JS  => $this->getJSFiles(),
            static::RESOURCE_CSS => $this->getCSSFiles(),
        ];
    }

    /**
     * Returns reference to this object
     *
     * @return string
     */
    public function getHashCode()
    {
        return get_class($this);
    }

    /**
     * Return resource structure for validation engine language file.
     * By default there are several ready-to-use language files from validationEngine project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return array
     */
    protected function getValidationEngineLanguageResource()
    {
        return [
            'file'      => 'js/validationEngine.min/languages/jquery.validationEngine-LANGUAGE_CODE.js',
            'filelist'  => [
                $this->getValidationEngineLanguageFile(),
                'js/validationEngine.min/languages/jquery.validationEngine-en.js',
            ],
            'no_minify' => true,
        ];
    }

    /**
     * Return validation engine language file path.
     * By default there are several ready-to-use language files from validationEngine project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return string
     */
    protected function getValidationEngineLanguageFile()
    {
        return 'js/validationEngine.min/languages/jquery.validationEngine-'
            . $this->getCurrentLanguage()->getCode()
            . '.js';
    }

    /**
     * Register widget resources
     *
     * @return void
     */
    protected function registerResourcesForCurrentWidget()
    {
        foreach (static::getResourcesSchema() as $data) {
            [$method, $index, $interface, $zone] = $data;

            $this->registerResources($this->$method(), $index, $interface, $zone, $method);
        }

        $this->registerMetas();
    }

    /**
     * This method collects the JS/CSS resources which are registered by various widgets via
     * methods registered in \XLite\View\AView::getResourcesSchema:
     *
     * getCommonFiles()
     * getResources() (this is a compilation of getJSFiles() / getCSSFiles() methods)
     * getThemeFiles()
     *
     * Every widget to display registers the resources which are collected in the \XLite\Core\Layout static storage.
     * Then these resources are prepared in this method and are ready to use in \XLite\View\AResourcesContainer class.
     * Container class just gets these resources and puts them into the page as a script or CSS files inclusions.
     *
     * This method takes the $resources parameter in the following format:
     * array(
     *  static::RESOURCE_JS => array(
     *      'js_file_path1',
     *      'js_file_path2',
     *      ...
     *  ),
     *  static::RESOURCE_CSS => array(
     *      'css_file_path1',
     *      'css_file_path2',
     *      ...
     *  ),
     * )
     *
     * Note: You can provide more details for the resource if the resource array is provided
     * instead of file path ('js_file_path1'):
     *
     * array(
     *      'file'  => 'resource_file_path',
     *      'media' => 'print'  // for example
     *      'filelist' => array(          // If you use this parameter then the 'file' parameter
     *                                    // is taken as a 'resource name',
     *          'file1_path(real_path)',  // and the real file paths must be provided via 'filelist' parameter
     *      )
     * )
     *
     * $index - parameter is an order_by number which helps to insert the resources into some ordered queue
     *
     * $interface - parameter to inform where the resources are placed.
     *
     * @param array             $resources List of resources to register
     * @param integer           $index     Position in the ordered resources queue
     * @param string            $interface Interface where the resources are placed OPTIONAL
     * @param \XLite\View\AView $owner     Mark resources with this object OPTIONAL
     *
     * @return void
     *
     * @see \XLite\View\AView::registerResourcesForCurrentWidget()
     * @see \XLite\View\AView::initView()
     */
    protected function registerResources(array $resources, $index, $interface = null, $zone = null, $owner = null)
    {
        $this->getRenderingContext()->registerAssets(new Assets($resources, $index, $interface, $zone, $owner));
    }

    /**
     * Method collects the meta definitions (meta tags) into the static meta storage. (the Customer interface only!)
     * Widgets can register the meta they are using via 'getMetaTags()'
     *
     * @return void
     *
     * @see \XLite\View\AView::getMetaTags()
     */
    protected function registerMetas()
    {
        $this->getRenderingContext()->registerMetaTags($this->getMetaTags());
    }

    // }}}

    // {{{ View lists

    /**
     * Display view list content
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return void
     */
    public function displayViewListContent($list, array $arguments = [])
    {
        foreach ($this->getViewList($list, $arguments) as $widget) {
            $widget->display();
        }
    }

    /**
     * Display a nested view list
     *
     * @param string $part   Suffix that should be appended to the name of a parent list (will be delimited with a dot)
     * @param array  $params Widget params OPTIONAL
     *
     * @return void
     */
    public function displayNestedViewListContent($part, array $params = [])
    {
        $this->displayViewListContent($this->getNestedListName($part), $params);
    }

    /**
     * Display a inherited view list
     *
     * @param string $part   Suffix that should be appended to the name of a inherited list
     *                       (will be delimited with a dot)
     * @param array  $params Widget params OPTIONAL
     *
     * @return void
     */
    public function displayInheritedViewListContent($part, array $params = [])
    {
        $this->displayViewListContent($this->getInheritedListName($part), $params);
    }

    /**
     * Combines the nested list name from the parent list name and a suffix
     *
     * @param string $part Suffix to be added to the parent list name
     *
     * @return string
     */
    protected function getNestedListName($part)
    {
        return $this->viewListName ? $this->viewListName . '.' . $part : $part;
    }

    /**
     * Get a nested view list
     *
     * @param string $part      Suffix of the nested list name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return array
     */
    protected function getNestedViewList($part, array $arguments = [])
    {
        return $this->getViewList($this->getNestedListName($part), $arguments);
    }

    /**
     * Combines the inherited list name from the parent list name and a suffix
     *
     * @param string $part Suffix to be added to the inherited list name
     *
     * @return string
     */
    protected function getInheritedListName($part)
    {
        return $this->getListName() ? $this->getListName() . '.' . $part : $part;
    }

    /**
     * Get a inherited view list
     *
     * @param string $part      Suffix of the inherited list name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return array
     */
    protected function getInheritedViewList($part, array $arguments = [])
    {
        return $this->getViewList($this->getInheritedListName($part), $arguments);
    }

    // }}}

    /**
     * Display plain array as JS array
     *
     * @param array $data Plain array
     *
     * @return void
     */
    public function displayCommentedData(array $data)
    {
        if (!empty($data)) {
            echo('<script type="text/x-cart-data">' . "\r\n" . json_encode($data) . "\r\n" . '</script>' . "\r\n");
        }
    }

    /**
     * Format price
     *
     * @param float                 $value        Price
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options)
     *
     * @return string
     */
    public static function formatPrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        if ($currency === null) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        if ($currency->getRoundUp() !== \XLite\Model\Currency::ROUNDUP_NONE && !\XLite::isAdminZone()) {
            $pow   = pow(10, (int) $currency->getRoundUp());
            $value = ceil($value * $pow) / $pow;
        }

        $parts = $currency->formatParts($value);

        if (isset($parts['sign']) && $parts['sign'] === '-') {
            $parts['sign'] = '− ';
        }

        if ($strictFormat) {
            $parts = static::formatPartsStrictly($parts);
        }

        return implode('', $parts);
    }

    /**
     * Format weight
     *
     * @param float $value Weight
     *
     * @return string
     */
    public static function formatWeight($value)
    {
        [$thousandDelimiter, $decimalDelimiter]
            = explode('|', \XLite\Core\Config::getInstance()->Units->weight_format);

        $result = number_format($value, 4, $decimalDelimiter, $thousandDelimiter);

        if (\XLite\Core\Config::getInstance()->Units->weight_trailing_zeroes) {
            $result = rtrim(rtrim($result, '0'), $decimalDelimiter);
        }

        return $result . ' ' . \XLite\Core\Translation::translateWeightSymbol();
    }

    /**
     * Format price as HTML block
     *
     * @param float                 $value        Value
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options)
     *
     * @return string
     */
    public function formatPriceHTML($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        if ($currency === null) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        if ($currency->getRoundUp() !== \XLite\Model\Currency::ROUNDUP_NONE && !\XLite::isAdminZone()) {
            $pow   = pow(10, (int) $currency->getRoundUp());
            $value = ceil($value * $pow) / $pow;
        }

        $parts = $currency->formatParts($value);

        if (isset($parts['sign']) && $parts['sign'] === '-') {
            $parts['sign'] = '&minus;&#8197;';
        }

        if ($strictFormat) {
            $parts = static::formatPartsStrictly($parts);
        }

        foreach ($parts as $name => $value) {
            $class        = 'part-' . $name;
            $parts[$name] = '<span class="' . $class . '">' . func_htmlspecialchars($value) . '</span>';
        }

        return implode('', $parts);
    }

    /**
     * Print tag attributes
     *
     * @param array $attributes Attributes
     *
     * @return string
     */
    protected function printTagAttributes(array $attributes)
    {
        return static::convertToHtmlAttributeString($attributes);
    }

    /**
     * Check - view list is visible or not
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return boolean
     */
    public function isViewListVisible($list, array $arguments = [])
    {
        return 0 < count($this->getViewList($list, $arguments));
    }

    /**
     * Format file size
     *
     * @param integer $size Size in bytes
     *
     * @return string
     */
    protected function formatSize($size)
    {
        if (1024 > $size) {
            $result = static::t('X bytes', ['value' => $size]);
        } elseif (1048576 > $size) {
            $result = static::t('X kB', ['value' => round($size / 1024, 1)]);
        } elseif (1073741824 > $size) {
            $result = static::t('X MB', ['value' => round($size / 1048576, 1)]);
        } else {
            $result = static::t('X GB', ['value' => round($size / 1073741824, 1)]);
        }

        return $result;
    }

    /**
     * Return specific CSS class for dialog wrapper
     *
     * @return string
     */
    protected function getDialogCSSClass()
    {
        return 'dialog-content';
    }

    /**
     * Change parts of format price if it is necessary
     *
     * @param array $parts
     *
     * @return array
     */
    protected static function formatPartsStrictly($parts)
    {
        if (
            \XLite\Core\Config::getInstance()->General->trailing_zeroes == 1
            && $parts['decimal'] == '00'
        ) {
            unset($parts['decimal'], $parts['decimalDelimiter']);
        }

        return $parts;
    }

    /**
     * Build list item class
     *
     * @param string $listName List name
     *
     * @return string
     */
    protected function buildListItemClass($listName)
    {
        $indexName = $listName . 'ArrayPointer';
        $countName = $listName . 'ArraySize';

        $class = [];

        if ($this->$indexName == 1) {
            $class[] = 'first';
        }

        if ($this->$countName == $this->$indexName) {
            $class[] = 'last';
        }

        return implode(' ', $class);
    }

    /**
     * Prepare human-readable output for file size
     * @todo: add twig filter
     *
     * @param integer $size Size in bytes
     *
     * @return string
     */
    protected function formatFileSize($size)
    {
        return \XLite\Core\Converter::formatFileSize($size);
    }

    /**
     * Helper to get array field values
     *
     * @param array  $array Array to get field value
     * @param string $field Field name
     *
     * @return mixed
     */
    protected function getArrayField(array $array, $field)
    {
        return \Includes\Utils\ArrayManager::getIndex($array, $field, true);
    }

    /**
     * Truncates the baseObject property value to specified length
     * @todo: add twig filter
     *
     * @param mixed   $base       String or object instance to get field value from
     * @param mixed   $field      String length or field to get value
     * @param integer $length     Field length to truncate to OPTIONAL
     * @param string  $etc        String to add to truncated field value OPTIONAL
     * @param mixed   $breakWords Word wrap flag OPTIONAL
     *
     * @return string
     */
    protected function truncate($base, $field, $length = 0, $etc = '...', $breakWords = false)
    {
        if (is_scalar($base)) {
            $string = $base;
            $length = $field;
        } else {
            if ($base instanceof \XLite\Model\AEntity) {
                $string = $base->{'get' . \Includes\Utils\Converter::convertToUpperCamelCase($field)}();
            } else {
                $string = $base->get($field);
            }
        }

        if ($length == 0) {
            $string = '';
        } elseif (strlen($string) > $length) {
            $length -= strlen($etc);
            if (!$breakWords) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }

            $string = substr($string, 0, $length) . $etc;
        }

        return $string;
    }

    /**
     * Format date
     * @todo: add twig filter
     *
     * @param mixed  $base   String or object instance to get field value from
     * @param string $field  Field to get value OPTIONAL
     * @param string $format Date format OPTIONAL
     *
     * @return string
     */
    protected function formatDate($base, $field = null, $format = null)
    {
        if (is_object($base)) {
            $base = $base instanceof \XLite\Model\AEntity
                ? $base->$field
                : $base->get($field);
        }

        return \XLite\Core\Converter::formatDate($base, $format);
    }

    /**
     * Format timestamp
     * @todo: add twig filter
     *
     * @param mixed  $base   String or object instance to get field value from
     * @param string $field  Field to get value OPTIONAL
     * @param string $format Date format OPTIONAL
     *
     * @return string
     */
    protected function formatTime($base, $field = null, $format = null)
    {
        if (is_object($base)) {
            $base = $base instanceof \XLite\Model\AEntity
                ? $base->$field
                : $base->get($field);
        }

        return \XLite\Core\Converter::formatTime($base, $format);
    }

    /**
     * Format timestamp as day time
     * @todo: add twig filter
     *
     * @param mixed  $base   String or object instance to get field value from
     * @param string $field  Field to get value OPTIONAL
     * @param string $format Time format OPTIONAL
     *
     * @return string
     */
    protected function formatDayTime($base, $field = null, $format = null)
    {
        if (is_object($base)) {
            $base = $base instanceof \XLite\Model\AEntity
                ? $base->$field
                : $base->get($field);
        }

        return \XLite\Core\Converter::formatDayTime($base, $format);
    }

    /**
     * Add slashes
     * @todo: add twig filter
     *
     * @param mixed  $base  String or object instance to get field value from
     * @param string $field Field to get value OPTIONAL
     *
     * @return string
     */
    protected function addSlashes($base, $field = null)
    {
        return addslashes(is_scalar($base) ? $base : $base->get($field));
    }

    /**
     * Check if data is empty
     *
     * @param mixed $data Data to check
     *
     * @return boolean
     */
    protected function isEmpty($data)
    {
        return empty($data);
    }

    /**
     * Split an array into chunks
     *
     * @param array   $array Array to split
     * @param integer $count Chunks count
     *
     * @return array
     */
    protected function split(array $array, $count)
    {
        $result = array_chunk($array, $count);

        $lastKey   = count($result) - 1;
        $lastValue = $result[$lastKey];

        $count -= count($lastValue);

        if (0 < $count) {
            $result[$lastKey] = array_merge($lastValue, array_fill(0, $count, null));
        }

        return $result;
    }

    /**
     * Increment
     *
     * @param integer $value Value to increment
     * @param integer $inc   Increment OPTIONAL
     *
     * @return integer
     */
    protected function inc($value, $inc = 1)
    {
        return $value + $inc;
    }

    /**
     * For the "zebra" tables
     *
     * @param integer $row          Row index
     * @param string  $oddCSSClass  First CSS class
     * @param string  $evenCSSClass Second CSS class OPTIONAL
     *
     * @return string
     */
    protected function getRowClass($row, $oddCSSClass, $evenCSSClass = null)
    {
        return ($row % 2) === 0 ? $oddCSSClass : $evenCSSClass;
    }

    /**
     * Get view list
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return \XLite\View\AView[]
     */
    protected function getViewList($list, array $arguments = [])
    {
        if (!isset($this->viewLists[$list])) {
            $this->viewLists[$list] = $this->defineViewList($list);
        }

        if (!empty($arguments)) {
            foreach ($this->viewLists[$list] as $widget) {
                $widget->setWidgetParams($arguments);
            }
        }

        $result = [];
        foreach ($this->viewLists[$list] as $widget) {
            if ($widget->checkVisibility() || $widget instanceof DynamicWidgetInterface) {
                $result[] = $widget;
            }
        }

        return $result;
    }

    /**
     * getViewListChildren
     *
     * @param string $list List name
     *
     * @return \XLite\Model\ViewList[]
     */
    protected function getViewListChildren($list)
    {
        $children = \XLite\Core\Database::getRepo('XLite\Model\ViewList')->findClassList(
            $list,
            \XLite\Core\Layout::getInstance()->getInterface(),
            \XLite\Core\Layout::getInstance()->getZone()
        );

        return $children;
    }

    /**
     * addViewListChild
     *
     * @param array   &$list       List to modify
     * @param array    $properties Node properties
     * @param integer  $weight     Node position OPTIONAL
     *
     * @return void
     */
    protected function addViewListChild(array &$list, array $properties, $weight = 0)
    {
        // Search node to insert after
        foreach ($list as $key => $node) {
            if ($node->getWeight() > $weight) {
                break;
            }
        }

        // Prepare properties
        $properties['tpl']    = substr(
            \XLite\Singletons::$handler->layout->getResourceFullPath($properties['tpl']),
            strlen(LC_DIR_SKINS)
        );
        $properties['weight'] = $weight;
        $properties['list']   = $node->getList();

        // Add element to the array
        array_splice($list, $key, 0, [new \XLite\Model\ViewList($properties)]);
    }

    /**
     * Define view list
     *
     * @param string $list List name
     *
     * @return array
     */
    protected function defineViewList($list)
    {
        $widgets = [];

        foreach ($this->getViewListChildren($list) as $widget) {
            /** @var \XLite\View\AView $widgetClass */
            $widgetClass = $widget->getChild();

            if ($widgetClass && $widgetClass::checkTarget()) {
                // List child is widget
                $widgets[] = $this->getWidget(
                    $this->getViewListItemWidgetParams($widget),
                    $widget->getChild()
                );
            } elseif ($widget->getTpl()) {
                // List child is template
                $widgets[] = $this->getWidget(
                    $this->getViewListItemWidgetParams($widget)
                );
            }
        }

        return $widgets;
    }

    /**
     * Returns view list item widget params, used in getWidget call
     *
     * @param \XLite\Model\ViewList $item
     *
     * @return array
     */
    protected function getViewListItemWidgetParams(\XLite\Model\ViewList $item)
    {
        $params = [
            'viewListClass' => $this->getViewListClass(),
            'viewListName'  => $item->getList(),
            'metadata'      => $this->getListItemMetadata($item),
        ];

        if ($item->getTpl()) {
            $params['template'] = $item->getTpl();
        }

        return $params;
    }

    /**
     * Define view list item metadata
     *
     * @param \XLite\Model\ViewList $item ViewList item
     *
     * @return array
     */
    protected function getListItemMetadata($item)
    {
        return ['entityId' => $item->getEntityId() ?: null];
    }

    /**
     * Get view list class name
     *
     * @return string
     */
    protected function getViewListClass()
    {
        return get_class($this);
    }

    /**
     * Get XPath by content
     * @todo: remove -> used only in \XLite\View\AView::insertViewListByXpath()
     *
     * @param string $content Content
     *
     * @return \DOMXPath
     */
    protected function getXpathByContent($content)
    {
        $dom               = new \DOMDocument();
        $dom->formatOutput = true;

        return @$dom->loadHTML($content) ? new \DOMXPath($dom) : null;
    }

    /**
     * Get view list content
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return string
     */
    protected function getViewListContent($list, array $arguments = [])
    {
        ob_start();
        $this->displayViewListContent($list, $arguments);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Get view list content as nodes list
     * @todo: remove -> used only in \XLite\View\AView::insertViewListByXpath()
     *
     * @param string $list List name
     *
     * @return \DOMNamedNodeMap|void
     */
    protected function getViewListContentAsNodes($list)
    {
        $d       = new \DOMDocument();
        $content = $this->getViewListContent($list);
        $result  = null;
        if ($content && @$d->loadHTML($content)) {
            $result = $d->documentElement->childNodes->item(0)->childNodes;
        }

        return $result;
    }

    /**
     * Insert view list by XPath query
     * @todo: check for usage
     *
     * @param string $content        Content
     * @param string $query          XPath query
     * @param string $list           List name
     * @param string $insertPosition Insert position code OPTIONAL
     *
     * @return string
     */
    protected function insertViewListByXpath($content, $query, $list, $insertPosition = self::INSERT_BEFORE)
    {
        $xpath = $this->getXpathByContent($content);
        if ($xpath) {
            $places  = $xpath->query($query);
            $patches = $this->getViewListContentAsNodes($list);
            if (0 < $places->length && $patches && 0 < $patches->length) {
                $this->applyXpathPatches($places, $patches, $insertPosition);
                $content = $xpath->document->saveHTML();
            }
        }

        return $content;
    }

    /**
     * Apply XPath-based patches
     * @todo: remove -> used only in \XLite\View\AView::insertViewListByXpath() (also in Flexy compiler)
     *
     * @param \DOMNamedNodeMap $places         Patch placeholders
     * @param \DOMNamedNodeMap $patches        Patches
     * @param string           $baseInsertType Patch insert type
     *
     * @return void
     */
    protected function applyXpathPatches(\DOMNamedNodeMap $places, \DOMNamedNodeMap $patches, $baseInsertType)
    {
        foreach ($places as $place) {
            $insertType = $baseInsertType;
            foreach ($patches as $node) {
                $node = $node->cloneNode(true);

                if ($insertType === static::INSERT_BEFORE) {
                    // Insert patch node before XPath result node
                    $place->parentNode->insertBefore($node, $place);
                } elseif ($insertType === static::INSERT_AFTER) {
                    // Insert patch node after XPath result node
                    if ($place->nextSibling) {
                        $place->parentNode->insertBefore($node, $place->nextSibling);
                        $insertType = self::INSERT_BEFORE;
                        $place      = $place->nextSibling;
                    } else {
                        $place->parentNode->appendChild($node);
                    }
                } elseif ($insertType === static::REPLACE) {
                    // Replace XPath result node to patch node
                    $place->parentNode->replaceChild($node, $place);

                    if ($node->nextSibling) {
                        $place      = $node->nextSibling;
                        $insertType = self::INSERT_BEFORE;
                    } else {
                        $place      = $node;
                        $insertType = self::INSERT_AFTER;
                    }
                }
            }
        }
    }

    /**
     * Insert view list by regular expression pattern
     * @todo: check for usage
     *
     * @param string $content Content
     * @param string $pattern Pattern (PCRE)
     * @param string $list    List name
     * @param string $replace Replace pattern OPTIONAL
     *
     * @return string
     */
    protected function insertViewListByPattern($content, $pattern, $list, $replace = '%s')
    {
        return preg_replace(
            $pattern,
            sprintf($replace, $this->getViewListContent($list)),
            $content
        );
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return null;
    }

    /**
     * getNamePostedData
     *
     * @param string  $field Field name
     * @param integer $id    Model object ID OPTIONAL
     *
     * @return string
     */
    protected function getNamePostedData($field, $id = null)
    {
        $args  = func_get_args();
        $field = $args[0];
        $tail  = '';

        if (2 <= count($args)) {
            $id = $args[1];
        }

        if (2 < count($args)) {
            $tail = '[' . implode('][', array_slice($args, 2)) . ']';
        }

        return $this->getPrefixPostedData() . ($id !== null ? '[' . $id . ']' : '') . '[' . $field . ']' . $tail;
    }

    /**
     * getNameToDelete
     *
     * @param integer $id Model object ID
     *
     * @return string
     */
    protected function getNameToDelete($id)
    {
        return $this->getPrefixSelected() . '[' . $id . ']';
    }

    /**
     * Checks if specific developer mode is defined
     *
     * @return boolean
     */
    protected function isDeveloperMode()
    {
        return LC_DEVELOPER_MODE;
    }

    /**
     * Checks if update notifications are enabled.
     *
     * @return boolean
     */
    protected function isUpgradeFunctionalityDisplayed()
    {
        return \XLite::areUpdateNotificationsEnabled();
    }

    /**
     * Checks if rendering is done for mobile device
     *
     * @return boolean
     */
    protected function isMobileDevice()
    {
        return \XLite\Core\Request::isMobileDevice();
    }

    /**
     * Checks if rendering is done for mobile device
     *
     * @return boolean
     */
    protected function isTablet()
    {
        return \XLite\Core\Request::isTablet();
    }

    /**
     * @return string
     */
    public function getAjaxPrefix()
    {
        $result = '';

        if (
            LC_USE_CLEAN_URLS
            && \XLite\Core\Router::getInstance()->isUseLanguageUrls()
            && !\XLite::isAdminZone()
        ) {
            $language = \XLite\Core\Session::getInstance()->getLanguage();
            if ($language && !$language->getDefaultAuth()) {
                $result = $language->getCode();
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isCleanUrlsEnabled()
    {
        return LC_USE_CLEAN_URLS;
    }

    /**
     * @return string
     */
    public function cleansUrlsBase()
    {
        return \XLite::getCustomerScript();
    }

    // }}}

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return \XLite\Core\Layout::getInstance()->getLogo();
    }

    /**
     * Get logo alt
     *
     * @return string
     */
    public function getLogoAlt()
    {
        return \XLite\Core\Layout::getInstance()->getLogoAlt();
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $url = \XLite\Core\Layout::getInstance()->getAppleIcon();

        $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']) . '/';
        if (substr($url, 0, strlen($webDir)) !== $webDir) {
            $url = $webDir . $url;
        }

        return $url;
    }

    /**
     * Return favicon resource path
     *
     * @return string
     */
    protected function getFavicon()
    {
        $url = \XLite\Core\Layout::getInstance()->getFavicon();

        if (\Includes\Utils\ConfigParser::getOptions(['host_details', 'public_dir'])) {
            $url = 'public/' . $url;
        }

        $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']) . '/';
        if (substr($url, 0, strlen($webDir)) !== $webDir) {
            $url = $webDir . $url;
        }

        return $url;
    }

    /**
     * Get invoice logo
     *
     * @return string
     */
    public function getInvoiceLogo()
    {
        return \XLite\Core\Layout::getInstance()->getInvoiceLogo();
    }

    /**
     * Return specific data for address entry. Helper.
     *
     * @param \XLite\Model\Address $address   Address
     * @param boolean              $showEmpty Show empty fields OPTIONAL
     *
     * @return array
     */
    protected function getAddressSectionData(\XLite\Model\Address $address = null, $showEmpty = false)
    {
        $result = [];

        if (!$address) {
            return $result;
        }

        $hasStates = $address->getCountry() ? $address->getCountry()->hasStates() : false;

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
            $method            = 'get'
                . \Includes\Utils\Converter::convertToUpperCamelCase(
                    $field->getViewGetterName() ?: $field->getServiceName()
                );
            $addressFieldValue = $address->{$method}();
            $cssFieldName      = $field->getCSSFieldName();

            switch ($field->getServiceName()) {
                case 'state_id':
                    $addressFieldValue = $hasStates ? $addressFieldValue : null;
                    if ($addressFieldValue === null && $hasStates) {
                        $addressFieldValue = $address->getCustomState();
                    }
                    break;

                case 'custom_state':
                    $addressFieldValue = $hasStates ? null : $address->getCustomState();
                    $cssFieldName      = $hasStates ? $cssFieldName : 'address-state';
                    break;
                default:
            }

            if (strlen($addressFieldValue) || $showEmpty) {
                $result[$field->getServiceName()] = [
                    'css_class' => $cssFieldName,
                    'title'     => $field->getName(),
                    'value'     => $addressFieldValue,
                ];
            }
        }

        return $result;
    }

    /**
     * Get escaped widget parameter
     * @todo: check for usage
     *
     * @param string $name Parameters name
     *
     * @return string
     */
    protected function getEscapedParam($name)
    {
        $value = $this->getParam($name);

        return func_htmlspecialchars($value);
    }

    // {{{ SVG

    /**
     * Get SVG image
     *
     * @param string $path      Path
     *
     * @return string
     */
    public function getSVGImage($path)
    {
        $content = null;

        $path = \XLite\Core\Layout::getInstance()->getResourceFullPath($path);

        if ($path && file_exists($path)) {
            $content = file_get_contents($path);
            $content = preg_replace(
                [
                    '/<\?xml [^>]+>/Ssi',
                    '/<!ENTITY [^>]+>/Ss',
                    '/<!DOCTYPE svg [^>]*>/Ssi',
                    '/<metadata>.+<\/metadata>/Ss',
                    '/xmlns:(?:x|i|graph)="[^"]+"/Ss',
                    '/>\s+</Ss',
                    '/<!--\s.+\s-->/USs',
                ],
                ['', '', '', '', '', '><', ''],
                $content
            );
            $content = trim($content);
        }

        return $content;
    }

    /**
     * Display SVG image
     * @todo: add twig filter or function
     *
     * @param string $path Path
     *
     * @return void
     */
    protected function displaySVGImage($path)
    {
        print $this->getSVGImage($path);
    }

    // }}}

    // {{{ Widget Cache todo: remove with trait

    /**
     * Get widget cache instance
     *
     * @return WidgetCache
     */
    protected function getCache()
    {
        /** @var WidgetCache $widgetCache */
        $widgetCache = \XCart\Container::getContainer()->get(WidgetCache::class);

        return $widgetCache;
    }

    /**
     * Check cached content
     *
     * @return boolean
     */
    public function hasCachedContent()
    {
        return $this->isCacheAllowed() && $this->getCache()->has($this->getCacheParameters());
    }

    /**
     * Cache allowed
     *
     * @return boolean
     */
    protected function isCacheAllowed()
    {
        return $this->isCacheAvailable()
            && \XLite\Core\Config::getInstance()->Performance->use_view_cache;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return false;
    }

    /**
     * Get cached widget html and assets.
     *
     * @return RenderedWidget rendered widget data transfer object
     */
    protected function getRenderedWidgetFromCache()
    {
        return $this->getCache()->get($this->getCacheParameters());
    }

    /**
     * Store widget html and assets in cache.
     *
     * @param RenderedWidget $widget rendered widget data transfer object
     *
     * @return void
     */
    protected function storeRenderedWidgetInCache(RenderedWidget $widget)
    {
        $this->getCache()->set($this->getCacheParameters(), $widget, $this->getCacheTTL());
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        return [
            \Includes\Utils\URLManager::isHTTPS(),
            \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
            \XLite\Core\Config::getInstance()->General->default_language,
            substr(get_called_class(), 6),
        ];
    }

    /**
     * Get cache TTL (seconds)
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return null;
    }

    /**
     * Execute callable and cache the return value (or retrieve the value from cache).
     * Caching policy matches the current widget's caching policy.
     *
     * @param callable $function      Function being cached
     * @param array    $cacheKeyParts Array of strings that will be concatenated and used as a key
     *
     * @return mixed
     */
    protected function executeCached(callable $function, array $cacheKeyParts)
    {
        $result = null;

        array_push($cacheKeyParts, 'executeCachedDoNotIntersectPlease');

        if ($this->isCacheAllowed()) {
            if ($this->getCache()->has($cacheKeyParts)) {
                $result = $this->getCache()->get($cacheKeyParts);
            } else {
                $result = $function();

                $this->getCache()->set($cacheKeyParts, $result, $this->getCacheTTL());
            }
        } else {
            $result = $function();
        }

        return $result;
    }

    /**
     * Get an instance of CacheKeyPartsGenerator
     *
     * @return CacheKeyPartsGenerator
     */
    protected function getCacheKeyPartsGenerator()
    {
        /** @var CacheKeyPartsGenerator $widgetCache */
        $cacheKeyPartsGenerator = \XCart\Container::getContainer()->get(CacheKeyPartsGenerator::class);

        return $cacheKeyPartsGenerator;
    }

    // }}}

    // {{{ Service

    /**
     * Get view class name as keys list
     *
     * @return array
     */
    protected function getViewClassKeys()
    {
        return \XLite\Core\Operator::getInstance()->getClassNameAsKeys(get_called_class());
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return 'block block-block';
    }

    /**
     * Return affiliate URL
     * @todo: add twig function
     *
     * @param string $url Url part to add OPTIONAL
     *
     * @return string
     */
    protected function getXCartURL($url = '')
    {
        return \XLite::getXCartURL($url);
    }

    /**
     * Defines the admin URL
     * @todo: add twig function
     *
     * @return string
     */
    protected function getAdminURL()
    {
        return \XLite::getInstance()->getShopURL(\XLite::getAdminScript());
    }

    /**
     * Admin authorization flow
     * Login / Password recovery
     *
     * @return boolean
     */
    protected function isAdminAuthorizationFlow()
    {
        return \XLite::isAdminZone()
            && (
                !\XLite\Core\Auth::getInstance()->isLogged()
                || !\XLite\Core\Auth::getInstance()->isAdmin()
                || $this->isForceChangePassword()
            );
    }

    /**
     * Get user role to define if user is admin
     *
     * @return string
     */
    protected function getLoggedUserRole()
    {
        if (!\XLite\Core\Auth::getInstance()->isLogged()) {
            return 'unauthorized';
        }

        return  \XLite\Core\Auth::getInstance()->isAdmin() ? 'admin' : 'customer';
    }

    public static function getMarketplaceApiUrl(): string
    {
        $marketplace = ConfigParser::getOptions(['marketplace']) ?? [];

        return $marketplace['url'] ?? '';
    }

    /**
     * Define the tag name
     * Currently we use the rule: '<tagName>' must have 'tag-<tagName>' language variable or it will be proceeded as is
     *
     * @param string $tag
     *
     * @return string
     */
    protected function getTagName($tag)
    {
        $label       = 'tag-' . $tag;
        $translation = (string) static::t($label);

        return ($translation === $label) ? $tag : $translation;
    }

    /**
     * Get view class name as keys list
     *
     * @param string $className Class name
     *
     * @return string
     */
    protected function formatClassNameToString($className)
    {
        return str_replace('\\', '', $className);
    }

    /**
     * Get translated weight symbol
     *
     * @return string
     */
    protected function getWeightSymbol()
    {
        return \XLite\Core\Translation::translateWeightSymbol();
    }

    /**
     * Get translated dim symbol
     *
     * @return string
     */
    protected function getDimSymbol()
    {
        return \XLite\Core\Translation::translateDimSymbol();
    }

    // }}}

    // {{{ Rendering context

    /** @var RenderingContextInterface */
    protected $renderingContext;

    /**
     * @return RenderingContextInterface
     */
    protected function getRenderingContext()
    {
        if ($this->renderingContext === null) {
            $this->renderingContext = RenderingContextFactory::createContext();
        }

        return $this->renderingContext;
    }

    public function setRenderingContext(RenderingContextInterface $renderingContext)
    {
        $this->renderingContext = $renderingContext;
    }

    // }}}

    /**
     * @return DynamicWidgetRenderer
     */
    protected function getDynamicWidgetRenderer()
    {
        /** @var DynamicWidgetRenderer $widgetCache */
        $dynamicWidgetRenderer = \XCart\Container::getContainer()->get(DynamicWidgetRenderer::class);

        return $dynamicWidgetRenderer;
    }
}
