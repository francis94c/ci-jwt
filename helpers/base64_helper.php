<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('base64url_encode')) {
  /**
   * [base64url_encode base64 url encode data.]
   *
   * @param  string $data Data to encode
   *
   * @return string       Encoded Data.
   */
  function base64url_encode(string $data):string {
    $b64 = base64_encode($data);
    if ($b64 === false) return false;
    $url = strtr($b64, '+/', '-_');
    return rtrim($url, '=');
  }
}

if (!function_exists('base64url_decode')) {
  /**
   * [base64url_decode decode base64 url encoded data.]
   *
   * @param  string  $data   Data to decode.
   *
   * @param  boolean $strict Strinct flag.
   *
   * @return string          Decoded String.
   */
  function base64url_decode(string $data, bool $strict=false):string {
    $b64 = strtr($data, '-_', '+/');
    return base64_decode($b64, $strict);
  }
}
