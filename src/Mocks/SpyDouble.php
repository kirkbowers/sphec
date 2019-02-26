<?php
namespace Sphec\Mocks;

/**
 * @author Kirk Bowers
 */
class SpyDouble extends Double {
  private $_spyee;

  public static function factory($spyee) {
    if ($spyee instanceof \Sphec\Mocks\Double) {
      return $spyee;
    } else {
      return new \Sphec\Mocks\SpyDouble($spyee);
    }
  }

  public function __construct($spyee) {
    parent::__construct();
    $this->_spyee = $spyee;
  }

  protected function __sphec_handle_unstubbed_method($name, $arguments) {
    return $this->_spyee->$name(...$arguments);
  }
}
