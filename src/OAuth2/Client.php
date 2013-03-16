<?php

namespace OAuth2;

use \Guzzle\Http\Client as GuzzleClient;
use \OAuth2\Token\Access as AccessToken;
use \InvalidArgumentException;

class Client extends GuzzleClient
{
    protected $tokens;

    public function __construct($baseUrl = '', $config = null, AccessToken $tokens = null)
    {
        if ($tokens) $this->setUserTokens($tokens);

        parent::__construct($baseUrl, $config);
    }

    public function setUserTokens(AccessToken $tokens)
    {
        $this->tokens = $tokens;

        return $this;
    }

    public function setBaseUrl($url)
    {
        $url .= strpos($url, '?') ? '&' : '?';
        $url .= http_build_query(array(
            'access_token' => $this->tokens->access_token
        ));

        $this->baseUrl = $url;

        return $this;
    }
}