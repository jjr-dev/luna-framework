<?php
namespace Luna\Db;

class Migration {
    public static function create($dir, $name) {
        $time = floor(microtime(true) * 1000);
        $filename = $time . '-' . $name . '.php';

        file_put_contents(
            $dir . $filename, 
            self::arrayToText([
                '<?php',
                'use Illuminate\Support\Facades\Schema;',
                'use Illuminate\Database\Schema\Blueprint;',
                '',
                'return new class {',
                '    public function up() {',
                '        Schema::create("name", function (Blueprint $table) {',
                '            $table->increments("id");',
                '        });',
                '    }',
                '',
                '    public function down() {',
                '        Schema::dropIfExists("name");',
                '    }',
                '};'
            ])
        );
    }

    public static function arrayToText($arr) {
        return implode(PHP_EOL, $arr);
    }
}