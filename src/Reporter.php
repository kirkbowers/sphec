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
  
  /**
   * If true, the reporter will echo to the console "." for passed tests and "F" for
   * failed tests.  Defaults to true.
   */
  public $verbose = true;
  
  /**
   * Resets all reported counts to zero.
   */
  public function reset() {
    $passed = 0;
    $failed = 0;
  }
  
  /**
   * Increments the number of passed tests.
   */
  public function pass() {
    if ($this->verbose) {
      echo ".";
    }
    $this->passed += 1;
  }
  
  /**
   * Increments the number of failed tests.
   */
  public function fail() {
    if ($this->verbose) {
      echo "F";
    }
    $this->failed += 1;
  }
}