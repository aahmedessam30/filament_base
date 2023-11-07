<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository file for a model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the repository?')) {
            $namespace  = $this->ask('What is the namespace of the repository?', 'App\Repositories');
            $dir        = $namespace !== 'App\Repositories' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Repositories');
            $class_name = str_contains(strtoupper($name), 'REPOSITORY')
                ? preg_replace_callback('/repository/i', fn ($matches) => 'Repository', $name)
                : ucfirst($name) . 'Repository';
            $stub       = str_replace(
                ['{{ namespace }}', '{{ class_name }}'],
                [$namespace, $class_name],
                file_get_contents(base_path('stubs/repository.stub')));
            $file      = str_replace('\\', '/', $dir . '/' . $class_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("Repository $class_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Repository $class_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the repository.');
        $this->handle();
    }
}
