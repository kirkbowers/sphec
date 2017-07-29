<?php
namespace Sphec;

/**
 * Performs the tests to satisfy expectations.
 *
 * @author Kirk Bowers
 */
class Tester {
  /**
   * @param $value The value to be tested against an expected result.
   * @param $reporter A Sphec\Reporter object (or any duck-typed equivalent) to which
   *  the result of any tests are to be reported (whether the test passed or failed).
   */
  public function __construct($value, $reporter) {
    $this->value = $value;
    $this->reporter = $reporter;
  }
  
  /**
   * Tests against logical equivalence (same value and type) using ===
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_be_equivalent($expected) {
    $this->report($this->value === $expected);
  }
  
  /**
   * Convenience alias for `to_be_equivalent`.
   *
   * @param $expected The expected value to be tested against.
   * @see to_be_equivalent
   */
  public function to_be($expected) {
    $this->to_be_equivalent($expected);
  }

  /**
   * Tests against type coerced equals (same value after type casting) using ==
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_equal($expected) {
    $this->report($this->value == $expected);
  }

  /**
   * Reports whether the test passed or failed.
   *
   * @param $result A truthy value will cause a "pass" to be reported, otherwise "fail"
   *   will be reported.
   */
  protected function report($result) {
    if ($result) {
      $this->reporter->pass();
    } else {
      $this->reporter->fail();
    }
  }
}
