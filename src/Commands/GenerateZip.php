<?php

namespace Lara41\Utils\Commands;

use Lara41\Utils\Helpers\Zip;
use Illuminate\Console\Command;
use Lara41\Utils\Helpers\Gitignore;
use Lara41\Utils\Helpers\HandlerGenerator;

class GenerateZip extends Command
{
    protected $github;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lambda:zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare a repository for Lambda upload';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zip = Zip::fromDir(base_path(), Gitignore::getExcludes()->except('/vendor')->toArray(), $randomPath = storage_path('tmp/' . str_random(20) . '.zip'));

        $zip->addFileFromString(HandlerGenerator::generate([
            'host' => 'lara41.wip',
            'prefix' => '/' . substr($zip->getClient()->statindex(0)['name'], 0, -1),
            'https' => true,
        ]), 'handler.js')
            ->addFile(__DIR__ . '/../../utils/php-cgi', 'php-cgi');

        $zip->saveTo($randomPath);

        $this->info("Zip saved to [$randomPath]");
    }
}
