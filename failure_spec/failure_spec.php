<?php

Sphec\Sphec::specify('Failure', function($spec) {
  $spec->it('fails with ints and to_be_equivalent', function($spec) {
    $spec->expect(3)->to_be_equivalent(4);
  });

  $spec->it('fails with int and string and to_be_equivalent', function($spec) {
    $spec->expect(3)->to_be_equivalent("3");
  });

  $spec->it('fails with arrays and to_be_equivalent', function($spec) {
    $spec->expect(array(1, 2, 3))->to_be_equivalent(array(1, 2, 3, 4));
  });

  $spec->it('fails with ints and to_be', function($spec) {
    $spec->expect(3)->to_be(4);
  });

  $spec->it('fails with ints and to_equal', function($spec) {
    $spec->expect(3)->to_equal(4);
  });

  $spec->it('fails with false and to_be_true', function($spec) {
    $spec->expect(false)->to_be_true();
  });

  $spec->it('fails with [] and to_be_truthy', function($spec) {
    $spec->expect(array())->to_be_truthy();
  });
});
