<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeNotificationChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:notification-channel {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new notification channel class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name') ?? $this->ask('What is the name of the notification channel?')) {
            $namespace  = $this->ask('What is the namespace of the notification channel?', 'App\Channels');
            $dir        = $namespace !== 'App\Channels' ? base_path(ucfirst(str_replace('\\', '/', strtolower($namespace)))) : app_path('Channels');
            $channel_name = str_contains(strtoupper($name), 'CHANNEL')
                ? preg_replace_callback('/channel/i', fn($matches) => 'Channel', $name)
                : ucfirst($name) . 'Channel';
            $stub = str_replace(
                ['{{ namespace }}', '{{ channel_name }}'],
                [$namespace, $channel_name],
                file_get_contents(base_path('stubs/notification-channel.stub')));
            $file = str_replace('\\', '/', $dir . '/' . $channel_name . '.php');

            if (!is_dir($dir) && !mkdir($concurrentDirectory = $dir) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            if (file_exists($file)) {
                $this->error("Notification channel $channel_name already exists.");
                return;
            }

            file_put_contents($file, $stub);
            $this->info("Notification channel $channel_name created successfully in $namespace.");
            return;
        }

        $this->error('Please provide a name for the notification channel.');
        $this->handle();
    }
}
