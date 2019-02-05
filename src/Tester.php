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
Tester::register_matcher('Sphec\Matchers\DoubleEqualMatcher');
Tester::register_matcher('Sphec\Matchers\TruthyMatcher');
Tester::register_matcher('Sphec\Matchers\FalseyMatcher');
Tester::register_matcher('Sphec\Matchers\ThrowsMatcher');
