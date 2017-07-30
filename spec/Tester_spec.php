<?php

Sphec\Sphec::specify('Tester', function($spec) {
  $spec->before(function($spec) {
    $spec->reporter = new Sphec\Reporter;
  });

  $spec->describe('to_be_equivalent', function($spec) {
    $spec->before(function($spec) {
      $spec->tester = new Sphec\Tester(3, $spec->reporter);
    });
    
    $spec->it('passes with same value and type', function($spec) {
      $spec->tester->to_be_equivalent(3);
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails with different value and same type', function($spec) {
      $spec->tester->to_be_equivalent(4);
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('fails with same value and different type', function($spec) {
      $spec->tester->to_be_equivalent("3");
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });
  });

  $spec->describe('to_be', function($spec) {
    $spec->before(function($spec) {
      $spec->tester = new Sphec\Tester(3, $spec->reporter);
    });
    
    $spec->it('passes with same value and type', function($spec) {
      $spec->tester->to_be(3);
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails with different value and same type', function($spec) {
      $spec->tester->to_be(4);
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('fails with same value and different type', function($spec) {
      $spec->tester->to_be("3");
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });
  });

  $spec->describe('to_equal', function($spec) {
    $spec->before(function($spec) {
      $spec->tester = new Sphec\Tester(3, $spec->reporter);
    });
    
    $spec->it('passes with same value and type', function($spec) {
      $spec->tester->to_equal(3);
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails with different value and same type', function($spec) {
      $spec->tester->to_equal(4);
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('passes with same value and different type', function($spec) {
      $spec->tester->to_equal("3");
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });
  });

  $spec->describe('to_be_true', function($spec) {
    $spec->it('passes with true', function($spec) {
      $spec->tester = new Sphec\Tester(true, $spec->reporter);
      $spec->tester->to_be_true();
      $spec->expect($spec->reporter->passed)->to_be(1);
      $spec->expect($spec->reporter->failed)->to_be(0);
    });

    $spec->it('fails with false', function($spec) {
      $spec->tester = new Sphec\Tester(false, $spec->reporter);
      $spec->tester->to_be_true();
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });

    $spec->it('fails with a non-boolean truthy value', function($spec) {
      $spec->tester = new Sphec\Tester(1, $spec->reporter);
      $spec->tester->to_be_true();
      $spec->expect($spec->reporter->passed)->to_be(0);
      $spec->expect($spec->reporter->failed)->to_be(1);
    });
  });
});
