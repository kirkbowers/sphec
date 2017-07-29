<?php
namespace Sphec;

/**
 * A holder of a lazily runnable block of code that contains `expect` tests.
 *
 * @author Kirk Bowers
 */
class Example extends Runnable {
  function __construct($label, $block, $indent = '', $parent = NULL) {
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
    // TODO:
    // Make this respect a verbose setting.
    echo $this->indent . $this->label. "\n";
    echo $this->indent;
    $this->block->__invoke($this);
    echo "\n";
  }
}
