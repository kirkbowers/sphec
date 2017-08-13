<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Specify the behavior of a class
Sphec\Sphec::specify('TestProject\Accumulator', function($spec) {

  // Describe how a method of that class is expected to behave
  $spec->describe('add', function($spec) {

    // Set up a subcontext with pre-test conditions shared by multiple examples.
    $spec->context('with default starting value', function($spec) {
    
      // Set up those pre-test conditions in a before action
      $spec->before(function($spec) {
        $spec->accumulator = new TestProject\Accumulator;
      });
      
      // Provide examples of what the expected behavior is.
      $spec->it('should start with a zero value', function($spec) {
        // Note, the "local" member variable accumulator that was create in `before` is
        // available inside the examples.
        
        // Write tests as expectations.
        $spec->expect($spec->accumulator->get_value())->to_be(0);
      });
      
      $spec->it('should accumulate a single value', function($spec) {
        $spec->accumulator->add(3);
        $spec->expect($spec->accumulator->get_value())->to_be(3);
      });
      
      $spec->it('should accumulate more than one value', function($spec) {
        $spec->accumulator->add(3);
        $spec->accumulator->add(5);
        $spec->expect($spec->accumulator->get_value())->to_be(8);
      });      
    });

    // Set up a different context with different pre-test conditions.
    $spec->context('with supplied starting value', function($spec) {
      $spec->before(function($spec) {
        $spec->accumulator = new TestProject\Accumulator(2);
      });
      
      $spec->it('should start with a zero value', function($spec) {
        $spec->expect($spec->accumulator->get_value())->to_be(2);
      });
      
      $spec->it('should accumulate a single value', function($spec) {
        $spec->accumulator->add(3);
        $spec->expect($spec->accumulator->get_value())->to_be(5);
      });
      
      $spec->it('should accumulate more than one value', function($spec) {
        $spec->accumulator->add(3);
        $spec->accumulator->add(5);
        $spec->expect($spec->accumulator->get_value())->to_be(10);
      });      
    });
  });
});
