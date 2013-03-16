<?php

namespace OAuth2\Provider;

interface ProviderInterface
{
	public function authorizeUrl();
    public function accessTokenUrl();
    public function getUserInfo();
}