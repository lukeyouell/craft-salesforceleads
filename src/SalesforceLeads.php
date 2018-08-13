<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads;

use lukeyouell\salesforceleads\services\SalesforceLeadsService as SalesforceLeadsServiceService;
use lukeyouell\salesforceleads\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;

use yii\base\Event;

/**
 * Class SalesforceLeads
 *
 * @author    Luke Youell
 * @package   SalesforceLeads
 * @since     1.0.0
 *
 * @property  SalesforceLeadsServiceService $salesforceLeadsService
 */
class SalesforceLeads extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var SalesforceLeads
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('settings/plugins/salesforce-leads'))->send();
                }
            }
        );

        // Register components
        $this->setComponents([
            'postService'       => \lukeyouell\salesforceleads\services\PostService::class,
            'validationService' => \lukeyouell\salesforceleads\services\ValidationService::class,
        ]);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        // Get the settings that are being defined by the config file
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate(
            'salesforce-leads/settings',
            [
                'settings'       => $settings,
                'overrides'      => array_keys($overrides),
                'emailValidator' => Craft::$app->plugins->getPlugin('email-validator')
            ]
        );
    }
}
