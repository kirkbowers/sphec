<?php

require_once __DIR__ . '/../vendor/autoload.php';

Sphec\Sphec::specify('DSLifier', function($spec) {
  $spec->describe('is_blank_or_comment', function($spec) {
    $spec->it('returns true on an empty string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns true on a bunch of spaces', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('           ');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns true starting with a hash', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('# This is a comment');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns true starting with two slashes', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('// This is a comment');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns false otherwise', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('specify something');
      $spec->expect($result)->to_be_falsy();
    });
  });


  $spec->describe('matches_command', function($spec) {
    $spec->it('returns true when starting with specify', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->matches_command('specify', 'specify MyClass');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns false when starting with anything else', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->matches_command('specify', '$spec->context');
      $spec->expect($result)->to_be_falsy();
    });
  });


  $spec->describe('process_specify_command', function($spec) {
    $spec->it('converts a specify with a simple string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('specify', 'specify MyClass');
      $result = $dsl->process_specify_command($matches);
      $expected = "Sphec\\Sphec::specify('MyClass', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('converts a specify with a single quote', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('specify', "specify can't touch this");
      $result = $dsl->process_specify_command($matches);
      $expected = "Sphec\\Sphec::specify('can\\'t touch this', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_describe_command', function($spec) {
    $spec->it('converts a describe with a simple string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('describe', 'describe my_method');
      $result = $dsl->process_describe_command($matches);
      $expected = "\$spec->describe('my_method', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_context_command', function($spec) {
    $spec->it('converts a context with a simple string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('context', 'context when something interesting happens');
      $result = $dsl->process_context_command($matches);
      $expected = "\$spec->context('when something interesting happens', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_it_command', function($spec) {
    $spec->it('converts an it with a simple string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('it', 'it does what I expect');
      $result = $dsl->process_it_command($matches);
      $expected = "\$spec->it('does what I expect', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_before_command', function($spec) {
    $spec->it('converts a before', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_simple_command('before', 'before');
      $result = $dsl->process_before_command($matches);
      $expected = "\$spec->before(function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_after_command', function($spec) {
    $spec->it('converts an after', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_simple_command('after', 'after    ');
      $result = $dsl->process_after_command($matches);
      $expected = "\$spec->after(function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });
  
  
  
  $spec->describe('get_php', function($spec) {
    $spec->it('closes a bare specify', function($spec) {
      $input = <<<EOF
specify MyClass
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  
    $spec->it('closes an indented specify', function($spec) {
      $input = <<<EOF
  specify MyClass
EOF;

      $output = <<<EOF
  Sphec\\Sphec::specify('MyClass', function(\$spec) {
  });

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  
    $spec->it('closes a specify and a describe', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe('my_method', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  
    $spec->it('closes a specify and two sibling describes', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
  describe other_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe('my_method', function(\$spec) {
  });
  \$spec->describe('other_method', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  
    $spec->it('closes a specify and embedded describes', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
    describe other_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe('my_method', function(\$spec) {
    \$spec->describe('other_method', function(\$spec) {
    });
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  
    $spec->it('closes a specify and embedded describes and contexts', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
    context interesting situation
    context other situation
  describe other_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe('my_method', function(\$spec) {
    \$spec->context('interesting situation', function(\$spec) {
    });
    \$spec->context('other situation', function(\$spec) {
    });
  });
  \$spec->describe('other_method', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  });  
});
    