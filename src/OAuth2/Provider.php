<?php

namespace OAuth2;

use \OAuth2\Token\Access;
use \OAuth2\Token\Authorize;

/**
 * OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

abstract class Provider
{
    public $name;

    public $uid_key = 'uid';

    public $callback;

    protected $params = array();

    protected $method = 'GET';

    protected $scope;

    protected $scope_seperator = ',';

    /**
     * Any of the provider options can be set here, such as app_id or secret.
     *
     * @param  array     $options provider options
     * @throws Exception if a required option is not provided
     */
    public function __construct(array $options = array())
    {
        if (empty($options['id'])) {
            throw new Exception('Required option not provided: id');
        }

        if (empty($options['redirect_url'])) {
            throw new Exception('Required option not provided: redirect_url');
        }

        $this->client_id = $options['id'];

        isset($options['callback']) and $this->callback = $options['callback'];
        isset($options['secret']) and $this->client_secret = $options['secret'];
        isset($options['scope']) and $this->scope = $options['scope'];

        $this->redirect_uri = $options['redirect_url'];
    }
    
    /**
     * Get the authorize URL where the user will be redirected to approve
     * the application.
     * 
     * @return string The authorize url
     */
    public abstract function authorizeUrl();
    
    /**
     * Get the access token URL
     * 
     * @return string The access token url
     */
    public abstract function accessTokenUrl();
    
    /**
     * Get information about the logged in user
     * 
     * @return array The user data
     */
    public abstract function getUserInfo();

    public function isAuthenticated()
    {
        if (isset($_GET['code'])) {
            $this->tokens = $this->access($_GET['code']);

            return true;
        } elseif (isset($this->tokens)) {
            return true;
        }

        return false;
    }

    public function getAuthenticationUrl()
    {
        if ( ! isset($_GET['code'])) {
            $params = $this->authorize();

            return "{$this->authorizeUrl()}?".http_build_query($params);
        }
    }

    public function getUserTokens()
    {
        return isset($this->tokens) ? $this->tokens : false;
    }

    public function authorize($options = array())
    {
        $state = md5(uniqid(rand(), true));

        $params = array(
            'client_id'         => $this->client_id,
            'redirect_uri'      => isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->redirect_uri,
            'state'             => $state,
            'scope'             => is_array($this->scope) ? implode($this->scope_seperator, $this->scope) : $this->scope,
            'response_type'     => 'code'
        );

        $params = array_merge($params, $this->params);

        return $params;
    }

    /*
    * Get access to the API
    *
    * @param    string  The access code
    * @return   object  Success or failure along with the response details
    */
    public function access($code, $options = array())
    {
        $params = array(
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => isset($options['grant_type']) ? $options['grant_type'] : 'authorization_code',
        );

        $params = array_merge($params, $this->params);

        switch ($params['grant_type']) {
            case 'authorization_code':
                $params['code'] = $code;
                $params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->redirect_uri;
            break;

            case 'refresh_token':
                $params['refresh_token'] = $code;
            break;
        }

        $response = null;
        $url = $this->accessTokenUrl();

        switch ($this->method) {
            case 'GET':

                // Need to switch to Request library, but need to test it on one that works
                $url .= '?'.http_build_query($params);
                $response = file_get_contents($url);

                parse_str($response, $return);

            break;

            case 'POST':

                $opts = array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($params),
                    )
                );

                $_default_opts = stream_context_get_params(stream_context_get_default());
                $context = stream_context_create(array_merge_recursive($_default_opts['options'], $opts));
                $response = file_get_contents($url, false, $context);

                $return = json_decode($response, true);

            break;

            default:
                throw new \OutOfBoundsException("Method '{$this->method}' must be either GET or POST");
        }

        if ( ! empty($return['error'])) {
            throw new Exception($return);
        }

        switch ($params['grant_type']) {
            case 'authorization_code':
                return new Access($return);
            break;

            case 'refresh_token':
                return new Refresh($return);
            break;
        }
    }
}
