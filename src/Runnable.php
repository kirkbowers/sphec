<?php
namespace Sphec;

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
abstract class Runnable {
  public $label;
  protected $block;
  protected $indent;
  protected $parent;
  
  public $expector;
  
  function __construct($label, $block, $indent = '', $parent = NULL) {
    $this->label = $label;
    $this->block = $block;
    $this->indent = $indent;
    $this->parent = $parent;
  }

  abstract public function run();
}
