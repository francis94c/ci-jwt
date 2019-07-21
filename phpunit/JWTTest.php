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
   *
   * @covers JWT::__construct
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
    $config = [
      "secret"     => "GHfsjfblsgo8r84nNOHHdgdgdgdyf758y8hyttjuoljlhkmjhgO8HOHLHLd",
      "alg"        => "HS256",
      "set_iat"    => true
    ];
    self::$ci->load->package(self::PACKAGE);
    self::$ci->jwt->init($config);
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
    $this->assertTrue(self::$ci->jwt->verify($jwt));
    // Let's Tamper with the JWT's integrity.
    $payload["sub"] = "john";
    $jwt = base64url_encode(json_encode($header)) . "." .
    base64url_encode(json_encode($payload)) . "." . $parts[2];
    $this->assertFalse(self::$ci->jwt->verify($jwt));
    // Tamper by signing with a different secret.
    $signature = hash_hmac(
      "sha256",
      base64url_encode(json_encode($header)) . "." .
      base64url_encode(json_encode($payload)),
      "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
    $jwt = base64url_encode(json_encode($header)) . "." .
    base64url_encode(json_encode($payload)) . "." . $signature;
    // Verify with Original Secret.
    $this->assertFalse(self::$ci->jwt->verify($jwt));
    // Verify with fake Secret.
    $this->assertTrue(self::$ci->jwt->verify($jwt, "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"));
    // Restore integrity.
    $payload["sub"] = "francis";
    $jwt = base64url_encode(json_encode($header)) . "." .
    base64url_encode(json_encode($payload)) . "." . $parts[2];
    $this->assertTrue(self::$ci->jwt->verify($jwt));
  }
  /**
   * [testEmpty description]
   *
   * @depends testSigning
   *
   * @tesdox Test Empty.
   */
  public function testEmpty():void {
    self::$ci->jwt->create();
    $this->assertEmpty(self::$ci->jwt->headerArray());
    $this->assertEmpty(self::$ci->jwt->payloadArray());
  }
  /**
   * [testEmptyPayload description]
   *
   * @depends testEmpty
   *
   * @tesdox Test Signed Empty Payload.
   */
  public function testUnsignedToken():void {
    $this->assertNull(self::$ci->jwt->token());
    self::$ci->jwt->payload("iss", "server");
    $jwt = self::$ci->jwt->token();
    $this->assertFalse(self::$ci->jwt->verify($jwt));
    self::$ci->jwt->init(["allow_unsigned" => true]);
    $this->assertTrue(self::$ci->jwt->verify($jwt));
    // Test auto-generation of time stamp with unsigned token.
    $this->assertIsNumeric(json_decode(base64url_decode(explode(".", $jwt)[1]), true)["iat"]);
    self::$ci->jwt->init(["allow_unsigned" => false]);
    // Test auto-generation of time stamp with signed token.
    self::$ci->jwt->create();
    self::$ci->jwt->payload("iss", "server");
    $jwt = self::$ci->jwt->sign();
    $this->assertIsNumeric(json_decode(base64url_decode(explode(".", $jwt)[1]), true)["iat"]);
  }
  /**
   * [testDecode description]
   *
   * @depends testUnsignedToken
   */
  public function testDecode():void {
    self::$ci->jwt->create();
    self::$ci->jwt->header("alg", JWT::HS256);
    self::$ci->jwt->header("typ", JWT::JWT);
    self::$ci->jwt->payload("iss", "www.example.com");
    self::$ci->jwt->payload("sub", "francis");
    self::$ci->jwt->payload("aud", "my_server");
    self::$ci->jwt->payload("exp", 23456789967);
    self::$ci->jwt->payload("iat", 12345677778);
    // Get Token
    $jwt = self::$ci->jwt->sign();
    self::$ci->jwt->create();
    self::$ci->jwt->decode($jwt);
    $header = self::$ci->jwt->headerArray();
    $this->assertEquals(JWT::HS256, $header["alg"]);
    $this->assertEquals(JWT::JWT, $header["typ"]);
    $payload = self::$ci->jwt->payloadArray();
    $this->assertEquals("www.example.com", $payload["iss"]);
    $this->assertEquals("francis", $payload["sub"]);
    $this->assertEquals("my_server", $payload["aud"]);
    $this->assertEquals(23456789967, $payload["exp"]);
    $this->assertEquals(12345677778, $payload["iat"]);
  }
  /**
   * [testExpired Test expiry date of JWTs.]
   *
   * @depends testDecode
   */
  public function testExpired():void {
    // Internal Check.
    self::$ci->jwt->payload("exp", time());
    $this->assertTrue(self::$ci->jwt->expired());
    self::$ci->jwt->payload("exp", time() + (7 * 24 * 60 * 60));
    $this->assertFalse(self::$ci->jwt->expired());

    // Supplied Token Check.
    self::$ci->jwt->payload("exp", time());
    $jwt = self::$ci->jwt->sign();
    $this->assertTrue(self::$ci->jwt->expired($jwt));
    self::$ci->jwt->payload("exp", time() + (7 * 24 * 60 * 60));
    $jwt = self::$ci->jwt->sign();
    $this->assertFalse(self::$ci->jwt->expired($jwt));
    // Empty Expire.
    self::$ci->jwt->create();
    $this->assertTrue(self::$ci->jwt->expired());
  }
  /**
   * [testSetExpired description]
   *
   * @depends testExpired
   *
   * @testdox Test Set Expiry Date on Token.
   */
  public function testSetExpired():void {
    self::$ci->jwt->expire("+1 Day");
    $this->assertFalse(self::$ci->jwt->expired());
  }
  /**
   * [setDefaultExpiryDate description]
   *
   */
  public function testSetDefaultExpiryDateForSignedToken():void {
    self::$ci->jwt->create();
    self::$ci->jwt->payload("iss", "www.example.com");
    self::$ci->jwt->init(["auto_expire" => "+1 Seconds"]);
    $jwt = self::$ci->jwt->sign();
    $this->assertFalse(self::$ci->jwt->expired($jwt));
    sleep(1); // Allow Token to expire.
    $this->assertTrue(self::$ci->jwt->expired($jwt));
  }
  /**
   * [testSetDefaultExpiryDateForUnSignedToken description]
   */
  public function testSetDefaultExpiryDateForUnSignedToken(): void {
    self::$ci->jwt->create();
    self::$ci->jwt->payload("iss", "www.example.com");
    self::$ci->jwt->init(["auto_expire" => "+1 Seconds"]);
    $jwt = self::$ci->jwt->token();
    $this->assertFalse(self::$ci->jwt->expired($jwt));
    sleep(1); // Allow Token to expire.
    $this->assertTrue(self::$ci->jwt->expired($jwt));
  }
}
?>
