<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeAbstraction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:abstraction {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new abstraction class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the abstraction?')) {
            $namespace  = $this->ask('What is the namespace of the abstraction?', 'App\Abstractions');
            $dir        = $namespace !== 'App\Abstractions' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Abstractions');
            $abstraction_name = str_contains(strtoupper($name), 'ABSTRACTION')
                ? preg_replace_callback('/abstraction/i', fn($matches) => 'Abstraction', $name)
                : ucfirst($name) . 'Abstraction';
            $stub = str_replace(
                ['{{ namespace }}', '{{ abstraction_name }}'],
                [$namespace, $abstraction_name],
                file_get_contents(base_path('stubs/abstraction.stub')));
            $file = str_replace('\\', '/', $dir . '/' . $abstraction_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("Abstraction $abstraction_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Abstraction $abstraction_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the abstraction.');
        $this->handle();
    }
}
