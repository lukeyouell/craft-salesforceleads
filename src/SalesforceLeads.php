<?php

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
            'post'       => \lukeyouell\salesforceleads\services\PostService::class,
            'validation' => \lukeyouell\salesforceleads\services\ValidationService::class,
            'log'        => \lukeyouell\salesforceleads\services\LogService::class,
        ]);
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

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
