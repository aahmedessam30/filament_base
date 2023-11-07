<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeDTO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the DTO?')) {
            $namespace  = $this->ask('What is the namespace of the DTO?', 'App\DTO');
            $dir        = $namespace !== 'App\DTO' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('DTO');
            $class_name = str_contains(strtoupper($name), 'DTO')
                ? preg_replace_callback('/dto/i', fn ($matches) => 'DTO', $name)
                : ucfirst($name) . 'DTO';
            $stub       = str_replace(
                ['{{ namespace }}', '{{ class_name }}'],
                [$namespace, $class_name],
                file_get_contents(base_path('stubs/dto.stub'))
            );
            $file      = str_replace('\\', '/', $dir . '/' . $class_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("DTO $class_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("DTO $class_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the DTO.');
        $this->handle();
    }
}
