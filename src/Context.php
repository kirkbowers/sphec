<?php
namespace Sphec;

// TODO:
// It would be nice if this autoloaded.  Autoloading in PHP is not something I've used 
// before since WordPress is all manually loaded.
// require 'Expector.php';
require 'Runnable.php';
require 'Example.php';

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Context extends Runnable {
  private $tests = array();
  public  $expector;
  
  function __construct($label, $block, $indent = '', $parent = NULL) {
    parent::__construct($label, $block, $indent, $parent);
    $this->expector = new Expector();
    $block($this);
  }

  /**
   * Creates a new subcontext.
   *
   * Usually this is used to group together tests on a sub-feature such as a method of 
   * a class.
   *
   * @param $label A string label of what is being described.
   * @param $block An anonymous function that performs all the specifying and testing
   *    for this subcontext.  It should take one parameter, which will be this Context 
   *    instance that can perform all
   *    the mojo that a Context does (describe, it, etc.).
   */
  public function describe($label, $block) {
    // TODO:
    // It needs to be able to handle `before` and `after` actions.  They must fire
    // recursively, meaning outer contexts fire first on befores and last on afters.
    
    // TODO:
    // It needs to be able to provide local variables that can be set in before blocks.
    // Those variables need to propogate recursively.
    
    $this->tests[] = new Context($label, $block, $this->indent . '  ', $this);
  }
  
  /**
   * Creates a new example.
   *
   * This is where individual tests are performed.  The label should describe
   * what is to be expected in this test in a sentence that follows "it".
   * (Eg. "It" "should evaluate to true in this situation.")
   *
   * @param $label A string label of what is being expected.
   * @param $block An anonymous function that performs all the testing
   *    for this example.  It should take one parameter, which will be the Example 
   *    instance that can perform `expect` methods.
   */
  public function it($label, $block) {
    $this->tests[] = new Example($label, $block, $this->indent . '  ', $this);
  }
  
  /**
   * Runs all the tests in this scope.
   */
  public function run() {
    $this->expector->reset();

    // TODO:
    // Make this respect a verbose setting.
    echo $this->indent . $this->label. "\n"; 
    
    foreach ($this->tests as $test) {
      $test->run();
    }
    
    // Fold in the counts of successes and failures from subcontexts.
    if ($this->parent) {
      $this->parent->expector->combine($this->expector);
    }
  }
}
