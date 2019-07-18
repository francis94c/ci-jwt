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
    /**
     * [$params Config Items.]
     *
     * @var array
     *
     * Description:-
     *
     * secret:  The secret strng used in signing the JWT.
     * alg:     Signing Algorithm.
     * set_iat: Automatically set issued at time on payloads.
     */
    $params = [
      "secret"     => "GHfsjfblsgo8r84nNOHHdgdgdgdyf758y8hyttjuoljlhkmjhgO8HOHLHLd",
      "alg"        => "HS256",
      "set_iat"    => true
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
    $this->assertEquals(JWT::HS256, $header["alg"]);
    $this->assertEquals(JWT::JWT, $header["typ"]);
  }
  /**
   * [testPayload test payload section.]
   *
   * RFC7519 Section 4.1
   *
   * @depends testHeader
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
  /**
   * [testBase64Methods descriptio]
   *
   * @depends testHelperMethodsExist
   *
   * @testdox Test Base64 Function.
   */
  public function testBase64Functions():void {
    $data = "The Quick Brown Fox Jumped over the Lazy Dog.";
    $b64 = base64url_encode($data);
    $this->assertEquals($data, base64url_decode($b64));
  }
  /**
   * [testSigning description]
   *
   * @depends testPayload
   *
   * @testdox Test Signing.
   */
  public function testSigning():void {
    $jwt = self::$ci->jwt->sign();
    $parts = explode(".", $jwt);
    $header = json_decode(base64url_decode($parts[0]), true);
    $this->assertEquals(JWT::HS256, $header["alg"]);
    $this->assertEquals(JWT::JWT, $header["typ"]);
    $payload = json_decode(base64url_decode($parts[1]), true);
    $this->assertEquals("www.example.com", $payload["iss"]);
    $this->assertEquals("francis", $payload["sub"]);
    $this->assertEquals("my_server", $payload["aud"]);
    $this->assertEquals(23456789967, $payload["exp"]);
    $this->assertEquals(12345677778, $payload["iat"]);
    // Verify Signature.
  }
  /**
   * [testExpired Test expiry date of jwts.]
   */
  public function testExpired():void {
    $this->assertTrue(true);
  }
}
?>
