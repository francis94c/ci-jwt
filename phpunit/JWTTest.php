<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase {
  /**
   * Code Igniter Instance.
   * @var object
   */
  private static $ci;
  /**
   * Package name for simplicity
   * @var string
   */
  private const PACKAGE = "francis94c/ci-jwt";

  /**
   * Prerquisites for the Unit Tests.
   */
  public static function setUpBeforeClass(): void {
    self::$ci =& get_instance();
    $params = [
      "secret" => "GHfsjfblsgo8r84nNOHHdgdgdgdyf758y8hyttjuoljlhkmjhgO8HOHLHLd"
    ];
    self::$ci->load->package(self::PACKAGE, $params);
  }
  /**
   * [testHelperMethodsExist Test Helper Method Exist.]
   * @testdox Helper Methods.
   */
  function testHelperMethodsExist() {
    $this->assertTrue(function_exists("base64url_encode"));
    $this->assertTrue(function_exists("base64url_decode"));
  }
  /**
   * [testHeader test header section of JWT]
   *
   * @testdox Header Section.
   */
  function testHeader():void  {
    self::$ci->jwt->header("alg", JWT::HS256);
    self::$ci->jwt->header("typ", JWT::JWT);
    $header = self::$ci->jwt->headerArray();
    $this->assertEquals(JWT::HS254, $header["alg"]);
    $this->assertEquals(JWT::JWT, $header["typ"]);
  }
  /**
   * [testPayload test payload section.]
   *
   * RFC7519 Section 4.1
   *
   * @testdox Payload Test.
   */
  public function testPayload():void  {
    self::$ci->jwt->payload("iss", "www.example.com");
    self::$ci->jwt->payload("sub", "francis");
    self::$ci->jwt->payload("aud", "my_server");
    self::$ci->jwt->payload("exp", 23456789967);
    self::$ci->jwt->payload("iat", 12345677778);
    $payload = self::$ci->jwt->payloadArray();
    $this->assertEquals("www.example.com", $payload["iss"]);
    $this->assertEquals("francis", $payload["sub"]);
    $this->assertEquals("my_server", $payload["aud"]);
    $this->assertEquals(23456789967, $payload["exp"]);
    $this->assertEquals(12345677778, $payload["iat"]);
  }

}
?>
