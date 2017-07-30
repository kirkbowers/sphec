<?php
namespace Sphec;

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Sphec {
  private static $sphecs = array();


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
    $spec = new Context($label, $block);  
    
    self::$sphecs[] = $spec;  
  }
  
  public static function run() {
    $reporter = new Reporter(); 
  
    foreach (self::$sphecs as $spec) {
      $spec->run();
      $reporter->combine($spec->expector);
    }

    echo "Successes: " . $reporter->passed . ", Failures: " . $reporter->failed . "\n";

    if ($reporter->failed == 0) {
      echo "Success!\n";
    } else {
      echo "Failure!\n";
    }
  }
}