<?php
namespace Sphec;

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Context extends Runnable {
  private $tests = array();
  private $befores = array();
  private $afters = array();
  public  $expector;
  
  function __construct($label, $block, $indent = '', $parent = NULL, $expector = NULL) {
    parent::__construct($label, $block, $indent, $parent);
    if ($expector) {
      $this->expector = $expector;
    } else if ($parent) {
      $this->expector = $parent->expector;
    } else {
      $this->expector = new Expector();
    }
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
    // It needs to be able to handle `after` actions.  They must fire
    // recursively, meaning outer contexts fire first on befores and last on afters.
    
    // TODO:
    // It needs to be able to provide local variables that can be set in before blocks.
    // Those variables need to propogate recursively.
    return $this->tests[] = new Context($label, $block, $this->indent . '  ', $this);
  }
  
  
  /**
   * Creates a new before action.  It will be run before every example in this context.
   *
   * @param $block An anonymous function that performs all the setup
   *    for this context.  It should take one parameter, which will be this Context 
   *    instance.
   */
  public function before($block) {
    return $this->befores[] = $block;
  }
  
  /**
   * Creates a new after action.  It will be run after every example in this context.
   *
   * @param $block An anonymous function that performs all the tear down
   *    for this context.  It should take one parameter, which will be this Context 
   *    instance.
   */
  public function after($block) {
    return $this->afters[] = $block;
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
    return $this->tests[] = new Example($label, $block, $this->indent . '  ', $this);
  }
  
  /**
   * Runs all the befores of containing contexts and then this context's befores in the
   * order they were added.
   */
  public function run_befores() {
    if ($this->parent) {
      $this->parent->run_befores();
    }
    
    foreach ($this->befores as $before) {
      $before($this);
    }
  }
  
  /**
   * Runs all the befores of containing contexts and then this context's befores in the
   * order they were added.
   */
  public function run_afters() {    
    foreach ($this->afters as $after) {
      $after($this);
    }

    if ($this->parent) {
      $this->parent->run_afters();
    }
  }
  
  /**
   * Runs all the tests in this scope.
   */
  public function run() {
    $this->expector->reset();

    if ($this->expector->output && $this->expector->output->isVerbose()) {
      $this->expector->output->writeln($this->indent . $this->label); 
    }
    
    foreach ($this->tests as $test) {
      $test->run();
    }
  }
}
