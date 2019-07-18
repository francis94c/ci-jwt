<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JWT {

  const JWT = "jwt";

  // Signing Algorithms.
  const HS256 = 'HS256';

  // Internal Variables.
  /**
   * [private description]
   * @var [type]
   */
  private $secret;
  /**
   * [private description]
   * @var [type]
   */
  private $header = [];
  /**
   * [private description]
   * @var [type]
   */
  private $payload = [];

  function __construct($params=null) {
    if ($params != null) {
      $this->secret = $params["secret"] ?? "";
    }
  }
  /**
   * [header description]
   * @param  [type]     $key   [description]
   * @param  string|int $value [description]
   * @return [type]            [description]
   */
  public function header(string $key, $value):void {
    $this->header[$key] = $value;
  }
  /**
   * [headerArray description]
   * @return array [description]
   */
  public function headerArray(): array {
    return $this->header;
  }
  /**
   * [payload description]
   * @param string $key   [description]
   * @param [type] $value [description]
   */
  public function payload(string $key, $value): void {
    $this->payload[$key] = $value;
  }
  /**
   * [payloadarray description]
   * @return array [description]
   */
  public function payloadarray(): array {
    return $this->payload;
  }
}
?>
