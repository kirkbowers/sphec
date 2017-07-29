<?php
namespace Sphec;

// TODO:
// It would be nice if this autoloaded.  Autoloading in PHP is not something I've used 
// before since WordPress is all manually loaded.
require 'Expector.php';

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Sphec {

  private $tests = array();
  private $expector;
  
  function __construct() {
    $this->expector = new Expector();
  }

  /** 
   * The entry point for building a specification.
   *
   * @param $label A string label of what is being specified in this spec.  Usually it
   *   is the name of a class.
   * @param $block An anonymous function that performs all the specifying and testing.
   *   It should take one parameter, which will be a Sphec instance that can perform all
   *   the mojo that a Sphec does (describe, it, expect, etc.).
   */
  public static function specify($label, $block) {
    $spec = new Sphec;
    $block($spec);
    $spec->run();
  }

  /**
   * Creates a new subcontext.
   *
   * Usually this is used to group together tests on a sub-feature such as a method of 
   * a class.
   *
   * @param $label A string label of what is being described.
   * @param $block An anonymous function that performs all the specifying and testing
   *    for this subcontext.  It should take one parameter, which will be a Sphec 
   *    instance that can perform all
   *    the mojo that a Sphec does (describe, it, expect, etc.).
   */
  public function describe($label, $block) {
    // TODO:
    // This should be creating a new context recursively.  And, it should allow 
    // for expectations to be created directly inside of it, just as if it were an `it`.
    
    // TODO:
    // It needs to be able to handle `before` and `after` actions.  They must fire
    // recursively, meaning outer contexts fire first on befores and last on afters.
    
    // TODO:
    // It needs to be able to provide local variables that can be set in before blocks.
    // Those variables need to propogate recursively.
    
    $block($this);
  }
  
  /**
   * Creates a new subcontext.
   *
   * Usually this is where individual tests are performed.  The label should describe
   * what is to be expected in this test in a sentence that follows "it".
   * (Eg. "It" "should evaluate to true in this situation.")
   *
   * @param $label A string label of what is being expected.
   * @param $block An anonymous function that performs all the specifying and testing
   *    for this subcontext.  It should take one parameter, which will be a Sphec 
   *    instance that can perform all
   *    the mojo that a Sphec does (describe, it, expect, etc.).
   */
  public function it($label, $block) {
    $this->tests[] = array($label, $block);
  }
  
  /**
   * Creates an expectation that can be tested.
   *
   * @param $value The calculated value to be compared to an expected value.
   */
  public function expect($value) {
    return $this->expector->test($value);
  }
  
  /**
   * Runs all the tests in this scope.
   */
  public function run() {
    $this->expector->reset();
    
    // TODO:
    // Be able to run tests in subcontexts recursively.
    foreach ($this->tests as $test) {
      $test[1]($this);
      echo $test[0] . "\n";
    }
    
    // TODO:
    // Fold in the counts of successes and failures from subcontexts.
    
    echo "Successes: " . $this->expector->passed . ", Failures: " . $this->expector->failed . "\n";
    
    if ($this->expector->failed == 0) {
      echo "Success!\n";
    } else {
      echo "Failure!\n";
    }
  }
}