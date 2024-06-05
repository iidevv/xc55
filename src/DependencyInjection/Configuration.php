<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('x_cart');

        $treeBuilder->getRootNode()
            ->append($this->getHostDetailsNode())
            ->append($this->getCleanURLsNode())
            ->append($this->getImagesNode())
            ->append($this->getInstallerDetailsNode())
            ->append($this->getLogDetailsNode())
            ->append($this->getErrorHandlingNode())
            ->append($this->getMarketplaceNode())
            ->append($this->getInstallationNode())
            ->append($this->getHtmlPurifierNode())
            ->append($this->getStorefrontOptionsNode())
            ->append($this->getOtherNode())
            ->append($this->getExportImportNode())
            ->append($this->getShippingListNode())
            ->append($this->getTrialNode())
            ->append($this->getDemoNode())
            ->append($this->getPerformanceNode())
            ->append($this->getServiceNode())
            ->append($this->getAffiliateNode())
            ->children()
                ->arrayNode('modules')
                    ->normalizeKeys(false)
                    ->variablePrototype()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getHostDetailsNode()
    {
        $treeBuilder = new TreeBuilder('host_details');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

X-Cart 5 HTTP & HTTPS host, web directory where cart installed and allowed domains

NOTE:
You should put here hostname ONLY without http:// or https:// prefixes.
Do not put slashes after the hostname.
Web dir is the directory in the URL, not the filesystem path.
Web dir must start with slash and have no slash at the end.
The only exception is when you configure for the root of the site,
in which case you should leave the option empty.
Domains should be listed separated by commas.

WARNING: Do not set the "$" sign before the parameter names!

EXAMPLE 1:
http_host: 'www.yourhost.com'
https_host: 'www.securedirectories.com/yourhost.com'
web_dir: '/shop'
domains: 'www.yourhost2.com, yourhost3.com'

will result in the following URLs:

http://www.yourhost.com/shop
https://www.securedirectories.com/yourhost.com/shop

EXAMPLE 2:
http_host: 'www.yourhost.com'
https_host: "www.yourhost.com"
web_dir: ''

will result in the following URLs:

http://www.yourhost.com
https://www.yourhost.com
EOF
            )
            ->children()
                ->scalarNode('http_host')
                    ->defaultValue('%env(string:XCART_HOST_DETAILS_HTTP_HOST)%')
                    ->example('www.yourhost.com')
                ->end()
                ->scalarNode('https_host')
                    ->defaultValue('%env(string:XCART_HOST_DETAILS_HTTPS_HOST)%')
                    ->example('www.yourhost.com')
                ->end()
                ->scalarNode('admin_host')
                    ->defaultValue('%env(string:XCART_HOST_DETAILS_ADMIN_HOST)%')
                    ->example('www.yourhost.com')
                ->end()
                    ->scalarNode('web_dir')
                    ->info(<<<EOF
Web dir is the directory in the URL, not the filesystem path.
Web dir must start with slash and have no slash at the end.
The only exception is when you configure for the root of the site,
in which case you should leave the option empty.
EOF
                    )
                    ->defaultValue('%env(string:XCART_HOST_DETAILS_WEB_DIR)%')
                    ->example('/shop')
                ->end()
                ->variableNode('domains')
                    ->defaultValue('%env(csv:XCART_HOST_DETAILS_DOMAINS)%')
                    ->example('www.yourhost2.com,yourhost3.com')
                ->end()
                ->scalarNode('admin_self')
                    ->defaultValue('%env(string:XCART_HOST_DETAILS_ADMIN_SELF)%')
                    ->example('admin/')
                ->end()
                ->scalarNode('cart_self')
                    ->defaultValue('%env(string:XCART_HOST_DETAILS_CART_SELF)%')
                    ->example('/')
                ->end()
                ->booleanNode('force_https')
                    ->defaultValue('%env(bool:XCART_HOST_DETAILS_FORCE_HTTPS)%')
                    ->example(true)
                ->end()
                ->booleanNode('public_dir')
                    ->info(<<<EOF
The public_dir variable must be either FALSE (preferred) or TRUE and depends on the web server root configuration.
If web server root points to the store's /public directory the value should be empty.
If web server root points to the store's root directory the value should be TRUE. This option exists for compatibility
reasons only. You need to reconfigure the web server to the preferred option at the first opportunity.
EOF
                    )
                    ->defaultValue('%env(bool:XCART_PUBLIC_DIR)%')
                    ->example(true)
                ->end()
            ->end();
    }

    private function getCleanURLsNode()
    {
        $treeBuilder = new TreeBuilder('clean_urls');

        return $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('use_language_url')
                    ->info(<<<EOF
Is use urls like domain.com/LG for languages
Changing this setting requires to re-deploy your store
EOF
                    )
                    ->defaultValue(true)
                ->end()
                ->scalarNode('default_separator')
                    ->info(<<<EOF
String with one or more chars.
It will be used to autogenerate clean URLs.
By default, only the '-' or '_' characters are allowed.
Empty string is also allowed.
EOF
                    )
                    ->defaultValue('-')
                    ->example('-')
                ->end()
                ->booleanNode('capitalize_words')
                    ->info(<<<EOF
Get clean URLs capitalized for every starting letter of a word
EOF
                    )
                    ->defaultValue(false)
                ->end()
                ->booleanNode('use_unicode')
                    ->info(<<<EOF
Allow non latin symbols for autogenerated clean URLs
EOF
                    )
                    ->defaultValue(false)
                ->end()
                ->arrayNode('formats')
                    ->info(<<<EOF
canonical_product
Canonical product Clean URL’s format
possible values:
 domain/product_clean_url
 domain/main_category_clean_url/product_clean_url

category
Category Clean URL’s format
possible values:
 domain/parent/goalcategory/
 domain/goalcategory/
 domain/parent/goalcategory.html
 domain/goalcategory.html

Changing this setting will not affect existing url's
and requires to re-deploy your store

product
Product Clean URL’s format
possible values:
 domain/goalproduct
 domain/goalproduct.html

Changing this setting will not affect existing url's
and requires to re-deploy your store
EOF
            )
                    //->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->defaultValue([
                        'canonical_product' => 'domain/product_clean_url',
                        'category' => 'domain/parent/goalcategory/',
                        'product' => 'domain/goalproduct',
                    ])
                ->end()
                ->arrayNode('aliases')
                    ->info(<<<EOF
to define your own alias add line below as:
target: 'clean-url'
EOF
                    )
                    //->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->defaultValue(['contact_us' => 'contact-us'])
                ->end()
            ->end();
    }

    private function getImagesNode()
    {
        $treeBuilder = new TreeBuilder('images');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Default image settings

EOF
            )
            ->children()
                ->scalarNode('default_image')
                    ->defaultValue('images/no_image.png')
                ->end()
                ->integerNode('default_image_width')
                    ->defaultValue(110)
                ->end()
                ->integerNode('default_image_height')
                    ->defaultValue(110)
                ->end()
                ->booleanNode('make_progressive')
                    ->defaultValue(true)
                ->end()
                ->booleanNode('generate_retina_images')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('image_magick_path')
                    ->info(<<<EOF
Installation path of Image Magick executables:
for example:
image_magick_path: 'C:\Program Files\ImageMagick-6.7.0-Q16\'   (in Windows)
image_magick_path: '/usr/local/imagemagick/' (in Unix/Linux )
You should consult with your hosting provider to find where Image Magick is installed
If you leave it empty then PHP GD library will be used.
EOF
                    )
                    ->defaultValue('%env(string:XCART_IMAGES_IMAGE_MAGICK_PATH)%')
                    ->example('/usr/local/imagemagick/')
                ->end()
            ->end();
    }

    private function getInstallerDetailsNode()
    {
        $treeBuilder = new TreeBuilder('installer_details');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Installer authcode.
A person who do not know the auth code can not access the installation script.
Installation authcode is created authomatically and stored in this section.

EOF
            )
            ->children()
                ->scalarNode('auth_code')
                    ->defaultValue('%env(string:XCART_INSTALLER_DETAILS_AUTH_CODE)%')
                ->end()
                ->scalarNode('shared_secret_key')
                    ->defaultValue('%env(string:XCART_INSTALLER_DETAILS_SHARED_SECRET_KEY)%')
                ->end()
            ->end();
    }

    private function getLogDetailsNode()
    {
        $treeBuilder = new TreeBuilder('log_details');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Logging details

EOF
            )
            ->children()
                ->scalarNode('suppress_errors')
                    ->defaultValue('%env(bool:XCART_LOG_DETAILS_SUPPRESS_ERRORS)%')
                ->end()
                ->scalarNode('suppress_log_errors')
                    ->defaultValue('%env(bool:XCART_LOG_DETAILS_SUPPRESS_LOG_ERRORS)%')
                ->end()
            ->end();
    }

    private function getErrorHandlingNode()
    {
        $treeBuilder = new TreeBuilder('error_handling');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Error handling options

EOF
            )
            ->children()
                ->scalarNode('page')
                    ->info(<<<EOF
Template for error pages
EOF
                    )
                    ->defaultValue('public/error.html')
                ->end()
                ->scalarNode('page_customer')
                    ->defaultValue('public/customer/error.html')
                ->end()
                ->scalarNode('maintenance')
                    ->info(<<<EOF
Template for maintenance pages
EOF
                    )
                    ->defaultValue('public/maintenance.html')
                ->end()
            ->end();
    }

    private function getMarketplaceNode()
    {
        $treeBuilder = new TreeBuilder('marketplace');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Marketplace

EOF
            )
            ->children()
                ->scalarNode('url')
                    ->defaultValue('%xcart.marketplace.url%')
                ->end()
                ->scalarNode('appstore_url')
                ->defaultValue('%xcart.marketplace.appstore_url%')
                ->end()
                ->integerNode('upgrade_step_time_limit')
                    ->defaultValue('%xcart.marketplace.upgrade_step_time_limit%')
                ->end()
                ->scalarNode('banner_url')
                    ->defaultValue('%xcart.marketplace.banner_url%')
                ->end()
                ->scalarNode('editions_url')
                    ->defaultValue('%xcart.marketplace.editions_url%')
                ->end()
                ->scalarNode('segment_url')
                    ->defaultValue('%xcart.marketplace.segment_url%')
                ->end()
                ->scalarNode('addon_images_url')
                    ->defaultValue('%xcart.marketplace.addon_images_url%')
                ->end()
                ->scalarNode('xb_host')
                    ->defaultValue('%xcart.marketplace.xb_host%')
                ->end()
            ->end();
    }

    private function getInstallationNode()
    {
        $treeBuilder = new TreeBuilder('installation');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Installation parameters

EOF
            )
            ->children()
                ->scalarNode('installation_lng')
                    ->defaultValue('%xcart.installation_lng%')
                ->end()
            ->end();
    }

    private function getHtmlPurifierNode()
    {
        $treeBuilder = new TreeBuilder('html_purifier');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

HTML Purifier options
See http://htmlpurifier.org/live/configdoc/plain.html for more details on HTML Purifier options

EOF
            )
            ->children()
                ->arrayNode('options')
                    ->info(<<<EOF
Default options info:

List of allowed values for 'target' attribute:
Attr.AllowedFrameTargets: [ _blank, _self, _top, _parent ]

Allow 'id' attribute:
Attr.EnableID: true

Allow tricky css like 'display:block;' on images:
CSS.AllowTricky: true

Allow embed tags:
HTML.SafeEmbed: true

Allow object tags:
HTML.SafeObject: true

Allow iframe tags:
HTML.SafeIframe: true

List of allowed URI (without http:// or https:// part) for iframe tags
If there are no allowed URIs specified then any src will be allowed for iframe tags
URI.SafeIframeRegexp: []

Example: URI.SafeIframeRegexp: ['www.youtube.com/embed/', 'www.youtube-nocookie.com/embed/', 'player.vimeo.com/video/']

EOF
                    )
                    //->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->variablePrototype()->end()
                    ->defaultValue([
                        'Attr.AllowedFrameTargets' => [ '_blank', '_self', '_top', '_parent' ],
                        'Attr.EnableID' => true,
                        'CSS.AllowTricky' => true,
                        'HTML.SafeEmbed' => true,
                        'HTML.SafeObject' => true,
                        'HTML.SafeIframe' => true,
                        'URI.SafeIframeRegexp' => []
                    ])
                ->end()
                ->arrayNode('attributes')
                    ->info(<<<EOF
HTML Purifier additional attributes
format:
tag: [attribute1:attribute_definition2, attribute2:attribute_definition2]

For tag only(if you specified attribute as above - tag will be added automatically):
tag: []

Attribute definitions:
Enum      - as example "Enum#_blank,_self,_target,_top"
Bool      - Boolean attribute, with only one valid value: the name of the attribute.
CDATA     - Attribute of arbitrary text. (also Text valid)
ID        - Attribute that specifies a unique ID
Pixels    - Attribute that specifies an integer pixel length
Length    - Attribute that specifies a pixel or percentage length
NMTOKENS  - Attribute that specifies a number of name tokens, example: the class attribute
URI       - Attribute that specifies a URI, example: the href attribute
Number    - Attribute that specifies an positive integer number
EOF
                    )
                    //->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->variablePrototype()->end()
                    ->defaultValue([
                        'iframe' => [ 'allowfullscreen:CDATA' ],
                        'video' => [ 'src:URI', 'type:Text', 'width:Length', 'height:Length', 'poster:URI', 'preload:Enum#auto,metadata,none', 'controls:Bool' ],
                    ])
                ->end()
            ->end();
    }

    private function getStorefrontOptionsNode()
    {
        $treeBuilder = new TreeBuilder('storefront_options');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Storefront options

EOF
            )
            ->children()
                ->booleanNode('callback_opened')
                    ->info(<<<EOF
Do not close target=callback for payments if storefront is closed
EOF
                    )
                    ->defaultValue('%env(bool:XCART_STOREFRONT_OPTIONS_CALLBACK_OPENED)%')
                ->end()
                ->booleanNode('optimize_css')
                    ->info(<<<EOF
Works only with 'Aggregate CSS files' option enabled
EOF
                    )
                    ->defaultValue('%env(bool:XCART_STOREFRONT_OPTIONS_OPTIMIZE_CSS)%')
                ->end()
                ->arrayNode('autocomplete_states_for_countries')
                    ->info(<<<EOF
All the following countries always uses custom state with autocomplete(if available)
possible values - country codes ('GB,US,DE' as example), 'All' or empty ''
EOF
                    )
                    ->scalarPrototype()->end()
                    ->defaultValue([ 'GB' ])
                ->end()
            ->end();
    }

    private function getOtherNode()
    {
        $treeBuilder = new TreeBuilder('other');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Other options

EOF
            )
            ->children()
                ->scalarNode('translation_driver')
                    ->info(<<<EOF
Translation drive code - auto / gettext / db
EOF
                    )
                    //->values(['auto', 'gettext', 'db'])
                    ->defaultValue('%env(string:XCART_OTHER_TRANSLATION_DRIVER)%')
                ->end()
                ->arrayNode('trusted_domains')
                    ->info(<<<EOF
List of trusted domains.
This option prevents redirecting to untrusted URLs passed via returnURL parameter.
EOF
                    )
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('x_frame_options')
                    ->info(<<<EOF
X-Frame-Options value
For possible values see https://developer.mozilla.org/en-US/docs/Web/HTTP/X-Frame-Options
Examples:
 x_frame_options: 'disabled'
 x_frame_options: 'sameorigin'
EOF
                    )
                    ->defaultValue('sameorigin')
                ->end()
                ->scalarNode('x_xss_protection')
                    ->info(<<<EOF
X-XSS-Protection value
For possible values see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection
Examples:
 x_xss_protection: 'disabled' prevent X-XSS-Protection header sending
 x_xss_protection: '0'
 x_xss_protection: '1; mode=block'
EOF
                    )
                    ->defaultValue('1; mode=block')
                ->end()
                ->scalarNode('content_security_policy')
                    ->info(<<<EOF
Content-Security-Policy value
For possible values see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy
Examples:
 content_security_policy: 'disabled' prevent Content-Security-Policy header sending
 content_security_policy: "default-src 'self'"
 content_security_policy: "default-src 'self'; img-src *;"
EOF
                    )
                    ->defaultValue('disabled')
                ->end()
                ->scalarNode('x_content_type_options')
                    ->info(<<<EOF
X-Content-Type-Options value
For possible values see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options
Examples:
 x_content_type_options: 'disabled' prevent X-Content-Type-Options header sending
 x_content_type_options: 'nosniff'
EOF
                    )
                    ->defaultValue('nosniff')
                ->end()
                ->enumNode('csrf_strategy')
                    ->info(<<<EOF
CSRF token strategy
possible values: per-session, per-form
EOF
                    )
                    ->values(['per-session', 'per-form'])
                    ->defaultValue('per-session')
                ->end()
                ->booleanNode('meta_upgrade_insecure')
                    ->info(<<<EOF
Add Content-Security-Policy meta tag with upgrade-insecure-requests
For details see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/upgrade-insecure-requests
EOF
                    )
                    ->defaultValue(false)
                ->end()
                ->booleanNode('show_initialized_transactions')
                    ->defaultValue(false)
                ->end()
                ->booleanNode('use_sendfile')
                    ->info(<<<EOF
Use X-Sendfile and X-Accel-Redirect to download files
Note that server should be properly configured
EOF
                    )
                    ->defaultValue('%env(bool:XCART_OTHER_USE_SENDFILE)%')
                ->end()
                ->enumNode('next_previous_order_criteria')
                    ->info(<<<EOF
Next-previous order criteria
allowed values: orderNumber, date
EOF
                    )
                    ->values(['orderNumber', 'date'])
                    ->defaultValue('orderNumber')
                ->end()
                ->enumNode('cookie_samesite')
                    ->info(<<<EOF
SameSite prevents the browser from sending this cookie along with cross-site requests.
The main goal is mitigate the risk of cross-origin information leakage. It also provides
some protection against cross-site request forgery attacks (https://www.owasp.org/index.php/SameSite)
Possible values for the flag are "lax", "strict" or "" (not set)
EOF
                    )
                    ->values(['lax', 'strict', ''])
                    ->defaultValue('lax')
                ->end()
                ->booleanNode('cookie_hostonly')
                    ->info(<<<EOF
If cookie_hostonly is true, domain attribute won't be sent with set cookie header.
That makes cookies of current domain inaccessible in subdomains (https://tools.ietf.org/html/rfc6265#section-5.3)
EOF
                    )
                    ->defaultValue(true)
                ->end()
            ->end();
    }

    private function getExportImportNode()
    {
        $treeBuilder = new TreeBuilder('export_import');

        return $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('encodings_list')
                    ->info(<<<EOF
Export/Import available encodings list
This values should be valid iconv encoding alias and should be listed in iconv -l output
EOF
                    )
                    ->scalarPrototype()->end()
                    ->defaultValue([ 'UTF-8','ISO-8859-1','WINDOWS-1251','CSSHIFTJIS','WINDOWS-1252','GB2312','EUC-KR','EUC-JP','GBK','ISO-8859-2','ISO-8859-15','WINDOWS-1250','WINDOWS-1256','ISO-8859-9','BIG5','WINDOWS-1254','WINDOWS-874' ])
                ->end()
            ->end();
    }

    private function getShippingListNode()
    {
        $treeBuilder = new TreeBuilder('shipping_list');

        return $treeBuilder->getRootNode()
            ->children()
                ->integerNode('display_selector_cutoff')
                    ->info(<<<EOF
Maximum number of shipping options to be shown as a radio button list on the order creation page.
If the number of available options exceeds this value, the options will be shown in a drop-down box.
EOF
                    )
                    ->defaultValue(8)
                ->end()
            ->end();
    }

    private function getTrialNode()
    {
        $treeBuilder = new TreeBuilder('trial');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Trial

EOF
            )
            ->children()
                ->scalarNode('end_date')
                    ->defaultValue('%env(string:XCART_TRIAL_END_DATE)%')
                ->end()
            ->end();
    }

    private function getDemoNode()
    {
        $treeBuilder = new TreeBuilder('demo');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Demo

EOF
            )
            ->children()
                ->booleanNode('demo_mode')
                    ->defaultValue('%env(bool:XCART_DEMO_DEMO_MODE)%')
                ->end()
            ->end();
    }

    private function getPerformanceNode()
    {
        $treeBuilder = new TreeBuilder('performance');

        return $treeBuilder->getRootNode()
            ->info(<<<EOF

Some options to optimize the store

EOF
            )
            ->children()
                ->booleanNode('skins_cache')
                    ->info(<<<EOF
Enable to cache resource paths
EOF
                    )
                    ->defaultValue(false)
                ->end()
                ->booleanNode('background_jobs')
                    ->info(<<<EOF
Enable to consume jobs via worker (php bin/console messenger:consume)
EOF
                    )
                    ->defaultValue('%env(bool:BACKGROUND_JOBS_ENABLED)%')
                ->end()
            ->end();
    }

    private function getServiceNode()
    {
        $treeBuilder = new TreeBuilder('service');

        return $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('is_trial')
                    ->defaultValue('%env(bool:XCART_SERVICE_IS_TRIAL)%')
                ->end()
                ->booleanNode('display_update_notification')
                    ->defaultValue('%env(bool:XCART_IS_UPDATE_AVAILABLE)%')
                ->end()
            ->end();
    }

    private function getAffiliateNode()
    {
        $treeBuilder = new TreeBuilder('affiliate');

        return $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('id')
                    ->defaultValue('%xcart.affiliate_id%')
                ->end()
            ->end();
    }
}
