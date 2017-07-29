<?php
namespace Sphec;

/**
 * A holder of a lazily runnable block of code that contains `expect` tests.
 *
 * @author Kirk Bowers
 */
class Example extends Runnable {
  function __construct($label, $block, $indent, $parent) {
    parent::__construct($label, $block, $indent, $parent);
  }
  
  /**
   * Creates an expectation that can be tested.
   *
   * @param $value The calculated value to be compared to an expected value.
   */
  public function expect($value) {
    return $this->parent->expector->test($value);
  }

  public function run() {
    $this->parent->run_befores();
  
    // TODO:
    // Make this respect a verbose setting.
    if (! $this->quiet) {
      echo $this->indent . $this->label. "\n";
      echo $this->indent;
    }
    $this->block->__invoke($this);
    if (! $this->quiet) {
      echo "\n";
    }
  }
}
