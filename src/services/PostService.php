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

/**
 * @author    Luke Youell
 * @package   SalesforceLeads
 * @since     1.0.0
 */
class PostService extends Component
{
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

          return [
            'success' => true,
            'statusCode' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'body' => (string) $response->getBody(),
            'payload' => $request
          ];

        } catch (\Exception $e) {

          return [
            'success' => false,
            'reason' => $e->getMessage(),
            'payload' => $request
          ];

        }
    }
}
