<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JWT {

  const JWT = "jwt";

  // Signing Algorithms.
  const NONE  = 'none';
  const HS256 = 'HS256';
  const HS512 = 'HS512';
  const HS384 = 'HS384';

  // JWT Standard Algs to PHP Algs.
  const ALGOS = [
    self::HS256 => 'sha256',
    self::HS512 => 'sha512',
    self::HS384 => 'sha384'
  ];

  // Internal Variables.
  /**
   * [private Default Signing Secret]
   * @var string
   */
  private $secret = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  /**
   * [private JWT header array]
   * @var array
   */
  private $header = [];
  /**
   * [private JWT payload array]
   * @var string
   */
  private $payload = [];
  /**
   * [private Allow Unsigned JWT]
   * @var bool
   */
  private $allow_unsigned = false;
  /**
   * [private Set Issued at Time]
   * @var bool
   */
  private $set_iat = true;
  /**
   * [private Auto expire at. Argument for PHP 'strtotime' function.]
   * @var string
   */
  private $auto_expire;
  /**
   * [private Default Signing Algorithm]
   * @var string
   */
  private $algorithm;

  function __construct($params=null) {
    if ($params != null) $this->init($params);
    get_instance()->load->splint("francis94c/ci-jwt", "%base64");
  }
  /**
   * [init For setting config variables.]
   * @param  array  $config Config Options.
   */
  public function init(array $config) {
    $this->secret = $config["secret"] ?? $this->secret;
    $this->allow_unsigned = $config["allow_unsigned"] ?? $this->allow_unsigned;
    $this->auto_expire = $config["auto_expire"] ?? $this->auto_expire;
    $this->algorithm = $config["algorithm"] ?? $this->algorithm;
  }
  /**
   * [header Add an item to the header array.]
   * @param  string     $key   Key of the item. e.g "alg", "typ".
   * @param  string|int $value Value of the item.
   */
  public function header(string $key, $value):void {
    $this->header[$key] = $value;
  }
  /**
   * [headerArray Returns the header array.]
   * @return array Header Array.
   */
  public function headerArray(): array {
    return $this->header;
  }
  /**
   * [payload description]
   * @param string $key   [description]
   * @param [type] $value [description]
   */
  public function payload(string $key, $value):void {
    $this->payload[$key] = $value;
  }
  /**
   * [payloadarray description]
   * @return array [description]
   */
  public function payloadarray(): array {
    return $this->payload;
  }
  /**
   * [create Start afresh, empty/reset header and ]
   * @return [type] [description]
   */
  public function create():void {
    $this->header = [];
    $this->payload = [];
  }
  /**
   * [sign description]
   * @param  [type] $secret [description]
   * @return [type]         [description]
   */
  public function sign(string $secret=null):?string {
    // Checks.
    if  (count($this->payload) == 0) return null;
    // $key is $secret.
    $key = $secret ?? $this->secret;
    $this->header["alg"] = $this->header["alg"] ?? ($this->algorithm ?? self::HS512);
    $this->header["typ"] = $this->header["typ"] ?? self::JWT;
    // Generate Issued At Time.
    if ($this->set_iat) $this->payload["iat"] = $this->payload["iat"] ?? time();
    // Auto Expire.
    if ($this->auto_expire != null) $this->payload["exp"] = strtotime($this->auto_expire);
    $jwt = base64url_encode(json_encode($this->header));
    if ($jwt === false) return null;
    if ($jwt != "") $jwt .= ".";
    $payload = base64url_encode(json_encode($this->payload));
    $jwt .= $payload;
    if ($key != "") return $this->sign_token($jwt, $key, $this->header["alg"]);
    return $jwt . ".";
  }
  /**
   * [token description]
   * @return string [description]
   */
  public function token():?string {
    // Checks.
    if  (count($this->payload) == 0) return null;
    // Begin.
    $this->header["alg"] = self::NONE;
    if ($this->set_iat) $this->payload["iat"] = $this->payload["iat"] ?? time();
    if ($this->auto_expire != null) $this->payload["exp"] = strtotime($this->auto_expire);
    return base64url_encode(json_encode($this->header)) . "." . base64url_encode(json_encode($this->payload)) . ".";
  }
  /**
   * [verify description]
   * @param  string $jwt    [description]
   * @param  string $secret [description]
   * @return bool           [description]
   */
  public function verify(string $jwt, string $secret=null):bool {
    if (substr_count($jwt, ".") != 2) return false; // Invalid JWT.
    $key = $secret ?? $this->secret;
    $parts = explode(".", $jwt);
    $header = json_decode(base64url_decode($parts[0]) ,true);
    if ($header == null) return false;
    $alg = $header["alg"] ?? self::HS256;
    $payload = json_decode(base64url_decode($parts[1]) ,true);
    if ($payload == null) return false;
    if ($parts[2] == "") {
      return $this->allow_unsigned;
    }
    return $this->hashmac($alg, $parts[0] . "." . $parts[1], $parts[2], $key);
  }
  /**
   * [expire Sets expiry date of JWT. This basically assigns the return value of
   *         PHP's 'strtotime()' function to the 'exp' field of the payload,
   *         passing it the $when argument.
   *         see https://www.php.net/manual/en/function.strtotime.php]
   * @param string $when Future time e.g +1 Week, +1 week 2 days 4 hours 2 seconds.
   */
  public function expire(string $when):void {
    $this->payload["exp"] = strtotime($when);
  }
  /**
   * [decode description]
   * @param  string  $jwt [description]
   * @return boolean      [description]
   */
  public function decode(string $jwt):bool {
    $parts = explode(".", $jwt);
    $header = json_decode(base64url_decode($parts[0]), true);
    if ($header === false) return false;
    $payload = json_decode(base64url_decode($parts[1]), true);
    if ($payload === false) return false;
    $this->header = $header;
    $this->payload = $payload;
    return true;
  }
  /**
   * [expired description]
   * @param  string $jwt [description]
   * @return bool        [description]
   */
  public function expired(string $jwt=null):bool {
    $exp = $jwt == null ? ($this->payload["exp"] ?? time()) : $this->get_expired($jwt);
    return time() >= $exp;
  }
  /**
   * [hashmac description]
   * @param  string $alg       [description]
   * @param  string $data      [description]
   * @param  string $signature [description]
   * @param  string $secret    [description]
   * @return bool              [description]
   */
  private function hashmac(string $alg, string $data, string $signature, string $secret):bool {
    return $signature === hash_hmac(self::ALGOS[$alg], $data, $secret);
  }
  /**
   * [get_expired description]
   * @param  string $jwt [description]
   * @return int         [description]
   */
  private function get_expired(string $jwt):int {
    $parts = explode(".", $jwt);
    return json_decode(base64url_decode($parts[1]) ,true)["exp"] ?? time();
  }
  /**
   * [sign_token Sign JWT]
   * @param  string $token base64 url encoded header and payload token pair.
   * @param  string $key   The scecret used to sign the token.
   * @param  string $alg   The algorithm used to sign the token.
   * @return string        Complete JWT.
   */
  private function sign_token(string $token, string $key, string $alg):string {
    if ($alg == self::NONE) return $token . ".";
    $token = rtrim($token, ".");
    $signature = hash_hmac(self::ALGOS[$alg], $token, $key);
    return $token . "." . $signature;
  }
}
