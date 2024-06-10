<?php
namespace Luna\Db;

use Exception;
use Luna\Utils\MakeFile;

class Migrate {
    public static function create($dir, $name, $table = false) {
        $time = floor(microtime(true) * 1000);

        $filename = $time . '-' . $name . '.php';

        if(!$table)
            $table = 'table';

        MakeFile::make($dir . $filename, [
            '<?php',
            'use Illuminate\Database\Capsule\Manager as Capsule;',
            'use Illuminate\Database\Schema\Blueprint;',
            '',
            'return new class {',
            '    public function up() {',
            '        Capsule::schema()->create("' . $table . '", function (Blueprint $table) {',
            '            $table->id();',
            '        });',
            '    }',
            '',
            '    public function down() {',
            '        Capsule::schema()->dropIfExists("' . $table . '");',
            '    }',
            '};'
        ]);

        return $dir . $filename;
    }

    public static function run($dir, $name = false) {
        $filenames = self::getMigrationFilenames($dir);

        $executedFilenames = Migration::pluck('filename')->toArray();

        $batch = Migration::max('batch');

        if(!$batch) $batch = 0;

        $batch ++;

        foreach($filenames as $key => $filename) {
            if(in_array($filename, $executedFilenames) || ($name && strpos($filename, $name) === false)) {
                unset($filenames[$key]);
                continue;
            }

            $migration = include $dir . '/' . $filename;
            $migration->up();

            Migration::create([
                'filename' => $filename,
                'batch' => $batch
            ]);
        }

        return $filenames;
    }

    public static function rollback($dir) {
        $batch = Migration::max('batch');
        $filenames = Migration::where('batch', $batch)->orderByDesc('filename')->pluck('filename')->toArray();

        foreach($filenames as $filename) {
            $migration = include $dir . '/' . $filename;
            $migration->down();

            Migration::where('filename', $filename)->delete();
        }

        return $filenames;
    }

    public static function fresh($dir) {
        $batch = Migration::max('batch');
        
        while($batch) {
            self::rollback($dir);
            $batch = Migration::max('batch');
        }

        self::run($dir);
    }

    private static function getMigrationFilenames($dir, $order = 'asc') {
        $dh = opendir($dir);

        if(!$dh)
            throw new Exception("Directory not found");

        $filenames = [];

        while(($filename = readdir($dh)) !== false) {
            if($filename == '.' || $filename == '..')
                continue;
                
            $filenames[] = $filename;
        }

        closedir($dh);

        if($order === 'asc') sort($filenames);
        else rsort($filenames);

        return $filenames;
    }
}