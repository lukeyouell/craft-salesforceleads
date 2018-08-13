<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads\utilities;

use Craft;
use craft\base\Utility;
use craft\services\SystemSettings;

use lukeyouell\salesforceleads\SalesforceLeads;

class Logs extends Utility
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('salesforce-leads', 'Salesforce Leads Logs');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'salesforce-leads-logs';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@lukeyouell/salesforceleads/icon-mask.svg");
    }

    /**
     * @inheritdoc
     */
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
        return SalesforceLeads::getInstance()->logService->getLogs();
    }
}
