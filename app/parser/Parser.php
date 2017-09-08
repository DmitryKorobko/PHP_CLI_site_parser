<?php

namespace liw\app\parser\Parser;

require __DIR__ . '/../../vendor/autoload.php';

use liw\app\actions\Actions;
/**
 * Created by PhpStorm.
 * User: steinmann
 * Date: 05.09.17
 * Time: 12:06
 */

system("clear");

echo "███──███──███──███───────████──████──████──███──███──████ \n";
echo "─█───█────█─────█────────█──█──█──█──█──█──█────█────█──█ \n";
echo "─█───███──███───█───███──████──████──████──███──███──████ \n";
echo "─█───█──────█───█────────█─────█──█──█─█─────█──█────█─█ \n";
echo "─█───███──███───█────────█─────█──█──█──█──███──███──█──█ \n\n";
echo "███─███─████─████─███─███─████─███ \n";
echo "█───█───█──█─█──█─█────█──█──█──█──█ \n";
echo "███─███─████─████─███──█──████──█ \n";
echo "──█─█───█─█──█──────█──█──█──█──█──█ \n";
echo "███─███─█──█─█────███──█──█──█──█ \n\n";


echo "Please enter your command(or use command 'help' to view all commands): \n";
$userAction = readline();

switch ($userAction) {
    case 'parse':
        $do = new Actions();
        $do->getParse();
        echo "Error of params, please enter correct URL or use command 'help'\n";
        break;
    case 'report':
        $do = new Actions();
        $do->getReport();
        echo "Error of params, please enter correct domain or use command 'help'\n";
        break;
    case 'help':
        $do = new Actions();
        $do ->getHelp();
        break;
    default:
        echo "Error of command, please use command 'help' to view all commands \n";
        break;
}

echo "\n";
