<?php

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

  $spec->describe('after', function($spec) {
    $spec->it('executes after actions as expected', function($spec) {
      // This is a nasty thing to unit test.  Ideally we'd do this in a lot of 
      // separate examples, and use before to set up a shared scope for all the 
      // similar examples, but that begs the question.  I want to prove `before` works
      // without using it.  So I do some local setup inside this example, and it's a
      // very long example that runs through a few different scenarios.
      
      $tracker = array();
      
      $outer = new Sphec\Context('Outer', function($spec) use (&$tracker) { 
        $spec->before(function($spec) use (&$tracker) {
          $tracker = array('Before Outer');
        });

        $spec->after(function($spec) use (&$tracker) {
          $tracker[] = 'After Outer';
        });
      });
      $outer->quiet = true;
      
      $outer_example = $outer->it('Outer Example', function($spec) use (&$tracker) {
        $tracker[] = 'Outer Example';      
      });


      $middle = $outer->describe('Middle', function($spec) use (&$tracker) {
        $spec->before(function($spec) use (&$tracker) {
          $tracker[] = 'Before Middle';
        });

        $spec->after(function($spec) use (&$tracker) {
          $tracker[] = 'After Middle';
        });

        $spec->after(function($spec) use (&$tracker) {
          $tracker[] = 'After Middle2';
        });
      });

      $middle_example = $middle->it('Middle Example', function($spec) use (&$tracker) {
        $tracker[] = 'Middle Example';      
      });


      $inner = $middle->describe('Inner', function($spec) use (&$tracker) {
        $spec->before(function($spec) use (&$tracker) {
          $tracker[] = 'Before Inner';
        });

        $spec->after(function($spec) use (&$tracker) {
          $tracker[] = 'After Inner';
        });
      });
      
      $inner_example1 = $inner->it('Inner Example 1', function($spec) use (&$tracker) {
        $tracker[] = 'Inner Example 1';      
      });
      $inner_example2 = $inner->it('Inner Example 2', function($spec) use (&$tracker) {
        $tracker[] = 'Inner Example 2';      
      });

      $outer_example->run();
      $spec->expect($tracker)->to_equal(array(
        'Before Outer', 
        'Outer Example',
        'After Outer',
      ));

      $middle_example->run();
      $spec->expect($tracker)->to_equal(array(
        'Before Outer', 
        'Before Middle',
        'Middle Example',
        'After Middle',
        'After Middle2',
        'After Outer',
      ));

      $inner_example1->run();
      $spec->expect($tracker)->to_equal(array(
        'Before Outer', 
        'Before Middle',
        'Before Inner',
        'Inner Example 1',
        'After Inner',
        'After Middle',
        'After Middle2',
        'After Outer',
      ));

      $inner_example2->run();
      $spec->expect($tracker)->to_equal(array(
        'Before Outer', 
        'Before Middle',
        'Before Inner',
        'Inner Example 2',
        'After Inner',
        'After Middle',
        'After Middle2',
        'After Outer',
      ));
    });
  });
});
