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
  public function __construct($value, $reporter, $example = NULL) {
    $this->value = $value;
    $this->reporter = $reporter;
    $this->example = $example;
  }
  
  /**
   * Tests against logical equivalence (same value and type) using ===
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_be_equivalent($expected) {
    $this->report($this->value === $expected, 'to be equivalent to', $expected);
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
    $this->report($this->value == $expected, 'to equal', $expected);
  }

  /**
   * Tests that the computed value is strictly the boolean value true.
   *
   */
  public function to_be_true() {
    $this->report($this->value === true, 'to be true');
  }

  /**
   * Tests that the computed value is true after type coercion.
   *
   */
  public function to_be_truthy() {
    $this->report($this->value, 'to be truthy');
  }

  /**
   * Tests that the computed value is strictly the boolean value false.
   *
   */
  public function to_be_false() {
    $this->report($this->value === false, 'to be false');
  }

  /**
   * Tests that the computed value is false after type coercion.
   *
   */
  public function to_be_falsy() {
    $this->report(! $this->value, 'to be falsy');
  }

  /**
   * Reports whether the test passed or failed.
   *
   * @param $result A truthy value will cause a "pass" to be reported, otherwise "fail"
   *   will be reported.
   */
  protected function report($result, $test, $expected = NULL) {
    if ($result) {
      $this->reporter->pass();
    } else {
      $name = '';
      if ($this->example) {
        $name = $this->example->get_full_name();
      }
      $this->reporter->fail($name, $this->value, $test, $expected);
    }
  }
}
