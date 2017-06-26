<?php
/**
 * @author Yevgeny Shemchik (schemtschik@gmail.com)
 */

/*
 * Обрабатывает результаты по правилам пентагона.
 * Для корректной работы необходимо определять переменную round номером раунда в 1-индексации (от 1 до 5)
 * Все баллы, высчитанные по правилам пентагона умножаются на стоимость подзадачи
 */

$calculate = function($answers, $quiz) {
    $res = array();
    for ($questionId = 0; $questionId < count($answers); $questionId++) {
        foreach ($answers[$questionId] as $answer) {
            for ($i = 0; $i < count($answer->results); $i++) {
                if (!isset($res[$answer->team]))
                    $res[$answer->team] = 0;
                if ($answer->results[$i] == 0)
                    $res[$answer->team] -= $quiz->questions[$questionId]->scores[$i]->score;
                else
                    $res[$answer->team] += (6 - $answer->round) * $quiz->questions[$questionId]->scores[$i]->score;
            }
        }
    }
    return $res;
};