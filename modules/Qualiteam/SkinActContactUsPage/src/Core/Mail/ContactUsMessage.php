<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\Core\Mail;

use Qualiteam\SkinActContactUsPage\View\FormField\Select\DepartmentSelect;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Mailer;
use XLite\Core\Translation;

/**
 * Contact
 * @Extender\Mixin
 */
class ContactUsMessage extends \CDev\ContactUs\Core\Mail\ContactUsMessage
{
    protected static function defineVariables()
    {
        return [
                'author_company'    => Translation::lbl('Company'),
                'author_firstname'  => Translation::lbl('Firstname'),
                'author_lastname'   => Translation::lbl('Lastname'),
                'author_address'    => Translation::lbl('Address'),
                'author_address2'   => Translation::lbl('Address (line 2)'),
                'author_city'       => Translation::lbl('City'),
                'author_country'    => Translation::lbl('Country'),
                'author_state'      => Translation::lbl('State'),
                'author_zipcode'    => Translation::lbl('Zipcode'),
                'author_phone'      => Translation::lbl('Phone'),
                'author_fax'        => Translation::lbl('fax'),
                'author_website'    => Translation::lbl('Web site'),
                'author_department' => Translation::lbl('SkinActContactUsPage Department'),
            ] + parent::defineVariables();
    }

    /**
     * ContactUsMessage constructor.
     *
     * @param \CDev\ContactUs\Model\Contact $contact
     * @param array|string                  $emails
     */
    public function __construct(\CDev\ContactUs\Model\Contact $contact, $emails)
    {
        parent::__construct($contact, $emails);

        $departments = DepartmentSelect::getDepartments();

        $author_country = '';
        $author_state = '';

        if ($contact->getCountry()) {
            $country = Database::getRepo('XLite\Model\Country')->findOneBy(['code' => $contact->getCountry()]);
            $author_country = $country ? $country->getCountry() : $contact->getCountry();
        }

        if ($contact->getState()) {
            $state = Database::getRepo('XLite\Model\State')->find($contact->getState());
            $author_state = $state ? $state->getState() : $contact->getState();
        }

        $this->populateVariables([
            'author_company'    => htmlspecialchars($contact->getCompany()),
            'author_firstname'  => htmlspecialchars($contact->getFirstname()),
            'author_lastname'   => htmlspecialchars($contact->getLastname()),
            'author_address'    => htmlspecialchars($contact->getStreet()),
            'author_address2'   => htmlspecialchars($contact->getStreet2()),
            'author_city'       => htmlspecialchars($contact->getCity()),
            'author_country'    => htmlspecialchars($author_country),
            'author_state'      => htmlspecialchars($author_state),
            'author_zipcode'    => htmlspecialchars($contact->getZipcode()),
            'author_phone'      => htmlspecialchars($contact->getPhone()),
            'author_fax'        => htmlspecialchars($contact->getFax()),
            'author_website'    => htmlspecialchars($contact->getSite()),
            'author_department' => isset($departments[$contact->getDepartment()])
                ? htmlspecialchars($departments[$contact->getDepartment()])
                : '',
        ]);
    }
}