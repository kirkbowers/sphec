<?php

Sphec\Sphec::specify('Example', function($spec) {
  $spec->it('lazily sets an instance variable that previously did not exist', function($spec) {
    // TODO: replace this with allow to receive with parameters when that's available
    $parent = test_double('Context', ['get_lazy_variable' => 'blah']);
    $parent->_expector = test_double('expector');
    $block = test_double('block');

    $example = new Sphec\Example('', $block, '', $parent);

    $spec->expect($example->foo)->to_be('blah');
    $spec->expect($parent)->to_have_received('get_lazy_variable');
  });

  $spec->it('does not overwrite an instance variable that previously did exist', function($spec) {
    // TODO: replace this with allow to receive with parameters when that's available
    $parent = test_double('Context', ['get_lazy_variable' => 'blah']);
    $parent->_expector = test_double('expector');
    $block = test_double('block');

    $example = new Sphec\Example('', $block, '', $parent);

    $example->foo = 'blech';
    $spec->expect($example->foo)->to_be('blech');
    $spec->expect($parent)->not_to_have_received('get_lazy_variable');
  });
});