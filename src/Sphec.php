<?php
namespace Sphec;

require_once 'Mocks/functions.php';
require_once 'Matchers/functions.php';

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Sphec {
  private static $sphecs = array();

  private static $reporter;

  public static function get_reporter() {
    if (! self::$reporter) {
      self::$reporter = new Reporters\SilentReporter;
    }

    return self::$reporter;
  }

  public static function set_reporter($reporter) {
    self::$reporter = $reporter;
  }

  /**
   * The entry point for building a specification.
   *
   * @param $label A string label of what is being specified in this spec.  Usually it
   *   is the name of a class.
   * @param $block An anonymous function that performs all the specifying and testing.
   *   It should take one parameter, which will be a Context instance that can perform all
   *   the mojo that a Context does (describe, it, etc.).
   */
  public static function specify($label, $block) {
    $spec = new Context($label, $block, '', NULL);

    self::$sphecs[] = $spec;
  }

  public static function register_matcher($matcher) {
    Tester::register_matcher($matcher);
  }

  public static function run() {
    foreach (self::$sphecs as $spec) {
      $spec->run();
    }

    self::$reporter->report_final_summary();
  }
}