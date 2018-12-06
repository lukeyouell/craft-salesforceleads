<?php

namespace lukeyouell\salesforceleads\utilities;

use Craft;
use craft\base\Utility;
use craft\services\SystemSettings;

use lukeyouell\salesforceleads\SalesforceLeads;

class Log extends Utility
{
    // Static
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('salesforce-leads', 'Salesforce Leads');
    }

    public static function id(): string
    {
        return 'salesforce-leads';
    }

    public static function iconPath()
    {
        return Craft::getAlias("@lukeyouell/salesforceleads/icon-mask.svg");
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'salesforce-leads/utility',
            [
                'logs' => self::getLogs()
            ]
        );
    }

    public static function getLogs()
    {
        return SalesforceLeads::getInstance()->log->getLogs();
    }
}
