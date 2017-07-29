<?php
namespace Sphec;

// TODO:
// It would be nice if this autoloaded.  Autoloading in PHP is not something I've used 
// before since WordPress is all manually loaded.
// require 'Expector.php';

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
  
  function __construct($label, $block, $indent = '', $parent = NULL) {
    $this->label = $label;
    $this->block = $block;
    $this->indent = $indent;
    $this->parent = $parent;
  }

  abstract public function run();
}
