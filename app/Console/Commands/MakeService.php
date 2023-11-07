<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name?} {--f|facade} {--p|provider} {--a|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the service?')) {
            $addService = $this->confirm("Do you want to add 'Service' to the end of the name?", false);
            $namespace  = $this->ask('What is the namespace of the service?', 'App\Services');
            $dir        = $namespace !== 'App\Services' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Services');
            $class_name = str_contains(strtoupper($name), 'SERVICE')
                ? preg_replace_callback('/service/i', fn ($matches) => 'Service', $name)
                : ($addService ? ucfirst($name) . 'Service' : ucfirst($name));
            $stub       = str_replace(
                ['{{ namespace }}', '{{ class_name }}'],
                [$namespace, $class_name],
                file_get_contents(base_path('stubs/service.stub')));
            $file      = str_replace('\\', '/', "$dir/$class_name.php");

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            $this->handleOptions(ucfirst($name));

            if (file_exists($file)) {
                $this->error("Service $class_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Service $class_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the service.');
        $this->handle();
    }

    /**
     * Handle the options.
     *
     * @param string $class_name
     * @return void
     */
    private function handleOptions(string $class_name)
    {
        if ($this->option('facade')) {
            $this->call('make:facade', ['name' => $class_name]);
        }

        if ($this->option('provider')) {
            $this->call('make:provider', ['name' => "{$class_name}Provider"]);
        }

        if ($this->option('all')) {
            $this->call('make:facade', ['name' => $class_name]);
            $this->call('make:provider', ['name' => "{$class_name}Provider"]);
        }
    }
}
