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

use lukeyouell\salesforceleads\models\Settings;
use lukeyouell\salesforceleads\utilities\Log;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\services\Utilities;

use yii\base\Event;

/**
 * Class SalesforceLeads
 *
 * @author    Luke Youell
 * @package   SalesforceLeads
 * @since     1.0.0
 *
 */
class SalesforceLeads extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;

    // Public Properties
    // =========================================================================
    
    public $schemaVersion = '1.1.0';

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

        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Log::class;
            }
        );

        // Register components
        $this->setComponents([
            'postService'       => \lukeyouell\salesforceleads\services\PostService::class,
            'validationService' => \lukeyouell\salesforceleads\services\ValidationService::class,
            'logService'        => \lukeyouell\salesforceleads\services\LogService::class,
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
