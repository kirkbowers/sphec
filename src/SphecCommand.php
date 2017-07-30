<?php
namespace Sphec;

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
  }

  protected function execute(InputInterface $input, OutputInterface $output){
    $folders = $input->getArgument('Folders');

    if (empty($folders)) {
      $folders = array('spec');
    }

    $files = array();

    foreach ($folders as $folder) {
      if (is_dir($folder)) {
        $files = array_merge($files, glob_recursive($folder . '/*_spec.php'));
      } else {
        $files[] = $folder;
      }
    }

    // TODO:
    // Make the Sphec object compile all tests together into one data structure before
    // running.  Make it take this output object and respect the verbose settings.
    foreach ($files as $file) {
//       $output->writeln($file);
      include $file;
    }
  }
}

