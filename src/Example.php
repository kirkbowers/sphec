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
    $this->_expector = $parent->_expector;
  }
  
  /**
   * Creates an expectation that can be tested.
   *
   * @param $value The calculated value to be compared to an expected value.
   */
  public function expect($value) {
    return $this->_expector->test($value);
  }

  public function run() {
    $this->_parent->run_befores($this);
  
    if ($this->_expector->output && $this->_expector->output->isVerbose()) {
      $this->_expector->output->writeln($this->_indent . $this->_label);
      $this->_expector->output->write($this->_indent);
    }
    $this->_block->__invoke($this);
    if ($this->_expector->output && $this->_expector->output->isVerbose()) {
      $this->_expector->output->writeln('');
    }
    
    $this->_parent->run_afters($this);
  }
}
