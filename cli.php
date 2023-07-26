<?php
    require __DIR__ . '/vendor/autoload.php';
    
    use App\Utils\Update;
    
    if (php_sapi_name() !== 'cli')
        die("Este arquivo só pode ser executado a partir da linha de comando.");

    $action = $argv[1];

    unset($argv[0]);
    unset($argv[1]);

    $args = $argv;

    switch($action) {
        case 'update':
            if(in_array('-v', $args)) {
                if(count($args) > 1)
                    die('Quantidade de argumentos inválida');

                die('Versão atual: ' . Update::getVersion());
            } elseif(in_array('-h', $args)) {
                if(!Update::has()) die('Versão mais recente já em uso');
                
                die('Versão atual desatualizada');
            } elseif(in_array('-u', $args)) {
                if(!Update::has()) die('Versão mais recente já em uso');
                if(!Update::do()) die('Erro ao atualizar versão');

                die('Versão atualizada para X');
            } else
                die('Argumentos inválidos');
                
            break;
        case 'help':
            die('Consulte a documentação oficial em https://github.com/jjr-dev/luna');
            break;
        default:
            die("Ação inválida, tente: php cli.php help");
            break;
    }