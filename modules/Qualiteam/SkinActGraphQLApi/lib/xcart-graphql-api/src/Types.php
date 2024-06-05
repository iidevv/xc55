<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi;

use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\Type;
use XcartGraphqlApi\Types\AppConfigType;
use XcartGraphqlApi\Types\AppDataType;
use XcartGraphqlApi\Types\AuthLinksType;
use XcartGraphqlApi\Types\CollectionItemType;
use XcartGraphqlApi\Types\CollectionType;
use XcartGraphqlApi\Types\Enum\AddressTypeEnumType;
use XcartGraphqlApi\Types\Enum\CartErrorEnumType;
use XcartGraphqlApi\Types\Enum\CollectionTypeEnumType;
use XcartGraphqlApi\Types\Enum\OfferStatusEnumType;
use XcartGraphqlApi\Types\Enum\UserTypeEnumType;
use XcartGraphqlApi\Types\Enum\Form\FormFieldTypeEnumType;
use XcartGraphqlApi\Types\InfoType;
use XcartGraphqlApi\Types\ContactUsInfoType;
use XcartGraphqlApi\Types\Input\ProductBatchLineType;
use XcartGraphqlApi\Types\Model\AppBannerImagesType;
use XcartGraphqlApi\Types\Model\BannerHtmlType;
use XcartGraphqlApi\Types\Model\BannerImagesType;
use XcartGraphqlApi\Types\Model\BannersListType;
use XcartGraphqlApi\Types\Model\DealBlockType;
use XcartGraphqlApi\Types\Model\SpecialOfferType;
use XcartGraphqlApi\Types\ObjectType;
use XcartGraphqlApi\Types\ShowroomInfoType;
use XcartGraphqlApi\Types\Input\AddressInputType;
use XcartGraphqlApi\Types\Input\ListOrderByInputType;
use XcartGraphqlApi\Types\Input\OrdersFiltersInputType;
use XcartGraphqlApi\Types\Input\PaymentFieldsInputType;
use XcartGraphqlApi\Types\Input\ProductsFilterInputType;
use XcartGraphqlApi\Types\Input\ValueRangeInputType;
use XcartGraphqlApi\Types\Model\ColorSwatchesType;
use XcartGraphqlApi\Types\Model\PagesUrlType;
use XcartGraphqlApi\Types\Model\AppData\AuthProvider;
use XcartGraphqlApi\Types\Model\AppData\HomePageWidgetType;
use XcartGraphqlApi\Types\Model\AppData\MembershipType;
use XcartGraphqlApi\Types\Model\AppData\ModuleType;
use XcartGraphqlApi\Types\Model\AppData\ProfileFieldType;
use XcartGraphqlApi\Types\Model\AppData\WidgetParamsType;
use XcartGraphqlApi\Types\Model\Cart\OrderItemOptionType;
use XcartGraphqlApi\Types\Model\Cart\PaymentMethodFieldType;
use XcartGraphqlApi\Types\Model\Cart\TransactionType;
use XcartGraphqlApi\Types\Model\CiaValue;
use XcartGraphqlApi\Types\Model\Conversation;
use XcartGraphqlApi\Types\Model\FAQType;
use XcartGraphqlApi\Types\Model\IconLinkType;
use XcartGraphqlApi\Types\Model\MenuNotificationsCustomerType;
use XcartGraphqlApi\Types\Model\MenuNotificationsVendorType;
use XcartGraphqlApi\Types\Model\MessageAdminType;
use XcartGraphqlApi\Types\Model\MessageType;
use XcartGraphqlApi\Types\Model\MessageUserType;
use XcartGraphqlApi\Types\Model\OfferType;
use XcartGraphqlApi\Types\Model\Cart\OrderType;
use XcartGraphqlApi\Types\Model\Product\ProductAttributeGroupType;
use XcartGraphqlApi\Types\Model\Product\ProductAttributeType;
use XcartGraphqlApi\Types\Model\Product\ProductVideoTours;
use XcartGraphqlApi\Types\Model\Product\ReorderAttributes;
use XcartGraphqlApi\Types\Model\Product\ProductOptionType;
use XcartGraphqlApi\Types\Model\Product\ProductOptionValueType;
use XcartGraphqlApi\Types\Model\Product\ProductSpecificationGroupType;
use XcartGraphqlApi\Types\Model\Product\SpecificationItemType;
use XcartGraphqlApi\Types\Model\Product\ProductShippingSection;
use XcartGraphqlApi\Types\Model\Product\ProductSticker;
use XcartGraphqlApi\Types\Model\Product\ProductTag;
use XcartGraphqlApi\Types\Model\Product\ProductType;

use XcartGraphqlApi\Types\Model\Category\CategoryType;
use XcartGraphqlApi\Types\Model\Category\CategoryProductFilters;

use XcartGraphqlApi\Types\Model\AppData\CountryType;
use XcartGraphqlApi\Types\Model\AppData\CurrencyType;
use XcartGraphqlApi\Types\Model\AppData\StateType;
use XcartGraphqlApi\Types\Model\AppData\LanguageType;
use XcartGraphqlApi\Types\Model\ProductAdditionalInfo;
use XcartGraphqlApi\Types\Model\QuestionType;
use XcartGraphqlApi\Types\Model\RegistrationResultType;
use XcartGraphqlApi\Types\Model\ReviewType;
use XcartGraphqlApi\Types\Model\SelectedOptionType;
use XcartGraphqlApi\Types\Model\SellerType;
use XcartGraphqlApi\Types\Model\VendorPlanTextType;
use XcartGraphqlApi\Types\Model\TooltipType;
use XcartGraphqlApi\Types\Model\VendorPlanType;
use XcartGraphqlApi\Types\Model\VendorType;
use XcartGraphqlApi\Types\Model\BrandType;
use XcartGraphqlApi\Types\Model\WishListType;
use XcartGraphqlApi\Types\MutationType;
use XcartGraphqlApi\Types\QueryType;
use XcartGraphqlApi\Types\Model\AddressType;
use XcartGraphqlApi\Types\Model\BannerType;
use XcartGraphqlApi\Types\Model\Cart\CartType;
use XcartGraphqlApi\Types\Model\Cart\CouponType;
use XcartGraphqlApi\Types\Model\Cart\OrderItemType;
use XcartGraphqlApi\Types\Model\Cart\PaymentType;
use XcartGraphqlApi\Types\Model\Cart\ShippingType;
use XcartGraphqlApi\Types\Model\Cart\ShippingStatusBarType;
use XcartGraphqlApi\Types\Model\UserType;

use XcartGraphqlApi\Types\Form\SelectFieldType;
use XcartGraphqlApi\Types\Form\SelectFieldValueType;
use XcartGraphqlApi\Types\Form\SwitchFieldType;
use XcartGraphqlApi\Types\Form\TextFieldType;
use XcartGraphqlApi\Types\Form\ValueRangeFieldType;
use XcartGraphqlApi\Types\Form\ValueRangeFieldValueType;
use XcartGraphqlApi\Types\Input;

/**
 * Class Types
 * @package XcartGraphqlApi
 */
class Types extends \GraphQL\Type\Definition\Type
{
    /**
     * @var ResolverFactoryInterface
     */
    private static $resolverFactory;

    /**
     * @var array
     */
    protected static $map;

    /**
     * @var array
     */
    protected static $registry;

    /**
     * @return array
     */
    protected static function defineMap()
    {
        return [
            'query' => function() {
                return new QueryType(static::$resolverFactory);
            },
            'mutation' => function() {
                return new MutationType(static::$resolverFactory);
            },

            'product' => function() {
                return new ProductType(static::$resolverFactory);
            },
            'productOption' => function() {
                return new ProductOptionType(static::$resolverFactory);
            },
            'productOptionValue' => function() {
                return new ProductOptionValueType(static::$resolverFactory);
            },
            'productAttributeGroup' => function() {
                return new ProductAttributeGroupType(static::$resolverFactory);
            },
            'productAttribute' => function() {
                return new ProductAttributeType(static::$resolverFactory);
            },
            'productSpecificationGroup' => function() {
                return new ProductSpecificationGroupType(static::$resolverFactory);
            },  
            'specificationItem' => function() {
                return new SpecificationItemType(static::$resolverFactory);
            },
            'productSticker' => function() {
                return new ProductSticker();
            },
            'productTag' => function() {
                return new ProductTag();
            },
            'category' => function() {
                return new CategoryType(static::$resolverFactory);
            },
            'banner' => function() {
                return new BannerType(static::$resolverFactory);
            },
            'cart' => function() {
                return new CartType(static::$resolverFactory);
            },
            'user' => function() {
                return new UserType(static::$resolverFactory);
            },
            'address' => function() {
                return new AddressType(static::$resolverFactory);
            },
            'addressTypeEnum' => function() {
                return new AddressTypeEnumType();
            },
            'orderItem' => function() {
                return new OrderItemType(static::$resolverFactory);
            },
            'paymentMethod' => function() {
                return new PaymentType(static::$resolverFactory);
            },
            'paymentMethodField' => function() {
                return new PaymentMethodFieldType(static::$resolverFactory);
            },
            'shippingStatusBar' => function() {
                return new ShippingStatusBarType(static::$resolverFactory);
            },
            'shippingMethod' => function() {
                return new ShippingType(static::$resolverFactory);
            },
            'appConfig' => function() {
                return new AppConfigType(static::$resolverFactory);
            },
            'orderItemOption' => function() {
                return new OrderItemOptionType(static::$resolverFactory);
            },
            'info' => function() {
                return new InfoType(static::$resolverFactory);
            },
            'contactUsInfo' => function() {
                return new ContactUsInfoType(static::$resolverFactory);
            },
            'showroomInfo' => function() {
                return new ShowroomInfoType(static::$resolverFactory);
            },                    
            'authLinks' => function() {
                return new AuthLinksType(static::$resolverFactory);
            },
            // Collection types
            'collection' => function() {
                return new CollectionType(static::$resolverFactory);
            },
            'collection_item' => function() {
                return new CollectionItemType();
            },
            'collection_type' => function() {
                return new CollectionTypeEnumType();
            },

            'appData' => function() {
                return new AppDataType(static::$resolverFactory);
            },
            'country' => function() {
                return new CountryType(static::$resolverFactory);
            },
            'state' => function() {
                return new StateType(static::$resolverFactory);
            },
            'currency' => function() {
                return new CurrencyType(static::$resolverFactory);
            },
            'language' => function() {
                return new LanguageType(static::$resolverFactory);
            },
            'module' => function() {
                return new ModuleType(static::$resolverFactory);
            },
            'membership' => function() {
                return new MembershipType(static::$resolverFactory);
            },
            'profileField' => function() {
                return new ProfileFieldType(static::$resolverFactory);
            },
            'homePageWidget' => function() {
                return new HomePageWidgetType(static::$resolverFactory);
            },
            'widgetParams' => function() {
                return new WidgetParamsType(static::$resolverFactory);
            },
            'authProvider' => function() {
                return new AuthProvider(static::$resolverFactory);
            },

            'coupon' => function() {
                return new CouponType(static::$resolverFactory);
            },
            'cartErrorEnum' => function() {
                return new CartErrorEnumType();
            },
            'userTypeEnum' => function() {
                return new UserTypeEnumType();
            },
            'faq' => function() {
                return new FAQType(static::$resolverFactory);
            },
            'offer' => function() {
                return new OfferType(static::$resolverFactory);
            },
            'wishlist' => function() {
                return new WishListType(static::$resolverFactory);
            },
            'vendor' => function() {
                return new VendorType(static::$resolverFactory);
            },
            'brand' => function() {
                return new BrandType(static::$resolverFactory);
            },
            'vendorPlan' => function() {
                return new VendorPlanType(static::$resolverFactory);
            },
            'vendorPlanText' => function() {
                return new VendorPlanTextType(static::$resolverFactory);
            },
            'conversation' => function() {
                return new Conversation(static::$resolverFactory);
            },
            'message' => function() {
                return new MessageType(static::$resolverFactory);
            },
            'message_admin' => function() {
                return new MessageAdminType(static::$resolverFactory);
            },
            'message_user' => function() {
                return new MessageUserType(static::$resolverFactory);
            },
            'seller' => function() {
                return new SellerType(static::$resolverFactory);
            },
            'productAdditionalInfo' => function() {
                return new ProductAdditionalInfo(static::$resolverFactory);
            },  
            'order' => function() {
                return new OrderType(static::$resolverFactory);
            },
            'transaction' => function() {
                return new TransactionType(static::$resolverFactory);
            },
            'review' => function() {
                return new ReviewType(static::$resolverFactory);
            },
            'question' => function() {
                return new QuestionType(static::$resolverFactory);
            },
            'registrationResult' => function() {
                return new RegistrationResultType(static::$resolverFactory);
            },
            'menuNotificationsCustomer' => function() {
                return new MenuNotificationsCustomerType(static::$resolverFactory);
            },
            'menuNotificationsVendor' => function() {
                return new MenuNotificationsVendorType(static::$resolverFactory);
            },
            'ciaValue' => function() {
                return new CiaValue(static::$resolverFactory);
            },

            // Form fields
            'formFieldTypeEnum' => function() {
                return new FormFieldTypeEnumType();
            },
            'selectField' => function() {
                return new SelectFieldType(static::$resolverFactory);
            },
            'selectFieldValue' => function() {
                return new SelectFieldValueType();
            },
            'switchField' => function() {
                return new SwitchFieldType(static::$resolverFactory);
            },
            'textField' => function() {
                return new TextFieldType(static::$resolverFactory);
            },
            'valueRangeField' => function() {
                return new ValueRangeFieldType(static::$resolverFactory);
            },
            'valueRangeFieldValue' => function() {
                return new ValueRangeFieldValueType();
            },

            // Filters related types
            'categoryFilters' => function() {
                return new CategoryProductFilters();
            },

            // Input types
            'AuthInput' => function() {
                return new Input\Service\AuthInputType();
            },
            'ExternalAuthInput' => function() {
                return new Input\Service\ExternalAuthInputType();
            },
            'AccessToken' => function() {
                return new Input\Service\AccessToken();
            },
            'ClientInput' => function() {
                return new Input\Service\ClientInputType();
            },
            'UserRegisterInput' => function () {
                return new Input\User\UserRegisterInput();
            },
            'UserUpdateInput' => function () {
                return new Input\User\UserUpdateInput();
            },

            'listOrderByInputType' => function() {
                return new ListOrderByInputType();
            },
            'valueRangeInputType' => function() {
                return new ValueRangeInputType();
            },
            'productsFiltersInput' => function() {
                return new ProductsFilterInputType();
            },
            'ordersFiltersInput' => function() {
                return new OrdersFiltersInputType();
            },
            'offersFiltersInput' => function () {
                return new Input\OffersFilterInputType();
            },
            'offersTypeEnum' => function() {
                return new OfferStatusEnumType();
            },
            'categoryFiltersInput' => function() {
                return new CategoryFilterInputType();
            },
            'address_input' => function() {
                return new AddressInputType();
            },
            'payment_fields_input' => function() {
                return new PaymentFieldsInputType();
            },
            'product_batch_line_input' => function() {
                return new ProductBatchLineType();
            },
            'tooltip' => function() {
                return new TooltipType(static::$resolverFactory);
            },
            'pagesUrl' => function() {
                return new PagesUrlType(static::$resolverFactory);
            },
            'iconLink' => function() {
                return new IconLinkType(static::$resolverFactory);
            },
            'productShippingSection' => function() {
                return new ProductShippingSection(static::$resolverFactory);
            },

            'colorSwatches' => function() {
                return new ColorSwatchesType(static::$resolverFactory);
            },

            'videoTours' => function() {
                return new ProductVideoTours(static::$resolverFactory);
            },

            'reorder_attributes' => function() {
                return new ReorderAttributes(static::$resolverFactory);
            },

            'specialOffer' => function() {
                return new SpecialOfferType(static::$resolverFactory);
            },

            'bannersList' => function() {
                return new BannersListType(static::$resolverFactory);
            },

            'bannerImages' => function() {
                return new BannerImagesType(static::$resolverFactory);
            },

            'appBannerImages' => function() {
                return new AppBannerImagesType(static::$resolverFactory);
            },

            'bannerHtml' => function() {
                return new BannerHtmlType(static::$resolverFactory);
            },

            'productSelectedOption' => function() {
                return new \XcartGraphqlApi\Types\Input\ProductSelectedOptionInputType();
            },

            'dealBlock' => function () {
                return new DealBlockType(static::$resolverFactory);
            },
        ];
    }

    public static function byName($name)
    {
        $map = static::getMap();

        if (!isset($map[$name])) {
            throw new \RuntimeException("No type instance constructor found for \"$name\"");
        }

        if (!is_callable($map[$name])) {
            throw new \RuntimeException("Type instance constructor for \"$name\" is not callable");
        }

        return static::getInstanceOfType($name, $map[$name]);
    }

    public static function serializable(array $config)
    {
        if (!isset($config['serialize'])) {
            $config['serialize'] = function ($value) {
                if (empty($value) || is_null($value)) {
                    return [];
                }

                return json_encode($value);
            };
        }

        return new CustomScalarType($config);
    }

    /**
     * @param callable $createCallback
     *
     * @return Type
     */
    protected static function getInstanceOfType($name, callable $createCallback)
    {
        if(!isset(static::$registry[$name])) {
            static::$registry[$name] = $createCallback();
        }

        return static::$registry[$name];
    }

    /**
     * @return array
     */
    protected static function getMap()
    {
        if (!static::$map) {
            static::$map = static::defineMap();
        }

        return static::$map;
    }

    /**
     * @return ResolverFactoryInterface
     */
    public static function getResolverFactory()
    {
        return self::$resolverFactory;
    }

    /**
     * @param ResolverFactoryInterface $resolverFactory
     */
    public static function setResolverFactory($resolverFactory)
    {
        self::$resolverFactory = $resolverFactory;
    }

}
