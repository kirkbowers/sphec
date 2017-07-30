<?php

Sphec\Sphec::specify('Accumulator', function($spec) {
  $spec->describe('add', function($spec) {
    $spec->context('with default starting value', function($spec) {
      $spec->before(function($spec) {
        $spec->accumulator = new TestProject\Accumulator;
      });
      
      $spec->it('should start with a zero value', function($spec) {
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
