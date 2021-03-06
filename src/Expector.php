<?php
namespace Sphec;

/**
 * Builds an expectation for a test.  Also reports the results of all tests.
 *
 * @author Kirk Bowers
 */
class Expector extends Reporter {
  function __construct($output = NULL) {
    parent::__construct($output);
  }

  /**
   * Performs a test on an expectation with a given value.
   *
   * @param $value The value to be tested.
   * @return Sphec\Tester A new Tester object that will use this Expector as its Reporter.
   */
  public function test($value, $example) {
    return new Tester($value, $this, $example);
  }
}