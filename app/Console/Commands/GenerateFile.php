<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class GenerateFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:component {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $controllerName = "{$name}Controller";
        $requestName = "{$name}Request";
        $gestionName = "Gestion{$name}";

        //Generate Controller
        Artisan::call('make:controller', [
            'name' => $controllerName,
            '--resource' => true,
        ]);

        // Generate Request
        Artisan::call('make:request', [
            'name' => $requestName,
        ]);

        // Lire le contenu du fichier source
        $fileContent = file_get_contents(\config_path("templates/_Gestion.php"));

        // Remplacer les parties spécifiques du contenu
        $nouveauContenu = str_replace('__NAME__', $name, $fileContent);

        file_put_contents(\app_path("Gestions/$gestionName.php"), $nouveauContenu);

    }
}