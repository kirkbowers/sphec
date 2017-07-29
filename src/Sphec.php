<?php
namespace Sphec;

// TODO:
// It would be nice if this autoloaded.  Autoloading in PHP is not something I've used 
// before since WordPress is all manually loaded.
require 'Expector.php';
require 'Context.php';

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Sphec {

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
    $spec->run();

    
    echo "Successes: " . $spec->expector->passed . ", Failures: " . $spec->expector->failed . "\n";
    
    if ($spec->expector->failed == 0) {
      echo "Success!\n";
    } else {
      echo "Failure!\n";
    }
  }
}