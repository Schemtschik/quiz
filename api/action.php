<?php
/**
 * @author Yevgeny Shemchik (schemtschik@gmail.com)
 */

error_reporting(E_ERROR);

function get() {
    $res = json_decode("{}");
    $nextPlayer = 0;
    $nextCompetition = 0;

    $res->competitions = array();
    $list = scandir("../data/competitions");
    foreach ($list as $i)
        if ($i != "." && $i != "..") {
            $res->competitions[] = json_decode(file_get_contents('../data/competitions/'. $i . '/competition.json'));
            $nextCompetition = max($nextCompetition, (int)$i);
        }

    $res->players = json_decode(file_get_contents("../data/players.json"));
    foreach ($res->players as $i)
        $nextPlayer = max($nextPlayer, (int)$i->id);

    $res->nextPlayer = $nextPlayer + 1;
    $res->nextCompetition = $nextCompetition + 1;
    $res->status = "ok";
    die(json_encode($res));
}

function save() {

    $data = json_decode($_GET['data']);

    if (isset($_GET['competition'])) {
        if (isset($_GET['quiz'])) {
            $competition = json_decode(file_get_contents("../data/competitions/" . $_GET['competition'] . "/competition.json"));
            if (isset($_GET['question'])) {
                $competition->quizzes[$_GET['quiz']]->questions[$_GET['question']] = $data->competitions[$_GET['competition']]->quizzes[$_GET['quiz']]->questions[$_GET['question']];
            } else {
                $competition->quizzes[$_GET['quiz']] = $data->competitions[$_GET['competition']]->quizzes[$_GET['quiz']];
            }
            file_put_contents("../data/competitions/" . $_GET['competition'] . "/competition.json", json_encode($competition));
        } else {
            if (!file_exists("../data/competitions/" . $_GET['competition'] . "/competition.json")) {
                mkdir("../data/competitions/" . $_GET['competition']);
                mkdir("../data/competitions/" .  $_GET['competition'] . "/media");
            }
            file_put_contents("../data/competitions/" . $_GET['competition'] . "/competition.json", json_encode($data->competitions[$_GET['competition']]));
        }
    } else {
        file_put_contents("../data/players.json", json_encode($data->players));

        for ($i = 0; $i < count($data->competitions); $i++) {
            if (!file_exists("../data/competitions/" .$data->competitions[$i]->id  . "/competition.json")) {
                mkdir("../data/competitions/" . $data->competitions[$i]->id);
                mkdir("../data/competitions/" . $data->competitions[$i]->id . "/media");
            }
            file_put_contents("../data/competitions/" . $data->competitions[$i]->id . "/competition.json", json_encode($data->competitions[$i]));
        }
    }

    die('{"status":"ok"}');
}

function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
        foreach($objs as $obj) {
            is_dir($obj) ? removeDirectory($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}

function delete() {
    if (isset($_GET['competition'])) {
        if (isset($_GET['quiz'])) {
            $competition = json_decode(file_get_contents("../data/competitions/" . $_GET['competition'] . "/competition.json"));
            if (isset($_GET['question'])) {
                array_splice($competition->quizzes[$_GET['quiz']]->questions, $_GET['question'], 1);
            } else {
                array_splice($competition->quizzes, $_GET['quiz'], 1);
            }
            file_put_contents("../data/competitions/" . $_GET['competition'] . "/competition.json", json_encode($competition));
        } else {
            removeDirectory("../data/competitions/" . $_GET['competition']);
        }
        die ('{"status":"ok"}');
    } else
        throw new Exception("No arguments");
}

function getFiles() {
    if (!isset($_GET['competition']))
        die ('{"status":"error", "message":"No arguments"}');

    $res = json_decode("{}");
    $res->status = "ok";
    $res->files = array();
    $list = scandir("../data/competitions/" . $_GET['competition'] . "/media");
    foreach ($list as $i)
        if ($i != "." && $i != "..")
            $res->files[] = $i;
    die(json_encode($res));
}

function getValuers() {
    $res = json_decode("{}");
    $res->status = "ok";
    $res->valuers = array();
    $list = scandir("../valuers");
    foreach ($list as $i)
        if ($i != "." && $i != "..")
            $res->valuers[] = $i;
    die(json_encode($res));
}

function addAnswer() {
    if (!isset($_GET['competition']) || !isset($_GET['quiz']) || !isset($_GET['question']) || !isset($_GET['data']))
        throw new Exception("No arguments");

    $filename = "../data/competitions/" . $_GET['competition'] . "/results.json";
    $results = (file_exists($filename) ? json_decode(file_get_contents($filename)) : array());
    for ($i = 0; $i <= (int)$_GET['quiz']; $i++)
        if (!isset($results[$i]))
            $results[$i] = array();
    for ($i = 0; $i <= (int)$_GET['question']; $i++)
        if (!isset($results[$_GET['quiz']][$i]))
            $results[$_GET['quiz']][$i] = array();
    $results[(int)$_GET['quiz']][(int)$_GET['question']][] = json_decode($_GET['data']);
    file_put_contents($filename, json_encode($results));
}

function getResults() {
    if (!isset($_GET['competition']))
        throw new Exception("No arguments");
    $filename = "../data/competitions/" . $_GET['competition'] . "/results.json";
    die(file_exists($filename) ? file_get_contents($filename) : "[]");
}

function getTable() {
    if (!isset($_GET['competition']))
        throw new Exception("No arguments");

    $filename = "../data/competitions/" . $_GET['competition'] . "/results.json";
    $results = json_decode(file_exists($filename) ? file_get_contents($filename) : "[]");
    $competition = json_decode(file_get_contents("../data/competitions/" . $_GET['competition'] . "/competition.json"));

    $scores = array();
    foreach ($competition->quizzes as $quiz) {
        foreach ($competition->teams as $team)
            if (!isset($scores[$team->id]))
                $scores[$team->id] = array();
            if (!isset($scores[$team->id][$quiz->id]))
                $scores[$team->id][$quiz->id] = 0;

        if (isset($results[$quiz->id])) {
            include "../valuers/" . $quiz->valuer;
            $tmp = $calculate($results[$quiz->id], $quiz);
            unset($calculate);

            foreach ($tmp as $teamId => $score)
                $scores[$teamId][$quiz->id] = $score;
        }
    }

    $res = json_decode("{}");
    $res->status = "ok";
    $res->lines = array();
    foreach ($competition->teams as $team) {
        $line = array();
        $line[] = $team->name;
        $sum = 0;
        foreach ($competition->quizzes as $quiz) {
            $sum += (isset($scores[$team->id][$quiz->id]) ? $scores[$team->id][$quiz->id] : 0);
            $line[] = (isset($scores[$team->id][$quiz->id]) ? $scores[$team->id][$quiz->id] : 0);
        }
        $line[] = $sum;
        $res->lines[] = $line;
    }

    die(json_encode($res));
}

if (isset($_GET['q'])) {
    $q = $_GET['q'];

    try {
        if (function_exists($q))
            $q();
        else
            throw new Exception('Unknown method');

    } catch (Exception $e) {
        die('{"status":"error", "message":"' . $e->getMessage() . '"}');
    }
    echo '{"status":"ok"}';
} else {
    echo '{"status":"error", "message":"Empty method"}';
}


?>