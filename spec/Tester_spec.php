<?php

// TODO:
// It would be nice if this autoloaded.  Autoloading in PHP is not something I've used 
// before since WordPress is all manually loaded.
require __DIR__ . '/../src/Sphec.php';

Sphec\Sphec::specify('Tester', function($spec) {

  $spec->describe('to_be_equivalent', function($spec) {
    $spec->it('passes to_be_equivalent with same value and type', function($spec) {
      // TODO:
      // These first two lines that are repeated over and over should be moved up to
      // a `before` method
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      // TODO:
      // This line also should be moved up to a `before` method, but in a `describe` block
      // for "to_be_equivalent"
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_be_equivalent(3);
      $spec->expect($reporter->passed)->to_be(1);
      $spec->expect($reporter->failed)->to_be(0);
    });

    $spec->it('fails to_be_equivalent with different value and same type', function($spec) {
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_be_equivalent(4);
      $spec->expect($reporter->passed)->to_be(0);
      $spec->expect($reporter->failed)->to_be(1);
    });

    $spec->it('fails to_be_equivalent with same value and different type', function($spec) {
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_be_equivalent("3");
      $spec->expect($reporter->passed)->to_be(0);
      $spec->expect($reporter->failed)->to_be(1);
    });
  });

  $spec->describe('to_be', function($spec) {
    $spec->it('passes to_be with same value and type', function($spec) {
      // TODO:
      // These first two lines that are repeated over and over should be moved up to
      // a `before` method
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      // TODO:
      // This line also should be moved up to a `before` method, but in a `describe` block
      // for "to_be"
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_be(3);
      $spec->expect($reporter->passed)->to_be(1);
      $spec->expect($reporter->failed)->to_be(0);
    });

    $spec->it('fails to_be with different value and same type', function($spec) {
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_be(4);
      $spec->expect($reporter->passed)->to_be(0);
      $spec->expect($reporter->failed)->to_be(1);
    });

    $spec->it('fails to_be with same value and different type', function($spec) {
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_be("3");
      $spec->expect($reporter->passed)->to_be(0);
      $spec->expect($reporter->failed)->to_be(1);
    });
  });

  $spec->describe('to_equal', function($spec) {
    $spec->it('passes to_equal with same value and type', function($spec) {
      // TODO:
      // These first two lines that are repeated over and over should be moved up to
      // a `before` method
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      // TODO:
      // This line also should be moved up to a `before` method, but in a `describe` block
      // for "to_equal"
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_equal(3);
      $spec->expect($reporter->passed)->to_be(1);
      $spec->expect($reporter->failed)->to_be(0);
    });

    $spec->it('fails to_equal with different value and same type', function($spec) {
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_equal(4);
      $spec->expect($reporter->passed)->to_be(0);
      $spec->expect($reporter->failed)->to_be(1);
    });

    $spec->it('passes to_equal with same value and different type', function($spec) {
      $reporter = new Sphec\Reporter;
      $reporter->verbose = false;
      $tester = new Sphec\Tester(3, $reporter);
    
      $tester->to_equal("3");
      $spec->expect($reporter->passed)->to_be(1);
      $spec->expect($reporter->failed)->to_be(0);
    });
  });
});
