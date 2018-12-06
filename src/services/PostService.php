<?php

namespace lukeyouell\salesforceleads\services;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\base\Component;
use lukeyouell\salesforceleads\events\SendEvent;

class PostService extends Component
{
    // Constants
    // =========================================================================

    const EVENT_BEFORE_SEND = 'beforeSend';

    const EVENT_AFTER_SEND = 'afterSend';

    // Public Methods
    // =========================================================================

    public static function cleanPost($post)
    {
        unset(
          $post['CRAFT_CSRF_TOKEN'],
          $post['action'],
          $post['redirect']
        );

        return $post;
    }

    public static function postRequest($request)
    {
        $settings = SalesforceLeads::$plugin->getSettings();

        // Fire a 'beforeSend' event
        $event = new SendEvent([
          'submission' => $request,
        ]);
        $self = new static;
        $self->trigger(self::EVENT_BEFORE_SEND, $event);

        $client = new \GuzzleHttp\Client([
          'base_uri' => 'https://webto.salesforce.com',
          'http_errors' => false,
          'timeout' => 10
        ]);

        try {

          $response = $client->request(
            'POST',
            'servlet/servlet.WebToLead',
            [
              'form_params' => $request
            ]
          );

          // Fire an 'afterSend' event
          $event = new SendEvent([
            'submission' => $request,
          ]);
          $self = new static;
          $self->trigger(self::EVENT_AFTER_SEND, $event);

          // Unset data we don't want to return in the response
          unset($request['oid'], $request[$settings->honeypotParam]);

          return [
            'success' => true,
            'statusCode' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'body' => (string) $response->getBody(),
            'payload' => $request
          ];

        } catch (\Exception $e) {

          // Unset data we don't want to return in the response
          unset($request['oid'], $request[$settings->honeypotParam]);

          return [
            'success' => false,
            'reason' => $e->getMessage(),
            'payload' => $request
          ];

        }
    }
}
