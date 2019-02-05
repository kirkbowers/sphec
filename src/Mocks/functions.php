<?php

namespace {
  function test_double($id = null, $args = null) {
    return new \Sphec\Mocks\Double($id, $args);
  }
}