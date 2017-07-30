<?php
namespace Sphec;

/**
 * Collects the reported test passes and fails.
 *
 * @author Kirk Bowers
 */
class Reporter {
  public $passed = 0;
  public $failed = 0;
  
  public $output = NULL;
  
  /**
   * @param $output An optional Symfony OutputInterface object for reporting to the 
   *    console.
   */
  function __construct($output = NULL) {
    $this->output = $output;  
  }
  
  /**
   * Resets all reported counts to zero.
   */
  public function reset() {
    $passed = 0;
    $failed = 0;
  }
  
  /**
   * Takes the reported values in the incoming Reporter and adds them to this one's 
   *   values.
   *
   * @param $reporter Another Reporter to combine into this one.
   */
  public function combine($reporter) {
    $this->passed += $reporter->passed;
    $this->failed += $reporter->failed;
  }
  
  /**
   * Increments the number of passed tests.
   */
  public function pass() {
    if ($this->output && !$this->output->isQuiet()) {
      $this->output->write(".");
    }
    $this->passed += 1;
  }
  
  /**
   * Increments the number of failed tests.
   */
  public function fail() {
    if ($this->output && !$this->output->isQuiet()) {
      $this->output->write("<fg=red>F</fg=red>");
    }
    $this->failed += 1;
  }
}