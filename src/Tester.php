<?php
namespace Sphec;

/**
 * Performs the tests to satisfy expectations.
 *
 * @author Kirk Bowers
 */
class Tester {
  private static $matchers = [];

  public static function register_matcher($matcher_class) {
    foreach ($matcher_class::ALIASES as $alias) {
      self::$matchers[$alias] = $matcher_class;
    }
  }

  private static function matcher_for_alias($alias) {
    if (isset(self::$matchers[$alias])) {
      return self::$matchers[$alias];
    } else {
      throw new \Sphec\Matchers\UnrecognizedMatcherException("Unrecognized matcher $alias");
    }
  }

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

  public function __call($method, $args) {
    if (preg_match('/^to_not_(.*)$/', $method, $matches)) {
      $alias = $matches[1];
      $matcher_class = self::matcher_for_alias($alias);
      $matcher = new $matcher_class($this->value);
      $result = !$matcher->matches(...$args);
      $this->report(!$matcher->matches(...$args), $matcher->failure_message(), ...$args);
      return $result;
    } else if (preg_match('/^to_(.*)$/', $method, $matches)) {
      $alias = $matches[1];
      $matcher_class = self::matcher_for_alias($alias);
      $matcher = new $matcher_class($this->value);
      $result = $matcher->matches(...$args);
      $this->report($result, $matcher->failure_message());
      return $result;
    } else if (preg_match('/^not_to_(.*)$/', $method, $matches)) {
      $alias = $matches[1];
      $matcher_class = self::matcher_for_alias($alias);
      $matcher = new $matcher_class($this->value);
      $result = !$matcher->matches(...$args);
      $this->report(!$matcher->matches(...$args), $matcher->failure_message(), ...$args);
      return $result;
    }

    if (! $matcher_class) {
      parent::__call($method, $args);
    }
  }

  /**
   * Tests against logical equivalence (same value and type) using ===
   *
   * @param $expected The expected value to be tested against.
   */
  // public function to_be_equivalent($expected) {
  //   $this->report($this->value === $expected, 'to be equivalent to', $expected);
  // }

  /**
   * Convenience alias for `to_be_equivalent`.
   *
   * @param $expected The expected value to be tested against.
   * @see to_be_equivalent
   */
  // public function to_be($expected) {
  //   $this->to_be_equivalent($expected);
  // }

  /**
   * Tests against type coerced equals (same value after type casting) using ==
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_equal($expected) {
    $this->report($this->value == $expected, 'to equal', $expected);
  }

  /**
   * Tests against logical inequivalence (different value or different type) using !==
   *
   * @param $expected The expected value to be tested against.
   */
  // public function to_not_be($expected) {
  //   $this->report($this->value !== $expected, 'to not be equivalent to', $expected);
  // }

  /**
   * Tests against type coerced not equals (not the same value after type casting) using !=
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_not_equal($expected) {
    $this->report($this->value != $expected, 'to not equal', $expected);
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

  public function to_be_falsey() {
    $this->report(! $this->value, 'to be falsey');
  }

  /**
   * Tests against greater than using >
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_be_greater_than($expected) {
    $this->report($this->value > $expected, 'to be greater than', $expected);
  }

  /**
   * Tests against greater than or equal using >=
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_be_greater_than_or_equal($expected) {
    $this->report($this->value >= $expected, 'to be greater than or equal', $expected);
  }

  /**
   * Tests against less than using <
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_be_less_than($expected) {
    $this->report($this->value < $expected, 'to be less than', $expected);
  }

  /**
   * Tests against less than or equal using <=
   *
   * @param $expected The expected value to be tested against.
   */
  public function to_be_less_than_or_equal($expected) {
    $this->report($this->value <= $expected, 'to be less than or equal', $expected);
  }

  /**
   * Tests whether the supplied function doesn't throw any exception.
   *
   */
  public function not_to_throw() {
    $result = true;
    $name = '';

    if (is_callable($this->value)) {
      try {
        $this->value->__invoke();
      } catch (\Exception $e) {
        $result = false;
        $name = get_class($e);
      }
    }

    $this->report($result, 'not to throw but threw', $name);
  }

  /**
   * Tests whether the supplied function throws the expected exception.
   *
   * @param $expected The name of the expected exception as a string.
   */
  public function to_throw($expected) {
    $result = false;

    if (is_callable($this->value)) {
      try {
        $this->value->__invoke();
      } catch (\Exception $e) {
        if (is_a($e, $expected)) {
          $result = true;
        }
      }
    }

    $this->report($result, 'to throw', $expected);
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

Tester::register_matcher('Sphec\Matchers\IdenticalMatcher');

