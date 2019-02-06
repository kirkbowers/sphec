<?php

require_once __DIR__ . '/../vendor/autoload.php';

class SucceedMatchingMatcher extends Sphec\Matchers\Matcher {
  const ALIASES = ['succeed_matching'];

  public function matches(...$args) {
    return succeeds_matching($this->actual);
  }

  public function failure_message() {
    'to be a successful match';
  }

  public function failure_message_when_negated() {
    'not to be a successful match';
  }
}

Sphec\Sphec::register_matcher('SucceedMatchingMatcher');
