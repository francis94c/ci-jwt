[![Build Status](https://travis-ci.org/francis94c/ci-jwt.svg?branch=master)](https://travis-ci.org/francis94c/ci-jwt) [![Coverage Status](https://coveralls.io/repos/github/francis94c/ci-jwt/badge.svg?branch=master)](https://coveralls.io/github/francis94c/ci-jwt?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/francis94c/ci-jwt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/francis94c/ci-jwt/?branch=master)

# JWT

![JWT](https://res.cloudinary.com/francis94c/image/upload/v1563336401/logo-asset.svg)

JSON Web Tokens are an open, industry standard RFC 7519 method for representing claims securely between two parties.

JWT.IO allows you to decode, verify and generate JWT.

## Installation ##
Download and Install Splint from https://splint.cynobit.com/downloads/splint and run the below from the root of your Code Igniter project.
```bash
splint install francis94c/blog
```
## Documentation ##
Load the package and initialize as needed.
```php
$this->load->package("francis94c/ci-jwt");
$params = [
  "secret"         => "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
  "algorithm"      => "HS512",
  "allow_unsigned" => false,
  "auto_expire"    => "+30 Days"
];
$this->jwt->init($params);
```
### Config/Initialization Parameters ###
| Name             | Description                                                                             |
| ---------------- | --------------------------------------------------------------------------------------- |
| `secret`         | The Secret Key used to Sign and Verify Tokens                                           |
| `algorithm`      | The Algorithm used to Sign and Verify Tokens. e.g. HS256                                |
| `allow_unsigned` | Set this to `true` if you want the `verify` function to return `true` for unsigned token. This config is set to false by default. |
| `auto_expire`    | Sets the time at which all tokens generated should be considered expired automatically.  |

### Methods ###

#### `init(array $config)` ####

This function allows you to set a couple of options that influences the behaviour of the library.

##### Example #####
```php
$this->jwt->init([
  "algorithm"   => JWT::HS512,
  "auto_expire" => "+30 Days"
]);
```

#### `header(string $key, string $value)` ####

This function adds an item to the Token's header section. _Note:_ You don't have to always set a header field, unless you want to change the signing algorithm for the current token other than the default set with `init()` as you can see from the example below.

##### Example #####
```php
$this->jwt->header("alg", JWT::HS512);
```
