[![Build Status](https://travis-ci.org/francis94c/ci-jwt.svg?branch=master)](https://travis-ci.org/francis94c/ci-jwt) [![Coverage Status](https://coveralls.io/repos/github/francis94c/ci-jwt/badge.svg?branch=master)](https://coveralls.io/github/francis94c/ci-jwt?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/francis94c/ci-jwt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/francis94c/ci-jwt/?branch=master)

# JWT

![JWT](https://res.cloudinary.com/francis94c/image/upload/v1563336401/logo-asset.svg)

JSON Web Tokens are an open, industry standard RFC 7519 method for representing claims securely between two parties.

JWT.IO allows you to decode, verify and generate JWT.

## Installation ##
Download and Install Splint from https://splint.cynobit.com/downloads/splint and run the below from the root of your Code Igniter project.
```bash
splint install francis94c/ci-jwt
```
## Documentation ##
Here's an example Usage.
Load the package and initialize as needed. Then sign or verify token.
```php
$this->load->package("francis94c/ci-jwt");
$params = [
  "secret"         => "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
  "algorithm"      => "HS512",
  "allow_unsigned" => false,
  "set_iat"        => true,
  "auto_expire"    => "+30 Days"
];
$this->jwt->init($params);

// Create Token.
$this->jwt->header("alg", JWT::HS384);
$this->jwt->payload("sub", "john");
$this->jwt->payload("iss", "www.example.com");
$this->jwt->expire("+5 Days");
$token = $this->jwt->sign();

// Process a Token.
if ($this->jwt->verify($token)) {
  $this->jwt->create(); // Clears payload and header array. This is required when working with fresh token data.
  $this->jwt->decode($token);
  $sub = $this->jwt->payloadArray()["sub"];
  $iss = $this->jwt->payload("iss");
  // Other Procedures Here.
} else {
  echo "Invalid Token!";
}

// OR

$this->jwt->create(); // Clears payload and header array. This is required when working with fresh token data.
$this->jwt->decode($token);
if ($this->jwt->verify()) {
  $sub = $this->jwt->payloadArray()["sub"];
  $iss = $this->jwt->payload("iss");
  // Other Procedures Here.
} else {
  echo "Invalid Token!";
}
```
### Config/Initialization Parameters ###
| Name             | Description                                                                             |
| ---------------- | --------------------------------------------------------------------------------------- |
| `secret`         | The Secret Key used to Sign and Verify Tokens                                           |
| `algorithm`      | The Algorithm used to Sign and Verify Tokens. e.g. HS256                                |
| `allow_unsigned` | Set this to `true` if you want the `verify` function to return `true` for unsigned token. This config is set to false by default. |
| `set_iat`        | Set this to true if you want the `iat` claim to be set to the time the token was created when you extract/sign the token by calling the `sign()` function. |
| `auto_expire`    | Sets the time at which all tokens generated should be considered expired automatically.  |

### Methods ###

#### `init(array $config):void` ####

This method allows you to set a couple of options that influences the behaviour of the library.

##### Example #####
```php
$this->jwt->init([
  "algorithm"   => JWT::HS512,
  "auto_expire" => "+30 Days"
]);
```

---

#### `create():void` ####

Essentially creates a new token. This results in the clearing of the header and payload array for input of fresh data.

##### Example #####
```php
$this->jwt->create();
```

---

#### `header(string $key, string|int|array $value):void` ####

This method adds an item in the Token's header section. _Note:_ You don't have to always set a header field, unless you want to change the signing algorithm for the current token other than the default set with `init()` as you can see from the example below. It returns the value of the given key if the `$value` argument is not supplied.

##### Example #####
```php
$this->jwt->header("alg", JWT::HS512);
// Supported algorithms are HS256, HS512, & HS384

$alg = $this->jwt->header("alg");
```

---

#### `headerArray():array` ####

Returns an associative array representing the contents of the token header.

##### Example #####
```php
$header = $this->jwt->headerArray();
echo $header["alg"];  // "HS256";
```

---

#### `payload(string $key, string|int|array $value)` ####

This method adds an item (string, int array, It's JSON after all) to the payload section of an array. It returns the value of the given key if the `$value` argument is not supplied.

##### Example #####
```php
$this->jwt->payload("sub", "username");

$sub = $this->jwt->payload("sub");
```

---

#### `payloadArray()` ####

This method adds an item (string, int array, It's JSON after all) to the payload section of an array.

##### Example #####
```php
$payload = $this->jwt->payloadArray();
echo $payload["sub"];
```
---

#### `sign([string $secret])` ####

This method generates a signed token (JWT), using the secret set with the `init()` function or the `$secret` argument if supplied.

##### Example #####
```php
$token = $this->jwt->sign();
echo $token;
// OR
$token = $this->jwt->sign("A_SECRET_KEY_HERE");
echo $token;
```
---
