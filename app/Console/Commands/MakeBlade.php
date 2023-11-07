<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeBlade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:blade {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade file or component file or layout file or make crud blade files for a model';

    /**
     * Files to be created.
     *
     * @var array
     */
    protected array $files = [
        'layouts' => 'default',
        'index',
        'create',
        'edit',
        'show',
    ];

    /**
     * Custom files to be created.
     *
     * @var array
     */
    protected array $customFiles = [
        'component',
        'modal',
        'form',
        'table',
        'custom'
    ];

    /**
     * Model name.
     *
     * @var string
     */
    private ?string $model = null;

    /**
     * Table name.
     *
     * @var string
     */
    private ?string $tableName = null;

    /**
     * Type of blade file to create.
     *
     * @var string
     */
    private ?string $type = 'single';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('name')) {
            $this->makeBladeFile($name);
        } else {
            $choice = $this->choice('What do you want to create?', [
                'Single Blade File',
                'Crud Blade Files for a Model',
            ], 0);

            if ($choice === 'Single Blade File') {
                $this->makeBladeFile($this->ask('Enter blade name?'));
            } else {
                $this->type = 'multiple';
                $this->makeCrudBladeFiles();
            }
        }
    }

    /**
     * Make a blade file.
     *
     * @param string $name
     * @param string|null $folder
     */
    private function makeBladeFile(string $name, string $folder = null): void
    {
        $folder = $folder ?? $this->ask("Enter folder name, leave empty for views folder", 'resources/views');
        $path   = $this->getBladePath($folder);
        $stub   = file_get_contents($this->getBladeStub($folder));
        $dir    = $this->type === 'single' && $folder === 'resources/views' ? $folder : "resources/views/$folder";
        $this->makeDirectory($folder === 'resources/views' ? $path : "$path/$folder");

        if (file_exists("$path/$name.blade.php")) {
            $this->error("Blade file $name.blade.php already exists in $dir folder");
            return;
        }

        file_put_contents("$path/$name.blade.php", $stub);
        $this->info("Blade file created: $name.blade.php in $dir folder");
    }

    /**
     * Make crud blade files for a model.
     */
    private function makeCrudBladeFiles(): void
    {
        if (($this->model = ucfirst($this->ask('Enter model name?')))) {
            if (file_exists(app_path("Models/$this->model.php"))) {
                $this->getModelTableName();
                $this->info("We will create " . implode(',', $this->files) . " files for $this->model model.");
                if ($this->confirm("Do you want to add extra files?")) {
                    $extraFiles = $this->choice('Select extra files to create', $this->customFiles);

                    if ($extraFiles === 'custom') {
                        $extraFiles = $this->ask('Enter custom files to create, separated by comma');
                        $this->files = array_merge($this->files, explode(',', preg_replace('/\s+/', '', $extraFiles)));
                        $this->info("Extra files: " . preg_replace('/\s+/', '', $extraFiles));
                    } else {
                        $this->files[] = $extraFiles;
                        $this->info('Extra files: ' . $extraFiles);
                    }

                    $this->createExtraFiles();
                    return;
                }
                $this->createExtraFiles();
                return;
            } else {
                $this->error("Model $this->model does not exist, please create it first before creating crud blade files for it or use an existing model");
                $this->makeCrudBladeFiles();
            }
        } else {
            $this->error('Please enter model name');
            $this->makeCrudBladeFiles();
        }
    }

    /**
     * Create extra files.
     *
     */
    private function createExtraFiles(): void
    {
        $folder = $this->ask("Enter folder name, leave empty for $this->model folder", $this->tableName);
        $this->info("Creating Files: " . implode(', ', $this->files) . " for $this->model model in $folder folder");
        $progressBar = $this->output->createProgressBar(count($this->files));
        $progressBar->start();
        $this->newLine();
        foreach ($this->files as $relatedFolder => $file) {
            $this->makeBladeFile($file, is_int($relatedFolder) ? $folder : $relatedFolder);
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info("\nFiles created successfully");
    }

    /**
     * Get model table name.
     *
     * @return void
     */
    private function getModelTableName(): void
    {
        $this->tableName = (new ("App\\Models\\$this->model"))->getTable();
    }

    /**
     * Get blade stub.
     *
     * @param string $name
     * @return string
     */
    private function getBladeStub(string $name = 'blade'): string
    {
        return file_exists(base_path("stubs/blade/$name.stub")) ? base_path("stubs/blade/$name.stub") : base_path("stubs/blade/blade.stub");
    }

    private function getBladePath($folder = null): string
    {
        return $folder && $folder !== 'resources/views' ? resource_path("views/$folder") : resource_path("views");
    }

    private function makeDirectory($path): void
    {
        if (!is_dir(dirname($path)) && !mkdir($concurrentDirectory = dirname($path), 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
