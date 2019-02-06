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

  public static $expector = NULL;

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
    $spec = new Context($label, $block, '', NULL, self::$expector);

    self::$sphecs[] = $spec;
  }

  public static function register_matcher($matcher) {
    Tester::register_matcher($matcher);
  }

  public static function run() {
    foreach (self::$sphecs as $spec) {
      $spec->run();
    }

    if (self::$expector->output && !self::$expector->output->isQuiet()) {
      self::$expector->output->writeln("");

      self::$expector->report_failures();

      self::$expector->output->writeln("Successes: " . self::$expector->passed . ", Failures: " . self::$expector->failed);

      if (self::$expector->failed == 0) {
        self::$expector->output->writeln("<fg=green>Success!</fg=green>");
      } else {
        self::$expector->output->writeln("<fg=red>Failure!</fg=red>");
      }
    }
  }
}