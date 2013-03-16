# OAuth 2.0 Composer Package

Authorize users with your application using multiple OAuth 2 providers.

## Supported Providers

- Appnet
- Facebook
- Foursquare
- GitHub
- Google
- Instagram
- Mailchimp
- Mailru
- PayPal
- Soundcloud
- Vkontakte
- Windows Live
- Yandex

## Usage Example

In this example we will authenticate the user using Facebook.

```php
$oauth = \OAuth2\Provider\Facebook(array(
	'id' => 'CLIENT_ID',
	'secret' => 'CLIENT_SECRET',
	'redirect_url' => 'URL_TO_THIS_PAGE'
));

if ( ! $oauth->isAuthenticated()) {
	header("Location: {$oauth->getAuthenticationUrl()}");
	exit;
}

// Tokens
print_r($oauth->getUserTokens());

// User data
print_r($oauth->getUserInfo());
```

If all goes well you should see a dump of the users tokens and data.

### Calling OAuth 2 APIs using Guzzle

You can also use this package to make calls to your respective APIs 
using Guzzle.

```php
$client = new \OAuth2\Client('https://graph.facebook.com');
$client->setUserTokens($oauth->getUserTokens());

echo $client->get('me')->send();
```

This example should show your Facebook profile from the API along with the headers

## Contribute

1. Check for open issues or open a new issue for a feature request or a bug
2. Fork [the repository][] on Github to start making your changes to the
    `develop` branch (or branch off of it)
3. Write a test which shows that the bug was fixed or that the feature works as expected
4. Send a pull request and bug me until I merge it

[the repository]: https://github.com/chrisnharvey/oauth2
