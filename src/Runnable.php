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
  public $quiet = false;
  
  function __construct($label, $block, $indent = '', $parent = NULL) {
    $this->label = $label;
    $this->block = $block;
    $this->indent = $indent;
    $this->parent = $parent;
    
    if ($parent) {
      $this->quiet = $parent->quiet;
    }
  }

  abstract public function run();
}
