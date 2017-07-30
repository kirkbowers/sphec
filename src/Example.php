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
    $this->expector = $parent->expector;
  }
  
  /**
   * Creates an expectation that can be tested.
   *
   * @param $value The calculated value to be compared to an expected value.
   */
  public function expect($value) {
    return $this->expector->test($value);
  }

  public function run() {
    $this->parent->run_befores();
  
    if ($this->expector->output && $this->expector->output->isVerbose()) {
      $this->expector->output->writeln($this->indent . $this->label);
      $this->expector->output->write($this->indent);
    }
    $this->block->__invoke($this);
    if ($this->expector->output && $this->expector->output->isVerbose()) {
      $this->expector->output->writeln('');
    }
    
    $this->parent->run_afters();
  }
}
