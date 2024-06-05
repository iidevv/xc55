<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation;

use XcartGraphqlApi\ResolverFactoryInterface;
use XcartGraphqlApi\Types\Enum\AddressTypeEnumType;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\MenuNotificationsCustomer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\MenuNotificationsVendor;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

/**
 * Class ResolverFactory
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation
 */
class ResolverFactory implements ResolverFactoryInterface
{
    /**
     * @var CartService
     */
    protected $cartService;

    public function createForType($typeName)
    {
        $productMapper = new Mapper\Product();
        $categoryMapper = new Mapper\Category();
        $cartMapper = new Mapper\Cart(
            $this->getCartService()
        );

        $orderMapper = new Mapper\Order();
        $specialOfferMapper = new Mapper\SpecialOffer();

        switch ($typeName) {
            case 'collection':
                return new Resolver\Collection($this);
                break;
            case 'product':
                return new Resolver\Product\Product($productMapper);
                break;
            case 'products':
                return new Resolver\Product\Products($productMapper);
                break;
            case 'productsCount':
                return new Resolver\Product\ProductsCount($productMapper);
                break;
            case 'productAttributes':
                return new Resolver\Product\Attribute();
                break;
            case 'productOptions':
                return new Resolver\Product\Options();
                break;
            case 'productSpecification':
                return new Resolver\Product\Specification();
                break;
            case 'relatedProducts':
                return new Resolver\Product\RelatedProducts($productMapper);
                break;
            case 'frequentlyBoughtTogether':
                return new Resolver\Modules\FrequentlyBoughtTogether($productMapper);
                break;
            case 'category':
                return new Resolver\Category\Category($categoryMapper);
                break;
            case 'categories':
                return new Resolver\Category\Categories($categoryMapper);
                break;
            case 'categoriesCount':
                return new Resolver\Category\CategoriesCount($categoryMapper);
                break;
            case 'subcategories':
                return new Resolver\Category\Subcategories($categoryMapper);
                break;
            case 'categoryProducts':
                return new Resolver\Category\Products($productMapper);
                break;
            case 'categoryProductsFilters':
                return new Resolver\Category\ProductsFilters();
                break;

            case 'catalog':
                return new Resolver\Category\Catalog($categoryMapper);
                break;
            case 'countries':
                return new Resolver\System\Countries();
                break;
            case 'states':
                return new Resolver\System\States();
                break;
            case 'stateCountry':
                return new Resolver\System\StateCountry();
                break;
            case 'currencies':
                return new Resolver\System\Currencies();
                break;
            case 'customerOrders':
                return new Resolver\Cart\CustomerOrders($orderMapper);
                break;
            case 'sellerOrders':
                return new Resolver\Cart\SellerOrders($orderMapper);
                break;
            case 'deleteOrder':
                return new Resolver\Mutations\Cart\DeleteOrder();
                break;
            case 'updateOrder':
                return new Resolver\Mutations\Cart\UpdateOrder($orderMapper);
                break;
            case 'orderTransactions':
                return new Resolver\Cart\OrderTransactions();
                break;
            case 'banners':
                return new Resolver\Banners();
                break;
            case 'cart':
                return new Resolver\Cart\Cart($cartMapper, $this->getCartService());
                break;
            case 'cartUser':
                return new Resolver\Cart\User(new Mapper\User());
                break;
            case 'cartAddresses':
                return new Resolver\Cart\Addresses(new Mapper\Address());
                break;
            case 'cartShippingAddress':
                return new Resolver\Cart\Address(
                    new Mapper\Address(),
                    AddressTypeEnumType::SHIPPING_TYPE
                );
                break;
            case 'cartBillingAddress':
                return new Resolver\Cart\Address(
                    new Mapper\Address(),
                    AddressTypeEnumType::BILLING_TYPE
                );
                break;
            case 'cartItems':
                return new Resolver\Cart\Items(
                    new Mapper\Product()
                );
                break;
            case 'cartPayment':
                return new Resolver\Cart\Payment(
                    new Mapper\PaymentMethod()
                );
                break;
            case 'cartShipping':
                return new Resolver\Cart\Shipping(
                    new Mapper\ShippingMethodRate()
                );
                break;
            case 'cartCoupons':
                return new Resolver\Cart\Coupons();
                break;
            case 'addressCountry':
                return new Resolver\Address\Country();
                break;
            case 'addressState':
                return new Resolver\Address\State();
                break;
            case 'user':
                return new Resolver\User\User(new Mapper\User());
                break;
            case 'userAddressList':
                return new Resolver\User\AddressList(new Mapper\Address());
                break;
            case 'appConfig':
                return new Resolver\System\AppConfig();
                break;
            case 'appData':
                return new Resolver\System\AppData($this->getCartService()) ;
                break;
            case 'info':
                return new Resolver\System\Info();
                break;
            case 'authLinks':
                return new Resolver\System\AuthLinks();
                break;
            case 'faq':
                return new Resolver\Modules\FAQ();
                break;
            case 'customerOffers':
                return new Resolver\Modules\Offers();
                break;
            case 'sellerOffers':
                return new Resolver\Modules\SellerOffers();
                break;
            case 'wishlist':
                return new Resolver\Modules\WishList\WishList();
                break;
            case 'wishlistItems':
                return new Resolver\Modules\WishList\WishListItems($productMapper);
                break;
            case 'menuNotificationsVendor':
                return new MenuNotificationsVendor();
                break;
            case 'ciaValues':
                return new Resolver\Modules\CiaValues();
                break;
            case 'menuNotificationsCustomer':
                return new MenuNotificationsCustomer();
                break;
            case 'addProductToCart':
                return new Resolver\Mutations\Cart\AddProduct(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'addBatchProductsToCart':
                return new Resolver\Mutations\Cart\AddBatchProductsToCart(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'removeCartItem':
                return new Resolver\Mutations\Cart\RemoveItem(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'clearCart':
                return new Resolver\Mutations\Cart\ClearCart(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changeItemAmountByModifier':
                return new Resolver\Mutations\Cart\ChangeItemAmountByModifier(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changeItemAmount':
                return new Resolver\Mutations\Cart\ChangeItemAmount(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'addAddress':
                return new Resolver\Mutations\Cart\AddAddress(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changeAddress':
                return new Resolver\Mutations\Cart\ChangeAddress(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changeShippingMethod':
                return new Resolver\Mutations\Cart\ChangeShipping(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changePaymentMethod':
                return new Resolver\Mutations\Cart\ChangePayment(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changeCustomerNotes':
                return new Resolver\Mutations\Cart\ChangeCustomerNotes(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'changePaymentFields':
                return new Resolver\Mutations\Cart\ChangePaymentFields(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'selectAddress':
                return new Resolver\Mutations\Cart\SelectAddress(
                    $cartMapper,
                    $this->getCartService()
                );
                break;

            case 'reorderedItems':
                return new Resolver\Cart\ReorderedItems($productMapper);
                break;

            case 'bannersList':
                return new Resolver\BannersList();
                break;

            case 'dealBlock':
                return new Resolver\DealBlock();
                break;

            // User
            case 'deleteUserAddress':
                return new Resolver\Mutations\User\DeleteUserAddress(new Mapper\User());
                break;
            case 'addUserAddress':
                return new Resolver\Mutations\User\AddUserAddress(new Mapper\User());
                break;
            case 'changeUserAddress':
                return new Resolver\Mutations\User\ChangeUserAddress(new Mapper\User());
                break;
            case 'updateUserRegisterData':
                return new Resolver\Mutations\User\UpdateUser(new Mapper\User());

            // Services
            case 'auth':
                return new Resolver\Mutations\System\Auth();
            case 'externalAuth':
                return new Resolver\Mutations\System\ExternalAuth();
            case 'mergeProfiles':
                return new Resolver\Mutations\System\MergeProfiles();
            case 'recoverPassword':
                return new Resolver\Mutations\System\RecoverPassword();
            case 'register':
                return new Resolver\Mutations\System\Register(new Mapper\User());

            // Modules
            case 'productTags':
                return new Resolver\Modules\ProductTags();
                break;
            case 'vendorPlans':
                return new Resolver\Modules\VendorPlans();
                break;
            case 'vendorPlansTexts':
                return new Resolver\Modules\VendorPlansTexts();
                break;

            case 'conversations':
                return new Resolver\Modules\Conversations();
                break;
            case 'messages':
                return new Resolver\Modules\Messages();
                break;
            case 'addMessage':
                return new Resolver\Modules\AddMessage();
                break;

            case 'vendors':
                return new Resolver\Modules\Vendors();
                break;
            case 'seller':
                return new Resolver\Modules\Seller();
                break;
            case 'productAdditionalInfo':
                return new Resolver\Modules\ProductAdditionalInfo();
                break;
            case 'brands':
                return new Resolver\Modules\Brands();
                break;
            case 'addProductToWishlist':
                return new Resolver\Modules\WishList\AddProduct();
                break;
            case 'removeProductFromWishlist':
                return new Resolver\Modules\WishList\RemoveProduct();
                break;

            case 'putOffer':
                return new Resolver\Modules\PutOffer();
                break;
            case 'updateOffer':
                return new Resolver\Modules\UpdateOffer();
                break;
            case 'deleteOffer':
                return new Resolver\Modules\DeleteOffer();
                break;

            case 'addReview':
                return new Resolver\Modules\AddReview();
                break;

            case 'contactUs':
                return new Resolver\Modules\ContactUs();
                break;
            
            case 'contactUsInfo':
                return new Resolver\Modules\ContactUsPage();
                break;

            case 'questions':
                return new Resolver\Modules\Questions();
                break;
            case 'addQuestion':
                return new Resolver\Modules\AddQuestion();
                break;
            case 'updateQuestion':
                return new Resolver\Modules\UpdateQuestion();
                break;
            case 'deleteQuestion':
                return new Resolver\Modules\DeleteQuestion();
                break;

            case 'signupVendorPlan':
                return new Resolver\Modules\SignupVendorPlan(
                    $cartMapper,
                    $this->getCartService()
                );
                break;

            case 'addCartCoupon':
                return new Resolver\Modules\Coupons\AddCartCoupon(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'removeCartCoupon':
                return new Resolver\Modules\Coupons\RemoveCartCoupon(
                    $cartMapper,
                    $this->getCartService()
                );
                break;
            case 'removeBG':
                return new Resolver\Modules\RemoveBG();
            case 'tooltips' :
                return new Resolver\Tooltips();
                break;

            case 'pagesUrls' :
                return new Resolver\PagesUrls();
                break;
            case 'iconLinks' :
                return new Resolver\IconLinks();

            case 'convertToVendor' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User\ConvertToVendor(new Mapper\User());
            case 'registerVendor' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User\RegisterVendor(new Mapper\User());
            case 'productShippingSection' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\ProductShippingSection(new Mapper\ProductShippingSection());

            case 'colorSwatches' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\ColorSwatches(new Mapper\ColorSwatches());

            case 'signUpForNews' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User\SignUpForNews();

            case 'orderedProducts' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\OrderedProducts($productMapper);

            case 'productVariantImage' :
                return new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\ProductVariantImage();

            case 'specialOffer':
                return new Resolver\SpecialOffer($specialOfferMapper);
                break;

            case 'specialOffers':
                return new Resolver\SpecialOffers($specialOfferMapper);
                break;

            case 'createReview':
                return new Resolver\Mutations\Reviews\Create();
                break;

            default:
                throw new \Exception("Resolver for type \"$typeName\" not found");
        }
    }

    /**
     * @return CartService
     */
    protected function getCartService()
    {
        if(!$this->cartService) {
            $this->cartService = new CartService();
        }

        return $this->cartService;
    }
}
