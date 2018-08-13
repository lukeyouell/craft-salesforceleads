<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads\services;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\base\Component;

class ValidationService extends Component
{
    // Public Methods
    // =========================================================================

    public static function checkHoneypot($param = null, $val = null)
    {
        if ($val === null) {
            Craft::error('Couldn\'t check honeypot field because no POST parameter named "'.$param.'" exists.');
            return false;
        }

        if ($val !== '') {
            Craft::info('Salesforce Leads submission detected as spam.');
            return true;
        }
    }
}
