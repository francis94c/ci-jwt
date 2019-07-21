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
## Usage ##
Load the package and initialize as needed.
```php
$this->load->package("francis94c/ci-jwt");
$params = [
  "secret"         => "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
  "algorithm"      => "HS512",
  "allow_unsigned" => false,
  "auto_expire"    => "+30 Days"
];
$this->jwt-.init($params);
```
### Config/Initialization Parameter ###
