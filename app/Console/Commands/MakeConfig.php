<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:config {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new config class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class_name = strtolower($this->argument('name') ?? $this->ask('What is the name of the config?'));
        $stub       = file_get_contents(base_path('stubs/config.stub'));

        if (file_exists(config_path("$class_name.php"))) {
            $this->error("Config $class_name already exists.");
            return;
        }

        file_put_contents(config_path("$class_name.php"), $stub);
        $this->info("Config $class_name created successfully.");
    }
}
