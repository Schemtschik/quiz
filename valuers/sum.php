<?php
/**
 * @author Yevgeny Shemchik (schemtschik@gmail.com)
 */

/*
 * Суммирует все баллы за правильные ответы в соответствии с разбалловкой
 * Для каждой пары "команда-вопрос" учитывает все записи
 */

$calculate = function($answers, $quiz) {
    $res = array();
    for ($questionId = 0; $questionId < count($answers); $questionId++) {
        foreach ($answers[$questionId] as $answer) {
            for ($i = 0; $i < count($answer->results); $i++) {
                if (!isset($res[$answer->team]))
                    $res[$answer->team] = 0;
                $res[$answer->team] += $answer->results[$i] * $quiz->questions[$questionId]->scores[$i]->score;
            }
        }
    }
    return $res;
};