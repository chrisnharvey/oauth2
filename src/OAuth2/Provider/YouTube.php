<?php

namespace OAuth2\Provider;

use \OAuth2\Token\Access;
use \OAuth2\Exception;

/**
 * YouTube OAuth2 Provider
 *
 * @package    OAuth/OAuth2
 * @category   Provider
 * @author     Benjamin David
 * @copyright  (c) 2013 Dukt.net
 * @license    http://dukt.net/
 */

class YouTube extends Google implements ProviderInterface
{

    public function __construct(array $options = array())
    {
        if(!isset($options['scope']))
        {  
            $options['scope'] = array(
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://gdata.youtube.com',
            );
        }

        parent::__construct($options);
    }
}
