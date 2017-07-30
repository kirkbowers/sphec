<?php

Sphec\Sphec::specify('Tester', function($spec) {
  $spec->before(function($spec) {
    $spec->reporter = new Sphec\Reporter;
  });

  $spec->describe('to_be_equivalent', function($spec) {
    $spec->before(function($spec) {
      $spec->tester = new Sphec\Tester(3, $spec->reporter);
    });
    
    $spec->it('passes to_be_equivalent with same value and type', function($spec) {
      $spec->tester->to_be_equivalent(3);
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails to_be_equivalent with different value and same type', function($spec) {
      $spec->tester->to_be_equivalent(4);
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('fails to_be_equivalent with same value and different type', function($spec) {
      $spec->tester->to_be_equivalent("3");
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });
  });

  $spec->describe('to_be', function($spec) {
    $spec->before(function($spec) {
      $spec->tester = new Sphec\Tester(3, $spec->reporter);
    });
    
    $spec->it('passes to_be with same value and type', function($spec) {
      $spec->tester->to_be(3);
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails to_be with different value and same type', function($spec) {
      $spec->tester->to_be(4);
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('fails to_be with same value and different type', function($spec) {
      $spec->tester->to_be("3");
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });
  });

  $spec->describe('to_equal', function($spec) {
    $spec->before(function($spec) {
      $spec->tester = new Sphec\Tester(3, $spec->reporter);
    });
    
    $spec->it('passes to_equal with same value and type', function($spec) {
      $spec->tester->to_equal(3);
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails to_equal with different value and same type', function($spec) {
      $spec->tester->to_equal(4);
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('passes to_equal with same value and different type', function($spec) {
      $spec->tester->to_equal("3");
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });
  });
});
