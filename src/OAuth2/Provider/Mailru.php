<?php

namespace OAuth2\Provider;

use \OAuth2\Provider;
use \OAuth2\Token\Access;

/**
 * Mailru OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Lavr Lyndin
 */

class Mailru extends Provider implements ProviderInterface
{
    public $method = 'POST';

    public function authorizeUrl()
    {
        return 'https://connect.mail.ru/oauth/authorize';
    }

    public function accessTokenUrl()
    {
        return 'https://connect.mail.ru/oauth/token';
    }

    protected function signServerServer(array $request_params, $secret_key)
    {
        ksort($request_params);
        $params = '';
        foreach ($request_params as $key => $value) {
            $params .= "$key=$value";
        }

        return md5($params . $secret_key);
    }

    public function getUserInfo()
    {
        $request_params = array(
            'app_id' => $this->consumer->client_id,
            'method' => 'users.getInfo',
            'uids' => $this->token->uid,
            'access_token' => $this->token->access_token,
            'secure' => 1
        );

        $sig = $this->signServerServer($request_params,$this->client_secret);
        $url = 'http://www.appsmail.ru/platform/api?'.http_build_query($request_params).'&sig='.$sig;

        $user = json_decode(file_get_contents($url));

        return array(
            'uid' => $user[0]->uid,
            'nickname' => $user[0]->nick,
            'name' => $user[0]->first_name.' '.$user[0]->last_name,
            'first_name' => $user[0]->first_name,
            'last_name' => $user[0]->last_name,
            'email' => isset($user[0]->email) ? $user[0]->email : null,
            'image' => isset($user[0]->pic_big) ? $user[0]->pic_big : null,
        );
    }

    public function authorize($options = array())
    {
        $state = md5(uniqid(rand(), true));
        $_SESSION['state'] = $state;

        $params = array(
            'client_id'         => $this->client_id,
            'redirect_uri'      => isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->redirect_uri,
            'response_type'     => 'code',
        );

        header("Location: {$this->url_authorize()}?".http_build_query($params));
    }
}
