<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ResolverFactoryInterface;
use XcartGraphqlApi\Types;
use Qualiteam\SkinActGraphQLApi\Core\CommonRoutines;

/**
 * Class MutationType
 * @package XcartGraphqlApi\Types
 */
class MutationType extends ObjectType
{
    /**
     * MutationType constructor.
     *
     * @param ResolverFactoryInterface $factory
     *
     * @throws \Exception
     */
    public function __construct(ResolverFactoryInterface $factory)
    {
        $config = [
            'name'         => 'Mutation',
            'fields'       => [
                // Cart
                'addProductToCart' => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Add product to cart',
                    'args'        => [
                        'product_id' => Types::nonNull(Types::id()),
                        'amount'     => Types::int(),
                        'attributes' => Types::listOf(Types::string()),
                    ]
                ],
                'addBatchProductsToCart' => [
                    'type' => Types::byName('cart'),
                    'description' => 'Add batch products to cart',
                    'args' => [
                        'productsLines' => Types::nonNull(
                            Types::listOf(Types::byName('product_batch_line_input'))
                        ),
                    ]
                ],
                'removeCartItem'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Remove orderItem from cart',
                    'args'        => [
                        'item_id' => Types::nonNull(Types::id()),
                    ]
                ],
                'clearCart'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Clear cart',
                ],
                'changeItemAmountByModifier'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change orderItem amount',
                    'args' => [
                        'item_id'         => Types::nonNull(Types::id()),
                        'amount_modifier' => Types::nonNull(Types::int()),
                    ]
                ],
                'changeItemAmount'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change orderItem amount',
                    'args' => [
                        'item_id' => Types::nonNull(Types::id()),
                        'amount'  => Types::nonNull(Types::int()),
                    ]
                ],
                'addAddress'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Add address',
                    'args' => [
                        'type'    => Types::nonNull(Types::byName('addressTypeEnum')),
                        'address' => Types::nonNull(Types::byName('address_input')),
                    ]
                ],
                'changeAddress'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change address',
                    'args' => [
                        'type'    => Types::nonNull(Types::byName('addressTypeEnum')),
                        'address' => Types::nonNull(Types::byName('address_input')),
                    ]
                ],
                'changePaymentMethod' => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change payment method of the cart',
                    'args'        => [
                        'payment_id' => Types::nonNull(Types::id()),
                    ]
                ],
                'changePaymentFields' => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change payment method fields for cart',
                    'args' => [
                        'fields' => Types::nonNull(
                            Types::listOf(Types::byName('payment_fields_input'))
                        ),
                    ]
                ],
                'changeShippingMethod' => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change shipping method of the cart',
                    'args'        => [
                        'shipping_id' => Types::nonNull(Types::id()),
                    ]
                ],
                'changeCustomerNotes' => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Change customer notes of the cart',
                    'args'        => [
                        'notes' => Types::nonNull(Types::string()),
                    ]
                ],
                'selectAddress'      => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Select address by id',
                    'args' => [
                        'type'    => Types::nonNull(Types::byName('addressTypeEnum')),
                        'address_id' => Types::nonNull(Types::id()),
                    ]
                ],
                // Order
                'deleteOrder'     => [
                    'type'        => Types::boolean(),
                    'description' => 'Delete Order',
                    'args' => [
                        'order_id'    => Types::nonNull(Types::id()),
                    ]
                ],
                'updateOrder'      => [
                    'type'        => Types::byName('order'),
                    'description' => 'Update Order',
                    'args' => [
                        'order_id'          => Types::nonNull(Types::id()),
                        'shippingStatus'    => Types::string(),
                        'paymentStatus'     => Types::string(),
                    ]
                ],
                // User
                'deleteUserAddress'      => [
                    'type'        => Types::byName('user'),
                    'description' => 'Remove address from address book',
                    'args' => [
                        'address_id'    => Types::nonNull(Types::id()),
                    ]
                ],
                'addUserAddress'      => [
                    'type'        => Types::byName('user'),
                    'description' => 'Add new address to address book',
                    'args' => [
                        'address' => Types::nonNull(Types::byName('address_input')),
                    ]
                ],
                'changeUserAddress'      => [
                    'type'        => Types::byName('user'),
                    'description' => 'Remove address from address book',
                    'args' => [
                        'address_id'    => Types::nonNull(Types::id()),
                        'address' => Types::nonNull(Types::byName('address_input')),
                    ]
                ],
                'updateUserRegisterData' => [
                    'type'        => Types::byName('user'),
                    'description' => 'Update profile',
                    'args'        => [
                        'currentPassword' => Types::nonNull(Types::string()),
                        'data'        => Types::nonNull(Types::byName('UserUpdateInput')),
                    ],
                ],

                // System
                'auth' => [
                    'type'        => Types::string(),
                    'description' => 'Get auth token for plain client ID or optionally for system user',
                    'args'        => [
                        'auth'       => Types::nonNull(Types::byName('AuthInput')),
                        'client'     => Types::byName('ClientInput')
                    ],
                ],
                'externalAuth' => [
                    'type'        => Types::string(),
                    'description' => 'Get auth token for plain client ID or optionally for system user by using external token',
                    'args'        => [
                        'auth'       => Types::nonNull(Types::byName('ExternalAuthInput')),
                        'client'     => Types::byName('ClientInput')
                    ],
                ],
                'mergeProfiles' => [
                    'type'        => Types::boolean(),
                    'description' => 'Merge anonymous profile with current logged in registered profile',
                    'args'        => [
                        'anonymous_jwt' => Types::string()
                    ],
                ],
                'recoverPassword' => [
                    'type'        => Types::boolean(),
                    'description' => 'Recover password for provided login',
                    'args'        => [
                        'login'     => Types::nonNull(Types::string()),
                    ],
                ],
                'register' => [
                    'type'        => Types::byName('registrationResult'),
                    'description' => 'Register profile',
                    'args'        => [
                        'data'       => Types::nonNull(Types::byName('UserRegisterInput')),
                        'client'     => Types::byName('ClientInput'),
                    ],
                ],

                // MODULES

                // QSL\MyWishlist
                'addProductToWishlist' => [
                    'type'        => Types::byName('wishlist'),
                    'description' => 'Add product to wishlist',
                    'args'        => [
                        'product_id' => Types::nonNull(Types::id()),
                    ]
                ],
                'removeProductFromWishlist' => [
                    'type'        => Types::byName('wishlist'),
                    'description' => 'Remove product from wishlist',
                    'args'        => [
                        'product_id' => Types::nonNull(Types::id()),
                    ]
                ],

                'addReview' => [
                    'type'        => Types::byName('review'),
                    'description' => 'Add a review to product',
                    'args'        => [
                        'review'      => Types::nonNull(Types::string()),
                        'name'        => Types::nonNull(Types::string()),
                        'rating'      => Types::nonNull(Types::int()),
                        'product_id'  => Types::nonNull(Types::id()),
                    ]
                ],

                'addQuestion' => [
                    'type'        => Types::byName('question'),
                    'description' => 'Add a question to product',
                    'args'        => [
                        'question'    => Types::nonNull(Types::string()),
                        'name'        => Types::nonNull(Types::string()),
                        'private'     => Types::nonNull(Types::boolean()),
                        'product_id'  => Types::nonNull(Types::id()),
                    ]
                ],

                'updateQuestion' => [
                    'type'        => Types::byName('question'),
                    'description' => 'Update questions for products',
                    'args'        => [
                        'id'          => Types::nonNull(Types::id()),
                        'private'     => Types::nonNull(Types::boolean()),
                        'answer'      => Types::nonNull(Types::string())
                    ]
                ],

                'deleteQuestion' => [
                    'type'        => Types::boolean(),
                    'description' => 'Delete question for products',
                    'args'        => [
                        'id'          => Types::nonNull(Types::id()),
                    ]
                ],

                // //CSI\MakeAnOffer
                'deleteOffer' => [
                    'type'        => Types::byName('offer'),
                    'description' => 'Delete offer for product (by vendor)',
                    'args'        => [
                        'id' => Types::nonNull(Types::id()),
                    ]
                ],
                'updateOffer' => [
                    'type'        => Types::byName('offer'),
                    'description' => 'Update offer for product (by vendor)',
                    'args'        => [
                        'id'                    => Types::nonNull(Types::id()),
                        'status'                => Types::nonNull(Types::byName('offersTypeEnum')),
                        'not_visible_notes'     => Types::string(),
                        'visible_notes'         => Types::string(),
                        'send_changes_email'    => Types::nonNull(Types::boolean()),
                    ]
                ],
                'putOffer' => [
                    'type'        => Types::byName('offer'),
                    'description' => 'Put offer for product (by customer)',
                    'args'        => [
                        'name'          => Types::nonNull(Types::string()),
                        'email'         => Types::nonNull(Types::string()),
                        'phone'         => Types::string(),
                        'offer_qty'     => Types::nonNull(Types::int()),
                        'offer_price'   => Types::nonNull(Types::float()),
                        'product_id'    => Types::nonNull(Types::int()),
                        'comments'      => Types::string(),
                    ]
                ],
                // \\CSI\MakeAnOffer

                // Qualiteam
                'signupVendorPlan'      => [
                    'type'        => Types::byName('vendorPlan'),
                    'description' => 'Signup for vendor plan',
                    'args' => [
                        'id'    => Types::nonNull(Types::id()),
                    ]
                ],

                // CDev\Coupons
                'addCartCoupon'    => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Add coupon to cart',
                    'args'        => [
                        'code'  => Types::nonNull(Types::string()),
                    ]
                ],
                'removeCartCoupon' => [
                    'type'        => Types::byName('cart'),
                    'description' => 'Remove coupon from cart',
                    'args'        => [
                        'code'  => Types::nonNull(Types::string()),
                    ]
                ],

                'contactUs' => [
                    'type'        => Types::boolean(),
                    'description' => 'Send message to service',
                    'args'        => [
                        //'name'      => Types::nonNull(Types::string()),
                        'email'     => Types::nonNull(Types::string()),
                        'subject'   => Types::nonNull(Types::string()),
                        'message'   => Types::nonNull(Types::string()),
                        //'login'     => Types::string(),
                        'company'   => Types::nonNull(Types::string()),
                        'firstname' => Types::nonNull(Types::string()),
                        'lastname'  => Types::nonNull(Types::string()),
                        'address'   => Types::nonNull(Types::string()),
                        'address2'  => Types::string(),
                        'city'      => Types::nonNull(Types::string()),
                        'country'   => Types::nonNull(Types::string()),
                        'state'     => Types::nonNull(Types::string()),
                        'zipcode'   => Types::nonNull(Types::string()),
                        'phone'     => Types::nonNull(Types::string()),
                        'fax'       => Types::string(),
                        'url'       => Types::string(),
                        'department'=> Types::nonNull(Types::string()),
                    ]
                ],

                'signUpForNews' => [
                    'type'        => Types::boolean(),
                    'description' => 'Sign-up for emails',
                    'args'        => [
                        'email'     => Types::nonNull(Types::string()),
                    ]
                ],

                // XC\Messages
                'addMessage' => [
                    'type'        => Types::boolean(),
                    'description' => 'Add message for order',
                    'args'        => [
                        'orderNumber'   => Types::nonNull(Types::string()),
                        'message'       => Types::nonNull(Types::string())
                    ]
                ],

                //Remove BG
                'removeBG' => [
                    'type'        => Types::boolean(),
                    'description' => 'Remove background for images',
                    'args'        => [
                        'images_id'   => Types::listOf(Types::nonNull(Types::int())),
                    ]
                ],

                // ?target=convert_to_vendor form
                'convertToVendor' => [
                    'type' => Types::byName('user'),
                    'description' => 'Convert user to vendor',
                    'args' => CommonRoutines::getInstance()->getTypeArgsConvert()
                ],

                'registerVendor' => [
                    'type' => Types::byName('user'),
                    'description' => 'Apply for a vendor account',
                    'args' => CommonRoutines::getInstance()->getTypeArgsRegister()
                ],

                'createReview' => [
                    'type' => Types::boolean(),
                    'description' => 'Create yotpo review',
                    'args' => [
                        'sku' => Types::nonNull(Types::string()),
                        'product_title' => Types::nonNull(Types::string()),
                        'product_url' => Types::nonNull(Types::string()),
                        'display_name' => Types::nonNull(Types::string()),
                        'email' => Types::nonNull(Types::string()),
                        'review_content' => Types::nonNull(Types::string()),
                        'review_title' => Types::nonNull(Types::string()),
                        'review_score' => Types::nonNull(Types::int()),
                    ]
                ],
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) use ($factory) {
                $resolver = $factory->createForType($info->fieldName);
                return $resolver(
                    $value, $args, $context, $info
                );
            }
        ];

        parent::__construct($config);
    }
}
