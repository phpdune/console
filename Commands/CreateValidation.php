<?php

declare(strict_types=1);

namespace Dune\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateValidation extends Command
{
    /**
     * command name
     *
     * @var string
     */

    protected static $defaultName = 'create:validation';

    /**
     * default symfony console configure method
     * setting description and arguments
     *
     */
    protected function configure(): void
    {
        $this
        ->setDescription('Create a validation file')
        ->addArgument('name', InputArgument::REQUIRED, 'Validation name');
    }
    /**
     * main execute function
     * create validation by given name (argument)
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $this->getPrefixName(
            $input->getArgument('name')
        );
        $message = new SymfonyStyle($input, $output);
        if(!$this->validationExist($name)) {
            $stub = $this->getStub($name);
            $file = fopen("app/validation/" . $name . ".php", "w");
            fwrite($file, $stub);
            fclose($file);
            $message->success(sprintf('%s Created Successfully', $name));
            return Command::SUCCESS;
        }
        $message->error(sprintf('%s Already Exists', $name));
        return Command::FAILURE;
    }
    /**
     * check the validation exists or not
     *
     * @param string $name
     *
     * @return bool
     */
    protected function validationExist(string $name): bool
    {
        if(!file_exists(PATH.'/app/validation')) {
            mkdir(PATH.'/app/validation');
        }
        return file_exists(PATH . "/app/validation/" . $name . ".php");
    }
    /**
     * return the validation stub file
     *
     * @param string $name
     *
     * @return string
     */
    protected function getStub(string $name): string
    {
        $stub = PATH . "/vendor/dune/framework/src/Dune/Stubs/validation.stub";
        $stub = file_get_contents($stub);
        $stub = str_replace("{{ Validation }}", $name, $stub);
        return $stub;
    }
    /**
     * check the validation name ends with 'Validation' , if not then return validation name + Validation
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPrefixName(string $name): string
    {
        if(!str_ends_with($name, 'Validation')) {
            return $name.'Validation';
        }
        return $name;
    }
}
