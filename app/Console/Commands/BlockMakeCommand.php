<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class BlockMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:block {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the necessary files for a Block';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * return void
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->getPath($name);

        $composerFile = "{$path}/Composer.php";
        $adminViewFile = "{$path}/Views/admin.blade.php";
        $frontViewFile = "{$path}/Views/front.blade.php";

        if ($this->alreadyExists($composerFile, $adminViewFile, $frontViewFile)) {
            $this->error("There is already a block with the name of {$this->argument('name')}");

            return false;
        }

        $this->makeDirectories($path);

        $this->files->put($composerFile, $this->buildComposer($name));
        $this->files->put($adminViewFile, $this->buildAdminView());
        $this->files->put($frontViewFile, $this->buildFrontView());

        $this->info("Block created successfully in 'App/Blocks/{$name}'");

        return true;
    }

    /**
     * Get the block's composer contents.
     *
     * @param string $name
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildComposer($name)
    {
        return str_replace(
            'DummyNamespace', 'App\Blocks\\' . $name, $this->files->get($this->getComposerStub())
        );
    }

    /**
     * Get the block's admin view contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildAdminView()
    {
        return $this->files->get($this->getAdminViewStub());
    }

    /**
     * Get the block's front view contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFrontView()
    {
        return $this->files->get($this->getFrontViewStub());
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the block.'],
        ];
    }

    /**
     * Get the composer stub file for the generator.
     *
     * @return string
     */
    protected function getComposerStub()
    {
        return app_path('Console/Commands/Stubs/Blocks/composer.stub');
    }

    /**
     * Get the admin view stub file for the generator.
     *
     * @return string
     */
    protected function getAdminViewStub()
    {
        return app_path('Console/Commands/Stubs/Blocks/admin.view.stub');
    }

    /**
     * Get the front view stub file for the generator.
     *
     * @return string
     */
    protected function getFrontViewStub()
    {
        return app_path('Console/Commands/Stubs/Blocks/front.view.stub');
    }

    /**
     * Get the application path.
     *
     * @param $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace('\\', '/', str_replace($this->laravel->getNamespace(), '', $name));

        return "{$this->laravel['path']}/Blocks/{$name}";
    }

    /**
     * Determine if the module files already exists.
     *
     * @param string $composerFile
     * @param string $adminViewFile
     * @param string $frontViewFile
     * @return bool
     */
    protected function alreadyExists($composerFile, $adminViewFile, $frontViewFile)
    {
        return $this->files->exists($composerFile) ||
            $this->files->exists($adminViewFile) ||
            $this->files->exists($frontViewFile);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $path
     * @return string
     */
    protected function makeDirectories($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }

        if (!$this->files->isDirectory($path . '/Views')) {
            $this->files->makeDirectory($path . '/Views', 0755, true, true);
        }
    }
}
