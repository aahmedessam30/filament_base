<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeFacade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:facade {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new facade class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the facade?')) {
            $namespace  = $this->ask('What is the namespace of the facade?', 'App\Facades');
            $dir        = $namespace !== 'App\Facades' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Facades');
            $class_name = str_contains(strtoupper($name), 'FACADE')
                ? preg_replace_callback('/facade/i', fn($matches) => 'Facade', $name)
                : ucfirst($name);
            $stub = str_replace(
                ['{{ namespace }}', '{{ class_name }}', '{{ facade_name }}'],
                [$namespace, $class_name, strtolower($name)],
                file_get_contents(base_path('stubs/facade.stub')));
            $file = str_replace('\\', '/', $dir . '/' . $class_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("Facade $class_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Facade $class_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the facade.');
        $this->handle();
    }
}
