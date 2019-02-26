<?php

namespace Sphec;

if (file_exists('spec/sphec_helper.php')) {
  include 'spec/sphec_helper.php';
}

if ( ! function_exists('glob_recursive'))
{
  // Does not support flag GLOB_BRACE
  function glob_recursive($pattern, $flags = 0)
  {
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
      $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }

    return $files;
  }
}


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;


class SphecCommand extends Command {

  protected function configure() {
    $this->setName("sphec")
        ->setDescription("Runs all the sphec spec files with the name pattern *_spec.php")
        ->addArgument('Folders', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Folders to look for spec files in (Default: spec)');

    $this->addOption(
      'format',
      'f',
      InputOption::VALUE_OPTIONAL,
      'The format for the output, choices are progress and documentation',
      'progress');
  }

  protected function execute(InputInterface $input, OutputInterface $output){
    $folders = $input->getArgument('Folders');

    if (empty($folders)) {
      $folders = array('spec');
    }

    $php_files = array();
    $shorthand_files = array();

    foreach ($folders as $folder) {
      if (is_dir($folder)) {
        $php_files = array_merge($php_files, glob_recursive($folder . '/*_spec.php'));
        $shorthand_files = array_merge($shorthand_files, glob_recursive($folder . '/*_spec.sphec'));
      } else {
        // It's a file.  Trust that it must have a suffix.  If not, we can probably
        // discard it.
        preg_match('/\.(\w+)$/', $folder, $matches);
        if (isset($matches[1])) {
          $suffix = $matches[1];
          if ($suffix == 'php') {
            $php_files[] = $folder;
          } else if ($suffix == 'sphec') {
            $shorthand_files[] = $folder;
          }
        }
      }
    }


    // set up the Sphec environment to collect all the results in one Reporter
    // that has this Symfony Output object.
    if (substr($input->getOption('format'), 0, 1) === 'd') {
      Sphec::set_reporter(new Reporters\DocumentationReporter($output));
    } else {
      Sphec::set_reporter(new Reporters\ProgressReporter($output));
    }

    foreach ($php_files as $file) {
      include $file;
    }

    foreach ($shorthand_files as $file) {
      $file = realpath($file);
      $file_handle = fopen($file, "r") or die("Unable to open " . $file);
      $shorthand = fread($file_handle, filesize($file));
      fclose($file_handle);

      $dsl = new DSLifier($shorthand, $file);

      // This should be safe, or at least no less safe than running the include above
      // on the non shorthand PHP spec files.  This is code that the person running sphec
      // supposedly wrote, so you're running your own code here.  It should be
      // trustworthy.
      $php = $dsl->get_php();
      eval($php);
    }

    Sphec::run();
  }
}
