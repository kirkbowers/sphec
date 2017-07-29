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
  public $quiet = true;
  
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
    if (! $this->quiet) {
      echo ".";
    }
    $this->passed += 1;
  }
  
  /**
   * Increments the number of failed tests.
   */
  public function fail() {
    if (! $this->quiet) {
      echo "F";
    }
    $this->failed += 1;
  }
}