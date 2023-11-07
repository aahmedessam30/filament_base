<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:contract {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new contract';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the contract?')) {
            $namespace     = $this->ask('What is the namespace of the contract?', 'App\Contracts');
            $dir           = $namespace !== 'App\Contracts' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Contracts');
            $contract_name = str_contains(strtoupper($name), 'CONTRACT')
                ? preg_replace_callback('/contract/i', fn ($matches) => 'Contract', $name)
                : ucfirst($name) . 'Contract';
            $stub       = str_replace(
                ['{{ namespace }}', '{{ contract_name }}'],
                [$namespace, $contract_name],
                file_get_contents(base_path('stubs/contract.stub')));
            $file      = str_replace('\\', '/', $dir . '/' . $contract_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("Contract $contract_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Contract $contract_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the contract.');
        $this->handle();
    }
}
