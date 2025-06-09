<?php

namespace Luna\Db;

use Exception;
use Luna\Utils\Environment;
use Luna\Utils\MakeFile;

class Migrate {
    public static function create(string $dir, string $name, ?string $table = null): string
    {
        $time = floor(microtime(true) * 1000);

        $filename = $time . '-' . $name . '.php';

        if (!$table) {
            $table = 'table';
        }

        MakeFile::make(Environment::get("__DIR__") . '/' . $dir . '/' . $filename, [
            '<?php',
            '',
            'use Illuminate\Database\Capsule\Manager;',
            'use Illuminate\Database\Schema\Blueprint;',
            '',
            'return new class',
            '{',
            '    public function up()',
            '    {',
            '        Manager::schema()->create("' . $table . '", function(Blueprint $table) {',
            '            $table->id();',
            '        });',
            '    }',
            '',
            '    public function down()',
            '    {',
            '        Manager::schema()->dropIfExists("' . $table . '");',
            '    }',
            '};'
        ]);

        return $dir . '/' . $filename;
    }

    public static function run(string $dir, ?string $name = null): array
    {
        $filenames = self::getMigrationFilenames($dir);

        $executedFilenames = Migration::pluck('filename')->toArray();

        $batch = Migration::max('batch');

        if (!$batch)  {
            $batch = 0;
        }

        $batch ++;

        foreach ($filenames as $key => $filename) {
            if (in_array($filename, $executedFilenames) || ($name && strpos($filename, $name) === false)) {
                unset($filenames[$key]);
                continue;
            }

            $migration = include Environment::get("__DIR__") . '/' . $dir . '/' . $filename;
            $migration->up();

            Migration::create([
                'filename' => $filename,
                'batch' => $batch
            ]);
        }

        return $filenames;
    }

    public static function rollback(string $dir): array
    {
        $batch = Migration::max('batch');
        $filenames = Migration::where('batch', $batch)->orderByDesc('filename')->pluck('filename')->toArray();
        
        foreach ($filenames as $filename) {
            $migration = include Environment::get("__DIR__") . '/' . $dir . '/' . $filename;
            $migration->down();

            Migration::where('filename', $filename)->delete();
        }

        return $filenames;
    }

    public static function fresh(string $dir): array
    {
        $batch = Migration::max('batch');
        
        while ($batch) {
            self::rollback($dir);
            $batch = Migration::max('batch');
        }

        $filenames = self::run($dir);
        
        return $filenames;
    }

    private static function getMigrationFilenames(string $dir, string $order = 'asc'): array
    {
        $dh = opendir(Environment::get('__DIR__') . '/' . $dir);

        if (!$dh) {
            throw new Exception("Diretório não encontrado: " . $dir);
        }

        $filenames = [];

        while (($filename = readdir($dh)) !== false) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }
                
            $filenames[] = $filename;
        }

        closedir($dh);

        if ($order === 'asc') {
            sort($filenames);
        } else {
            rsort($filenames);
        }

        return $filenames;
    }
}