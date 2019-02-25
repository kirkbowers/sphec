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
  public function __construct($value) {
    $this->value = $value;
  }

  public function __call($method, $args) {
    if (preg_match('/^to_not_(.*)$/', $method, $matches)) {
      $matcher = $this->create_matcher($matches[1], $args);
      $result = ! succeeds_matching($matcher->matches(...$args));
      if (! $result) {
        throw new FailedMatchException($matcher->failure_message_when_negated());
      }
      return $result;
    } else if (preg_match('/^to_(.*)$/', $method, $matches)) {
      $matcher = $this->create_matcher($matches[1], $args);
      $result = succeeds_matching($matcher->matches(...$args));
      if (! $result) {
        throw new FailedMatchException($matcher->failure_message());
      }
      return $result;
    } else if (preg_match('/^not_to_(.*)$/', $method, $matches)) {
      $matcher = $this->create_matcher($matches[1], $args);
      $result = ! succeeds_matching($matcher->matches(...$args));
      if (! $result) {
        throw new FailedMatchException($matcher->failure_message_when_negated());
      }
      return $result;
    }

    if (! $matcher_class) {
      parent::__call($method, $args);
    }
  }

  private function create_matcher($alias, $args) {
    $matcher_class = self::matcher_for_alias($alias);
    return new $matcher_class($this->value);
  }
}

Tester::register_matcher('Sphec\Matchers\IdenticalMatcher');
Tester::register_matcher('Sphec\Matchers\DoubleEqualMatcher');
Tester::register_matcher('Sphec\Matchers\TruthyMatcher');
Tester::register_matcher('Sphec\Matchers\FalseyMatcher');
Tester::register_matcher('Sphec\Matchers\ThrowsMatcher');
Tester::register_matcher('Sphec\Matchers\CompareMatcher');
Tester::register_matcher('Sphec\Matchers\HaveReceivedMatcher');
