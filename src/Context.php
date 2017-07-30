<?php
namespace Sphec;

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
class Context extends Runnable {
  private $_tests = array();
  private $_befores = array();
  private $_afters = array();
  
  function __construct($label, $block, $indent = '', $parent = NULL, $expector = NULL) {
    parent::__construct($label, $block, $indent, $parent);
    if ($expector) {
      $this->_expector = $expector;
    } else if ($parent) {
      $this->_expector = $parent->_expector;
    } else {
      $this->_expector = new Expector();
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
    return $this->_tests[] = new Context($label, $block, $this->_indent . '  ', $this);
  }
  
  
  /**
   * Creates a new before action.  It will be run before every example in this context.
   *
   * @param $block An anonymous function that performs all the setup
   *    for this context.  It should take one parameter, which will be the Example 
   *    instance that will consume any local variables set up.
   */
  public function before($block) {
    return $this->_befores[] = $block;
  }
  
  /**
   * Creates a new after action.  It will be run after every example in this context.
   *
   * @param $block An anonymous function that performs all the tear down
   *    for this context.  It should take one parameter, which will be the Example 
   *    instance for which tear down is needed.
   */
  public function after($block) {
    return $this->_afters[] = $block;
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
    return $this->_tests[] = new Example($label, $block, $this->_indent . '  ', $this);
  }
  
  /**
   * Runs all the befores of containing contexts and then this context's befores in the
   * order they were added.
   *
   * @param $scope The object scope of an Example being run for which local variables
   *    need to be set up.
   */
  public function run_befores($scope) {
    if ($this->_parent) {
      $this->_parent->run_befores($scope);
    }
    
    foreach ($this->_befores as $before) {
      $before($scope);
    }
  }
  
  /**
   * Runs all the befores of containing contexts and then this context's befores in the
   * order they were added.
   *
   * @param $scope The object scope of an Example being run for which local variables
   *    need to be torn down.
   */
  public function run_afters($scope) {    
    foreach ($this->_afters as $after) {
      $after($scope);
    }

    if ($this->_parent) {
      $this->_parent->run_afters($scope);
    }
  }
  
  /**
   * Runs all the tests in this scope.
   */
  public function run() {
    $this->_expector->reset();

    if ($this->_expector->output && $this->_expector->output->isVerbose()) {
      $this->_expector->output->writeln($this->_indent . $this->_label); 
    }
    
    foreach ($this->_tests as $test) {
      $test->run();
    }
  }
}
