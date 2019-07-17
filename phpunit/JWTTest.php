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

  function testHeader():void  {
    
  }
}
?>
