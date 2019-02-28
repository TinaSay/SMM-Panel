<?php

namespace App\Providers;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\Data;
use Hybridauth\HttpClient;
use Hybridauth\Exception\InvalidAuthorizationStateException;
use Hybridauth\Exception\InvalidAccessTokenException;

class BillingOauthProvider extends OAuth2
{
    protected $scope = '';

    protected $apiBaseUrl = 'https://billing.smm-pro.uz/api/';

    protected $authorizeUrl = 'https://billing.smm-pro.uz/oauth/authorize';

    protected $accessTokenUrl = 'https://billing.smm-pro.uz/oauth/token';

    protected $supportRequestState = false;

    public function authenticate($get = [])
    {
        $this->logger->info(sprintf('%s::authenticate()', get_class($this)));

        if ($this->isConnected()) {
            return true;
        }

        try {
            $this->authenticateCheckError();

            $code = isset($get['code']) ? $get['code'] : null;

            if (empty($code)) {
                $this->authenticateBegin();
            } else {
                $this->authenticateFinish($code);
            }
        } catch (\Exception $e) {
            $this->clearStoredData();

            throw $e;
        }

        return null;
    }

    /**
     * Initiate the authorization protocol
     *
     * Build Authorization URL for Authorization Request and redirect the user-agent to the
     * Authorization Server.
     */
    protected function authenticateBegin()
    {
        $authUrl = $this->getAuthorizeUrl();

        $this->logger->debug(sprintf('%s::authenticateBegin(), redirecting user to:', get_class($this)), [$authUrl]);

        HttpClient\Util::redirect($authUrl);
    }

    /**
     * Build Authorization URL for Authorization Request
     *
     * RFC6749: The client constructs the request URI by adding the following
     * $parameters to the query component of the authorization endpoint URI:
     *
     *    - response_type  REQUIRED. Value MUST be set to "code".
     *    - client_id      REQUIRED.
     *    - redirect_uri   OPTIONAL.
     *    - scope          OPTIONAL.
     *    - state          RECOMMENDED.
     *
     * http://tools.ietf.org/html/rfc6749#section-4.1.1
     *
     * Sub classes may redefine this method when necessary.
     *
     * @param array $parameters
     *
     * @return string Authorization URL
     */
    protected function getAuthorizeUrl($parameters = [])
    {
        $this->AuthorizeUrlParameters = !empty($parameters)
            ? $parameters
            : array_replace(
                (array)$this->AuthorizeUrlParameters,
                (array)$this->config->get('authorize_url_parameters')
            );

        if ($this->supportRequestState) {
            if (!isset($this->AuthorizeUrlParameters['state'])) {
                $this->AuthorizeUrlParameters['state'] = 'HA-' . str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
            }

            $this->storeData('authorization_state', $this->AuthorizeUrlParameters['state']);
        }

        return $this->authorizeUrl . '?' . http_build_query($this->AuthorizeUrlParameters, '', '&');
    }

    /**
     * Finalize the authorization process
     *
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidAuthorizationStateException
     */
    protected function authenticateFinish($code = null)
    {
        $this->logger->debug(
            sprintf('%s::authenticateFinish(), callback url:', get_class($this)),
            [HttpClient\Util::getCurrentUrl(true)]
        );

        $state = filter_input(INPUT_GET, 'state');

        /**
         * Authorization Request State
         *
         * RFC6749: state : RECOMMENDED. An opaque value used by the client to maintain
         * state between the request and callback. The authorization server includes
         * this value when redirecting the user-agent back to the client.
         *
         * http://tools.ietf.org/html/rfc6749#section-4.1.1
         */
        if ($this->supportRequestState
            && $this->getStoredData('authorization_state') != $state
        ) {
            throw new InvalidAuthorizationStateException(
                'The authorization state [state=' . substr(htmlentities($state), 0, 100) . '] '
                . 'of this page is either invalid or has already been consumed.'
            );
        }

        /**
         * Authorization Request Code
         *
         * RFC6749: If the resource owner grants the access request, the authorization
         * server issues an authorization code and delivers it to the client:
         *
         * http://tools.ietf.org/html/rfc6749#section-4.1.2
         */
        $response = $this->exchangeCodeForAccessToken($code);

        $this->validateAccessTokenExchange($response);

        $this->initialize();
    }

    protected function initialize()
    {
        parent::initialize();
    }

    public function getUserProfile()
    {
        $response = $this->apiRequest('user');

        $data = new Data\Collection($response);

        $userProfile = new \stdClass;

        if (!$data->exists('id')) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        $userProfile->identifier = $data->get( 'id' );
        $userProfile->username = $data->get('name');
        $userProfile->email = $data->get( 'email' );
        $userProfile->socialId = $data->get('social_id');
        $userProfile->status = $data->get('status');
        $userProfile->balance = $data->get('balance');
        $userProfile->token = $this->getStoredData('access_token');
        $userProfile->role = $data->get('role');

        return $userProfile;
    }
}
