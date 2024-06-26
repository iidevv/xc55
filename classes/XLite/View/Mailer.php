<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use PHPMailer\PHPMailer\OAuth;
use XCart\Messenger\OAuthProviderFactory;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Mail\AMail;
use XLite\Core\Mailer\Entries;
use XLite\InjectLoggerTrait;
use XLite\View\FormField\Select\AuthEmailFrom;
use XLite\View\FormField\Select\EmailFrom;

/**
 * Mailer
 */
class Mailer extends \XLite\View\AView
{
    use ExecuteCachedTrait;
    use InjectLoggerTrait;

    public const CRLF = "\r\n";

    public const ATTACHMENT_ENCODING = 'base64';

    /**
     * Subject template file name
     *
     * @var string
     */
    protected $subjectTemplate = 'common/subject.twig';

    /**
     * Body template file name
     *
     * @var string
     */
    protected $bodyTemplate = 'body.twig';

    /**
     * Layout template file name
     *
     * @var string
     */
    protected $layoutTemplate = 'common/layout.twig';

    /**
     * Language locale (for PHPMailer)
     *
     * @var string
     */
    protected $langLocale = 'en';

    /**
     * PHPMailer object
     *
     * @var \PHPMailer
     */
    protected $mail;

    /**
     * Message charset
     *
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * Current template
     *
     * @var string
     */
    protected $template;

    /**
     * Embedded images list
     *
     * @var array
     */
    protected $images = [];

    /**
     * Image parser
     *
     * @var null|\XLite\Model\MailImageParser
     */
    protected $imageParser;

    /**
     * Error log message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Error message set by PHPMailer class
     *
     * @var string
     */
    protected $errorInfo;

    /**
     * Embedded file attachments list
     *
     * @var array
     */
    protected $attachments = [];

    /**
     * Embedded string attachments list
     *
     * @var array
     */
    protected $stringAttachments = [];

    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => [
                'mail/core.less',
            ],
        ]);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'common/style.less';

        return $list;
    }

    /**
     * Setter
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     *
     * @return void
     */
    public function set($name, $value)
    {
        if (in_array($name, ['from'], true)) {
            $value = $this->prepareAddress($value);
        }

        parent::set($name, $value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function prepareAddress($value)
    {
        if ($value instanceof Entries) {
            return $value;
        }

        /**
         * Prevent the attack works by placing a newline character
         * (represented by \n in the following example) in the field
         * that asks for the user's e-mail address.
         * For instance, they might put:
         * joe@example.com\nCC: victim1@example.com,victim2@example.com
         */
        $value = str_replace('\t', "\t", $value);
        $value = str_replace("\t", '', $value);
        $value = str_replace('\r', "\r", $value);
        $value = str_replace("\r", '', $value);
        $value = str_replace('\n', "\n", $value);
        $value = explode("\n", $value);

        return $value[0];
    }

    /**
     * Set subject template
     *
     * @param string $template Template path
     *
     * @return void
     */
    public function setSubjectTemplate($template)
    {
        $this->subjectTemplate = $template;
    }

    /**
     * Set layout template
     *
     * @param string $template Template path
     *
     * @return void
     */
    public function setLayoutTemplate($template)
    {
        $this->layoutTemplate = $template;
    }

    /**
     * Add attachment to mailer
     *
     * @param string $path     Full path to file
     * @param string $name     Filename in mail OPTIONAL
     * @param string $encoding File encoding (default: 'base64') OPTIONAL
     * @param string $mime     File MIME-type (default: 'Application/octet-stream') OPTIONAL
     *
     * @return void
     */
    public function addAttachment($path, $name = '', $encoding = null, $mime = '')
    {
        $attachments = $this->get('attachments');

        $encoding = $encoding ?: static::ATTACHMENT_ENCODING;

        $file = ['path' => $path, 'name' => $name, 'encoding' => $encoding, 'mime' => $mime];

        $attachments[] = $file;

        $this->set('attachments', $attachments);
    }

    /**
     * Add attachment to mailer
     *
     * @param string $string   String contents
     * @param string $name     Filename in mail OPTIONAL
     * @param string $encoding File encoding (default: 'base64') OPTIONAL
     * @param string $mime     File MIME-type (default: 'Application/octet-stream') OPTIONAL
     *
     * @return void
     */
    public function addStringAttachment($string, $name = '', $encoding = null, $mime = '')
    {
        $attachments = $this->get('stringAttachments');

        $encoding = $encoding ?: static::ATTACHMENT_ENCODING;

        $file = ['string' => $string, 'name' => $name, 'encoding' => $encoding, 'mime' => $mime];

        $attachments[] = $file;

        $this->set('stringAttachments', $attachments);
    }

    /**
     * Clear string attachments from mailer
     *
     * @return void
     */
    public function clearStringAttachments()
    {
        $this->set('stringAttachments', []);
    }

    /**
     * Clear attachments from mailer
     *
     * @return void
     */
    public function clearAttachments()
    {
        $this->set('attachments', []);
    }

    /**
     * @param AMail    $mail
     * @param \Closure $populateVariables
     */
    public function compose(AMail $mail, \Closure $populateVariables)
    {
        \XLite\Core\Translation::setTmpTranslationCode($mail->getLanguageCode());

        $this->set('from', $mail->getFrom());
        $this->set('to', $mail->getTo());
        $this->set('dir', $mail::getDir());
        $this->set('hideCompanyInSubject', $mail->getData()['hideCompanyInSubject'] ?? false);
        $subject = $this->compile($this->get('subjectTemplate'), $mail::getZone());
        $subject = $populateVariables($subject);

        $this->set('subject', $subject);

        $body = $this->compile($this->get('layoutTemplate'), $mail::getZone(), true);
        $this->set('body', $body);

        $body = $populateVariables($body);

        $fname = tempnam(LC_DIR_COMPILE, 'mail');

        file_put_contents($fname, $body);

        $this->imageParser         = new \XLite\Model\MailImageParser();
        $this->imageParser->webdir = \XLite::getInstance()->getShopURL('/', false);
        $this->imageParser->parse($fname);

        $this->set('body', $this->imageParser->result);
        $this->set('images', $this->imageParser->images);

        ob_start();
        $this->initMailFromConfig();

        $this->initMailFromSet();

        $this->populateReplyTo($mail);

        $output = ob_get_contents();
        ob_end_clean();

        if ($output !== '') {
            $this->getLogger()->debug('Mailer echoed: "' . $output . '". Error: ' . $this->mail->ErrorInfo);
        }

        // Check if there is any error during mail composition. Log it.
        if ($this->mail->isError()) {
            $this->getLogger()->debug('Compose mail error: ' . $this->mail->ErrorInfo);
        }

        if (file_exists($fname)) {
            unlink($fname);
        }

        \XLite\Core\Translation::setTmpTranslationCode('');
    }

    protected function populateReplyTo(AMail $mail)
    {
        $this->mail->clearReplyTos();
        foreach ($mail->getReplyTo() as $replyTo) {
            if (is_array($replyTo)) {
                if (isset($replyTo['address'], $replyTo['name'])) {
                    $this->mail->addReplyTo($replyTo['address'], $replyTo['name']);
                } else {
                    $this->mail->addReplyTo(reset($replyTo));
                }
            } else {
                $this->mail->addReplyTo($replyTo);
            }
        }
    }

    /**
     * Send message
     *
     * @return boolean
     * @throws \phpmailerException
     */
    public function send()
    {
        $result       = false;
        $errorMessage = null;

        if ($this->get('to') === '') {
            $errorMessage = 'Send mail FAILED: sender address is empty';
        } elseif ($this->mail === null) {
            $errorMessage = 'Send mail FAILED: not initialized inner mailer';
        } else {
            $this->errorInfo = null;

            ob_start();
            $this->mail->send();
            $error = ob_get_contents();
            ob_end_clean();

            // Check if there are any error during mail sending
            if ($this->mail->isError()) {
                $errorMessage = 'Send mail FAILED: ' . $this->prepareErrorMessage($this->mail->ErrorInfo) . PHP_EOL
                    . $this->prepareErrorMessage($error);

                $this->errorInfo = $this->mail->ErrorInfo;
            } else {
                $result = true;
            }
        }

        if ($errorMessage) {
            $this->errorMessage = $errorMessage;
            $this->getLogger()->error($errorMessage);
        }

        $this->imageParser->unlinkImages();

        return $result;
    }

    /**
     * Return description of the last occurred error (log)
     *
     * @return string
     */
    public function getLastErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Return description of the last occurred error
     *
     * @return string|null
     */
    public function getLastError()
    {
        return $this->errorInfo;
    }

    /**
     * Get language code
     * Note: for admin's emails always use default_admin_language from the store settings
     *
     * @param string $interface Recipient type OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    public function getLanguageCode($interface = \XLite::ZONE_CUSTOMER, $code = '')
    {
        if (!$code) {
            $code = $interface === \XLite::ZONE_CUSTOMER
                ? Config::getInstance()->General->default_language
                : Config::getInstance()->General->default_admin_language;
        }

        return $code;
    }

    /**
     * Get body class
     *
     * @return string
     */
    protected function getMailBodyClass()
    {
        return implode(' ', $this->defineBodyClasses());
    }

    /**
     * The layout defines the specific CSS classes for the 'body' tag
     * The body CSS classes can define:
     *
     * AREA: area-a / area-c
     * SKINS that are added to this interface: skin-<skin1>, skin-<skin2>, ...
     *
     * @return array Array of CSS classes to apply to the 'body' tag
     */
    protected function defineBodyClasses()
    {
        $classes = [
            'area-' . (\XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_ADMIN ? 'a' : 'c'),
        ];

        return $classes;
    }

    /**
     * Before using the CSS class in the 'class' attribute it must be prepared to be valid
     * The restricted symbols are changed to '-'
     *
     * @param string $class CSS class name to be prepared
     *
     * @return string
     *
     * @see \XLite\View\AView::defineBodyClasses()
     */
    protected function prepareCSSClass($class)
    {
        return preg_replace('/[^a-z0-9_-]+/Si', '-', $class);
    }

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->template;
    }

    /**
     * Create alternative message body (text/plain)
     *
     * @param string $html Message body
     *
     * @return string
     */
    protected function createAltBody($html)
    {
        $transTbl           = array_flip(get_html_translation_table(HTML_ENTITIES));
        $transTbl['&nbsp;'] = ' '; // Default: ['&nbsp;'] = 0xa0 (in ISO-8859-1)

        // remove style tag with all content
        $html = preg_replace('/<style.*<\/style>/sSU', '', $html);

        // remove html tags & convert html entities to chars
        $txt = strtr(strip_tags($html), $transTbl);

        $txt = preg_replace_callback(
            '/&#\d+;/',
            static function ($m) {
                return mb_convert_encoding($m[0], 'UTF-8', 'HTML-ENTITIES');
            },
            $txt
        );

        return preg_replace('/^\s*$/m', '', $txt);
    }

    protected function getFromMail()
    {
        switch (Config::getInstance()->Email->mail_from_type) {
            case EmailFrom::OPTION_FROM_SERVER:
                return null;
            case EmailFrom::OPTION_MANUAL:
                return Config::getInstance()->Email->mail_from_manual;
            default:
                $from = $this->get('from');

                return $from instanceof Entries
                    ? $from->getFrom()
                    : $from;
        }
    }

    /**
     * Inner mailer initialization from set variables
     */
    protected function initMailFromSet()
    {
        $this->mail->setLanguage(
            $this->get('langLocale')
        );

        $this->mail->CharSet = $this->get('charset');
        $fromMail            = $this->getFromMail();
        if (!empty($fromMail)) {
            $this->mail->From   = $fromMail;
            $this->mail->Sender = $fromMail;
        }

        $this->mail->FromName = $this->get('fromName') ?: $fromMail;

        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
        $this->mail->clearCustomHeaders();

        foreach ($this->get('to') as $email) {
            $value = $this->prepareAddress($email['email']);
            $name  = !empty($email['name']) ? $this->prepareAddress($email['name']) : $value;
            $this->mail->addAddress($value, $name);
        }

        $this->mail->Subject = $this->get('subject');
        $this->mail->AltBody = $this->createAltBody($this->get('body'));
        $this->mail->Body    = $this->get('body');

        if (is_array($this->get('images'))) {
            foreach ($this->get('images') as $image) {
                // Append to $attachment array
                $this->mail->addEmbeddedImage(
                    $image['path'],
                    $image['name'] . '@mail.lc',
                    $image['name'],
                    'base64',
                    $image['mime']
                );
            }
        }

        $attachments = $this->get('attachments');

        if (is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $file) {
                $this->mail->addAttachment($file['path'], $file['name'], $file['encoding'], $file['mime']);
            }
        }
        $this->set('attachments', []);

        $attachments = $this->get('stringAttachments');

        if (is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $file) {
                $this->mail->addStringAttachment($file['string'], $file['name'], $file['encoding'], $file['mime']);
            }
        }
        $this->set('stringAttachments', []);
    }

    public function __construct(array $params = [])
    {
        parent::__construct($params);
    }

    /**
     * Inner mailer initialization from DB configuration
     *
     * @return void
     */
    protected function initMailFromConfig()
    {
        if ($this->mail === null) {
            $this->mail = new \PHPMailer\PHPMailer\PHPMailer();
            // SMTP settings
            if (Config::getInstance()->Email->use_smtp) {
                $this->mail->Mailer = 'smtp';
                $this->mail->Host   = Config::getInstance()->Email->smtp_server_url;
                $this->mail->Port   = Config::getInstance()->Email->smtp_server_port;

                $authMode = Config::getInstance()->Email->smtp_auth_mode;

                if ($authMode === AuthEmailFrom::AUTH_CUSTOM) {
                    if (
                        !empty(Config::getInstance()->Email->smtp_username)
                        && !empty(Config::getInstance()->Email->smtp_password)
                    ) {
                        $this->mail->SMTPAuth = true;
                        $this->mail->Username = Config::getInstance()->Email->smtp_username;
                        $this->mail->Password = Config::getInstance()->Email->smtp_password;
                    }

                    if (
                        in_array(Config::getInstance()->Email->smtp_security, ['ssl', 'tls'], true)
                    ) {
                        $this->mail->SMTPSecure = Config::getInstance()->Email->smtp_security;
                    }
                } else {
                    $this->mail->SMTPAuth = true;
                    $this->mail->AuthType = 'XOAUTH2';
                    $this->mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;

                    $oAuthConfig = OAuthProviderFactory::create(
                        $authMode,
                        Config::getInstance()->Email->smtp_client_id,
                        Config::getInstance()->Email->smtp_secret_key,
                        $this->buildURL('email_settings')
                    );

                    $this->mail->Host   = $oAuthConfig->getHost();
                    $this->mail->Port   = $oAuthConfig->getPort();

                    $tokenData = \json_decode(Config::getInstance()->Email->smtp_auth_token, false);

                    if (!empty($tokenData)) {
                        $this->mail->setOAuth(
                            new OAuth(
                                [
                                    'provider'     => $oAuthConfig->getProvider(),
                                    'clientId'     => Config::getInstance()->Email->smtp_client_id,
                                    'clientSecret' => Config::getInstance()->Email->smtp_secret_key,
                                    'refreshToken' => $tokenData->refresh_token,
                                    'userName'     => $this->get('from')
                                ]
                            )
                        );
                    }
                }
            }

            $this->mail->SMTPDebug = true;
            $this->mail->isHTML();
            $this->mail->Encoding = 'base64';
        }
    }

    /**
     * Compile template
     *
     * @param string $template  Template path
     * @param string $interface Interface OPTIONAL
     * @param bool   $inline
     *
     * @return string
     */
    protected function compile($template, $zone = \XLite::ZONE_CUSTOMER, $inline = false)
    {
        // replace layout with mailer skinned
        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        return $layout->callInInterfaceZone(function () use ($template, $inline) {
            $this->widgetParams[static::PARAM_TEMPLATE]->setValue($template);
            $this->template = $template;
            $this->init();

            $text = $this->getContent();

            if ($inline) {
                $text = $this->convertToInline($text);
            }

            return $text;
        }, \XLite::INTERFACE_MAIL, $zone);
    }

    /**
     * Convert html to inline
     *
     * @param string $html Initial HTML
     *
     * @return string
     */
    protected function convertToInline($html)
    {
        if (!$html || !class_exists('DOMDocument')) {
            return $html;
        }

        $styleFiles = \XLite\Core\Layout::getInstance()
            ->getRegisteredResourcesByType(\XLite\View\AView::RESOURCE_CSS);
        array_unshift($styleFiles, 'reset.css');

        $cssToInlineStyles = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();

        return $cssToInlineStyles->convert(
            $html,
            $this->getStylesAsCSSString($styleFiles)
        );
    }

    /**
     * Get CSS string from passed array of files
     *
     * @param array $styles Style files
     *
     * @return string
     */
    protected function getStylesAsCSSString($styles)
    {
        $result = '';

        foreach ($styles as $file) {
            $path = $this->getStyleFilePath($file);

            if (!$path) {
                continue;
            }

            $fileZone = $file['zone'] ?? \XLite\Core\Layout::getInstance()->getZone();

            $result .= $this->getStyleFileContent($path, $fileZone);
        }

        return $result;
    }

    /**
     * Get style file path
     *
     * @param $fileNode string|array
     *
     * @return string|null
     */
    protected function getStyleFilePath($fileNode)
    {
        if (is_array($fileNode)) {
            return isset($fileNode['original'])
                ? \XLite\Core\Layout::getInstance()->getResourceFullPath($fileNode['original'])
                : null;
        }

        return \XLite\Core\Layout::getInstance()->getResourceFullPath($fileNode);
    }

    /**
     * Get style file content
     *
     * @param string $path Path to style file
     * @param string $zone
     *
     * @return string
     */
    protected function getStyleFileContent($path, $zone)
    {
        $pathinfo = pathinfo($path);

        $result = '';
        if (
            isset($pathinfo['extension'])
            && $pathinfo['extension'] === 'less'
        ) {
            $lessRaw = \XLite\Core\LessParser::getInstance()
                ->makeCSS(
                    [
                        [
                            'file'     => $path,
                            'original' => $path,
                            'less'     => true,
                            'media'    => 'all',
                            'zone'     => $zone,
                        ],
                    ]
                );
            if ($lessRaw && isset($lessRaw['file'])) {
                $result = \Includes\Utils\FileManager::read($lessRaw['file']);
            }
        } else {
            $result = \Includes\Utils\FileManager::read($path);
        }

        return $result;
    }

    /**
     * Get headers as string
     *
     * @return string
     */
    protected function getHeaders()
    {
        $headers = '';
        foreach ($this->headers as $name => $value) {
            $headers .= $name . ': ' . $value . self::CRLF;
        }

        return $headers;
    }

    /**
     * Prepare error message
     *
     * @param string $message Message
     *
     * @return string
     */
    protected function prepareErrorMessage($message)
    {
        return trim(strip_tags($message));
    }

    /**
     * Returns notification by current dir
     *
     * @return \XLite\Model\Notification
     */
    protected function getNotification()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Notification')
            ->findOneByTemplatesDirectory($this->get('dir'));
    }

    /**
     * Returns subject for current notification
     *
     * @return string
     */
    protected function getNotificationSubject()
    {
        $result       = '';
        $notification = $this->getNotification();

        if ($notification) {
            switch (\XLite\Core\Layout::getInstance()->getZone()) {
                case \XLite::ZONE_CUSTOMER:
                    $result = $notification->getCustomerSubject();
                    break;

                case \XLite::ZONE_ADMIN:
                    $result = $notification->getAdminSubject();
                    break;

                default:
                    break;
            }
        }

        return $result;
    }

    /**
     * Returns text for current notification
     *
     * @return string
     */
    protected function getNotificationText()
    {
        $result       = '';
        $notification = $this->getNotification();

        if ($notification) {
            switch (\XLite\Core\Layout::getInstance()->getZone()) {
                case \XLite::ZONE_CUSTOMER:
                    $result = $notification->getCustomerText();
                    break;

                case \XLite::ZONE_ADMIN:
                    $result = $notification->getAdminText();
                    break;

                default:
                    break;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function buildNotificationContent()
    {
        $text = $this->getNotificationText();

        $widget = clone $this;
        $widget->setWidgetParams([
            static::PARAM_TEMPLATE => sprintf('%s/body.twig', $this->dir),
        ]);
        $content = $widget->getContent();

        if (mb_strpos($text, '%dynamic_message%') === false) {
            return $text . $content;
        }

        return str_replace('%dynamic_message%', $content, $text);
    }

    /**
     * @return boolean
     */
    protected function isNotificationHeaderEnabled()
    {
        $result = true;

        switch (\XLite\Core\Layout::getInstance()->getZone()) {
            case \XLite::ZONE_CUSTOMER:
                $result = $this->getNotification() === null || $this->getNotification()->getCustomerHeaderEnabled();
                break;

            case \XLite::ZONE_ADMIN:
                $result = $this->getNotification() === null || $this->getNotification()->getAdminHeaderEnabled();
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * Returns header for current notification
     *
     * @return string
     */
    protected function getNotificationHeader()
    {
        $result = '';

        switch (\XLite\Core\Layout::getInstance()->getZone()) {
            case \XLite::ZONE_CUSTOMER:
                $result = static::t('emailNotificationCustomerHeader');
                break;

            case \XLite::ZONE_ADMIN:
                $result = static::t('emailNotificationAdminHeader');
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * @return boolean
     */
    protected function isNotificationGreetingEnabled()
    {
        $result = true;

        switch (\XLite\Core\Layout::getInstance()->getZone()) {
            case \XLite::ZONE_CUSTOMER:
                $result = $this->getNotification() === null || $this->getNotification()->getCustomerGreetingEnabled();
                break;

            case \XLite::ZONE_ADMIN:
                $result = $this->getNotification() === null || $this->getNotification()->getAdminGreetingEnabled();
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * @return boolean
     */
    protected function getNotificationGreeting()
    {
        switch (\XLite\Core\Layout::getInstance()->getZone()) {
            case \XLite::ZONE_CUSTOMER:
                return static::t('emailNotificationCustomerGreeting');

            case \XLite::ZONE_ADMIN:
                return static::t('emailNotificationAdminGreeting');

            default:
                return '';
        }
    }

    /**
     * @return boolean
     */
    protected function isNotificationSignatureEnabled()
    {
        $result = true;

        switch (\XLite\Core\Layout::getInstance()->getZone()) {
            case \XLite::ZONE_CUSTOMER:
                $result = $this->getNotification() === null || $this->getNotification()->getCustomerSignatureEnabled();
                break;

            case \XLite::ZONE_ADMIN:
                $result = $this->getNotification() === null || $this->getNotification()->getAdminSignatureEnabled();
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * Returns header for current notification
     *
     * @return string
     */
    protected function getNotificationSignature()
    {
        $result = '';

        switch (\XLite\Core\Layout::getInstance()->getZone()) {
            case \XLite::ZONE_CUSTOMER:
                $result = static::t('emailNotificationCustomerSignature');
                break;

            case \XLite::ZONE_ADMIN:
                $result = static::t('emailNotificationAdminSignature');
                break;

            default:
                break;
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function hasCompanyAddress()
    {
        return $this->getCompanyAddressFirstLine() || $this->getCompanyAddressSecondLine();
    }

    /**
     * @return string
     */
    protected function getCompanyAddressFirstLine()
    {
        return $this->executeCachedRuntime(static function () {
            $countryCode = Config::getInstance()->Company->location_country;

            $country     = Database::getRepo('XLite\Model\Country')->find($countryCode);
            $countryName = $country
                ? $country->getCountry()
                : $countryCode;

            $state = Config::getInstance()->Company->location_custom_state;
            if (
                $country instanceof \XLite\Model\Country
                && $country->hasStates()
                && Config::getInstance()->Company->location_state
            ) {
                if ($state = Database::getRepo('XLite\Model\State')->find((int) Config::getInstance()->Company->location_state)) {
                    $state = $state->getState();
                } else {
                    $state = '';
                }
            }

            return trim(sprintf(
                '%s %s',
                implode(', ', array_filter(array_map('trim', [
                    Config::getInstance()->Company->location_address,
                    Config::getInstance()->Company->location_city,
                    $state,
                    Config::getInstance()->Company->location_zipcode,
                ]), 'strlen')),
                $countryName
            ));
        });
    }

    /**
     * @return string
     */
    protected function getCompanyAddressSecondLine()
    {
        return $this->executeCachedRuntime(static function () {
            $phone = trim(Config::getInstance()->Company->company_phone);
            $fax   = trim(Config::getInstance()->Company->company_fax);

            return implode(' ', array_filter([
                strlen($phone) ? static::t('Phone') . ": $phone" : null,
                strlen($fax) ? static::t('Fax') . ": $fax" : null,
            ]));
        });
    }
}
