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
  private $_lazy_variables = array();

  function __construct($label, $block, $indent = '', $parent = NULL, $line_number = NULL) {
    parent::__construct($label, $block, $indent, $parent, $line_number);
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
  public function describe(...$args) {
    if (count($args) > 2) {
      $line_number = $args[0];
      $label = $args[1];
      $block = $args[2];
    } else {
      $line_number = null;
      $label = $args[0];
      $block = $args[1];
    }
    return $this->_tests[] = new Context($label, $block, $this->_indent . '  ', $this, $line_number);
  }

  /**
   * Creates a new subcontext.
   *
   * This is an alias to `describe`.  It has a different name just to make things in
   * your spec files a little more human readable.  It's intended to be used in a
   * different context than `describe`.  Usually it is used to group together tests
   * that share a set of preconditions.  They will share a `before` set up.
   *
   * @param $label A string label of what is being described.
   * @param $block An anonymous function that performs all the specifying and testing
   *    for this subcontext.  It should take one parameter, which will be this Context
   *    instance that can perform all
   *    the mojo that a Context does (describe, it, etc.).
   */
  public function context(...$args) {
    return $this->describe(...$args);
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

  public function let($variable, $block) {
    $this->_lazy_variables[$variable] = $block;
  }

  public function get_lazy_variable($scope, $variable) {
    if (isset($this->_lazy_variables[$variable]) && is_callable($this->_lazy_variables[$variable])) {
      return $this->_lazy_variables[$variable]($scope);
    } if ($this->_parent) {
      return $this->_parent->get_lazy_variable($scope, $variable);
    } else {
      return null;
    }
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
  public function it(...$args) {
    if (count($args) > 2) {
      $line_number = $args[0];
      $label = $args[1];
      $block = $args[2];
    } else {
      $line_number = null;
      $label = $args[0];
      $block = $args[1];
    }
    return $this->_tests[] = new Example($label, $block, $this->_indent . '  ', $this, $line_number);
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
    Sphec::get_reporter()->report_context_start($this->_label);

    foreach ($this->_tests as $test) {
      $test->run();
    }

    Sphec::get_reporter()->report_context_end($this->_label);
  }
}
