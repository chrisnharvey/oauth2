<?php

namespace OAuth\Provider;

use \OAuth\OAuth2\Token\Access;

/**
 * Facebook OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

class Facebook extends \OAuth\OAuth2\Provider
{
    protected $scope = array('offline_access', 'email', 'read_stream');

    public function authorizeUrl()
    {
        return 'https://www.facebook.com/dialog/oauth';
    }

    public function accessTokenUrl()
    {
        return 'https://graph.facebook.com/oauth/access_token';
    }

    public function getUserInfo()
    {
        $url = 'https://graph.facebook.com/me?'.http_build_query(array(
            'access_token' => $this->token->access_token,
        ));

        $user = json_decode(file_get_contents($url));

        // Create a response from the request
        return array(
            'uid' => $user->id,
            'nickname' => isset($user->username) ? $user->username : null,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => isset($user->email) ? $user->email : null,
            'location' => isset($user->hometown->name) ? $user->hometown->name : null,
            'description' => isset($user->bio) ? $user->bio : null,
            'image' => 'https://graph.facebook.com/me/picture?type=normal&access_token='.$this->token->access_token,
            'urls' => array(
              'Facebook' => $user->link,
            ),
        );
    }
}
