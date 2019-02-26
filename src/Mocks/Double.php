<?php
namespace Sphec\Mocks {

  /**
   * The control class for building specifications.
   *
   * @author Kirk Bowers
   */
  class Double {
    private $_legal_functions = array();
    private $_function_call_counts = array();
    private $_legal_functions_with_params = array();
    private $_name = '<Anonymous>';

    public function __construct($id = null, $legal_functions = null) {
      if (is_array($id)) {
        $this->__sphec_add_functions($id);
      } else if ($id) {
        $this->_name = $id;
        if (is_array($legal_functions)) {
          $this->__sphec_add_functions($legal_functions);
        }
      }
    }

    private function __sphec_add_functions($hash) {
      foreach ($hash as $name => $result) {
        $this->__sphec_add_legal_function($name, $result);
      }
    }

    public function __sphec_name() {
      return $this->_name;
    }

    public function __sphec_is_legal_function($name) {
      return array_key_exists($name, $this->_legal_functions);
    }

    public function __sphec_add_legal_function($name, ...$args) {
      if (!isset($this->_legal_functions_with_params[$name])) {
        $this->_legal_functions_with_params[$name] = [];
      }
      if (count($args) > 1) {
        $params = $args[0];
        $result = $args[1];
        if (!isset($this->_legal_functions[$name])) {
          $this->_legal_functions[$name] = null;
        }
        $this->__sphec_add_function_with_params($name, $params, $result);
      } else {
        $result = count($args) > 0 ? $args[0] : null;
        $this->_legal_functions[$name] = $result;
      }
      $this->_function_call_counts[$name] = 0;
    }

    public function __sphec_function_call_count($name, $params = null) {
      if ($this->__sphec_is_legal_function($name)) {
        if ($params) {
          $index = $this->__sphec_find_function_with_params($name, $params);
          $tuple = $this->_legal_functions_with_params[$name][$index];
          return $tuple['call_count'];
        }
        return $this->_function_call_counts[$name];
      } else {
        // TODO:
        // Should this throw an exception?
        return 0;
      }
    }

    public function __call($name, $arguments) {
      if ($this->__sphec_is_legal_function($name)) {
        $this->_function_call_counts[$name]++;
        if (count($arguments) > 0) {
          if (false !== ($index = $this->__sphec_existing_function_with_params($name, $arguments))) {
            $this->__sphec_increment_call_count_with_params_at_index($name, $index);
            return $this->__sphec_result_with_params_at_index($name, $index);
          } else {
            $index = $this->__sphec_add_function_with_params($name, $arguments);
            $this->__sphec_increment_call_count_with_params_at_index($name, $index);
            return $this->_legal_functions[$name];
          }
        }
        return $this->_legal_functions[$name];
      } else {
        return $this->__sphec_handle_unstubbed_method($name, $arguments);
      }
    }

    protected function __sphec_handle_unstubbed_method($name, $arguments) {
      throw new UnstubbedMethodException("Call of unstubbed method $name on test double $this->_name");
    }

    public function __toString() {
      return "test_double($this->_name)";
    }

    private function __sphec_find_function_with_params($name, $params, $result = null) {
      if (false !== ($index = $this->__sphec_existing_function_with_params($name, $params, $result))) {
        return $index;
      }

      $index = $this->__sphec_add_function_with_params($name, $params, $result);
      return $index;
    }

    private function __sphec_existing_function_with_params($name, $params, $result = null) {
      if (isset($this->_legal_functions_with_params[$name])) {
        foreach($this->_legal_functions_with_params[$name] as $index => $tuple) {
          if ($params == $tuple['params']) {
            return $index;
          }
        }
      }
      return false;
    }

    private function __sphec_add_function_with_params($name, $params, $result = null) {
      $tuple = [
        'params' => $params,
        'result' => $result,
        'call_count' => 0
      ];

      // Using array_push here instead of [] = notation because it returns the count
      return array_push($this->_legal_functions_with_params[$name], $tuple) - 1;
    }

    private function __sphec_increment_call_count_with_params_at_index($name, $index) {
      $this->_legal_functions_with_params[$name][$index]['call_count'] += 1;
    }

    private function __sphec_result_with_params_at_index($name, $index) {
      return $this->_legal_functions_with_params[$name][$index]['result'];
    }
  }
}
