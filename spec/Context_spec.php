<?php

// TODO:
// It would be nice if this autoloaded.  Autoloading in PHP is not something I've used 
// before since WordPress is all manually loaded.
require __DIR__ . '/../src/Sphec.php';

Sphec\Sphec::specify('Context', function($spec) {
  $spec->describe('before', function($spec) {
    $spec->it('executes before actions as expected', function($spec) {
      // This is a nasty thing to unit test.  Ideally we'd do this in a lot of 
      // separate examples, and use before to set up a shared scope for all the 
      // similar examples, but that begs the question.  I want to prove `before` works
      // without using it.  So I do some local setup inside this example, and it's a
      // very long example that runs through a few different scenarios.
      
      $tracker = array();
      
      $outer = new Sphec\Context('Outer', function($spec) use (&$tracker) { 
        $spec->before(function($spec) use (&$tracker) {
          $tracker = array('Outer');
        });
      });
      $outer->quiet = true;
      
      $outer_example = $outer->it('does nothing', function($spec) {});

      $outer->run();
      $spec->expect($tracker)->to_equal(array('Outer'));

      $outer_example->run();
      $spec->expect($tracker)->to_equal(array('Outer'));


      $inner = $outer->describe('Inner', function($spec) use (&$tracker) {
        $spec->before(function($spec) use (&$tracker) {
          $tracker[] = 'Inner';
        });
      });
      
      $inner_example1 = $inner->it('does nothing', function($spec) {});
      $inner_example2 = $inner->it('does nothing', function($spec) {});

      $outer_example->run();
      $spec->expect($tracker)->to_equal(array('Outer'));

      $inner->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Inner'));

      $inner_example1->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Inner'));

      $inner_example2->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Inner'));

      
      $outer->before(function($spec) use (&$tracker) {
        $tracker[] = 'Outer2';
      });

      $outer_example->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Outer2'));

      $inner->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Outer2', 'Inner'));

      $inner_example1->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Outer2', 'Inner'));

      $inner_example2->run();
      $spec->expect($tracker)->to_equal(array('Outer', 'Outer2', 'Inner'));
    });
  });
});
