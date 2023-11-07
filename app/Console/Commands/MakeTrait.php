<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the trait?')) {
            $namespace  = $this->ask('What is the namespace of the trait?', 'App\Traits');
            $dir        = $namespace !== 'App\Traits' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Traits');
            $trait_name = str_contains(strtoupper($name), 'TRAIT')
                ? preg_replace_callback('/trait/i', fn($matches) => 'Trait', $name)
                : ucfirst($name) . 'Trait';
            $stub = str_replace(
                ['{{ namespace }}', '{{ trait_name }}'],
                [$namespace, $trait_name],
                file_get_contents(base_path('stubs/trait.stub')));
            $file = str_replace('\\', '/', $dir . '/' . $trait_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("Trait $trait_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Trait $trait_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the trait.');
        $this->handle();
    }
}
