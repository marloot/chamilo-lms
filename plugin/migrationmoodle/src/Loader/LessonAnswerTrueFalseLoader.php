<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\PluginBundle\MigrationMoodle\Loader;

use Chamilo\PluginBundle\MigrationMoodle\Interfaces\LoaderInterface;

/**
 * Class LessonAnswerTrueFalseLoader.
 *
 * Loader for True-False answers from lesson pages.
 *
 * @package Chamilo\PluginBundle\MigrationMoodle\Loader
 */
class LessonAnswerTrueFalseLoader implements LoaderInterface
{
    /**
     * Load the data and return the ID inserted.
     *
     * @param array $incomingData
     *
     * @return int
     */
    public function load(array $incomingData)
    {
        $courseInfo = api_get_course_info_by_id($incomingData['c_id']);

        $exercise = new \Exercise($incomingData['c_id']);
        $exercise->read($incomingData['quiz_id']);

        $question = \Question::read($incomingData['question_id'], $courseInfo);

        $answer = new \Answer($incomingData['question_id'], $incomingData['c_id'], $exercise);

        $isGoodAnswer = !empty($incomingData['score']);

        if ($isGoodAnswer) {
            $incomingData['score'] = abs($incomingData['score']);

            if ($incomingData['score'] > 0) {
                $question->weighting += $incomingData['score'];
            }
        }

        $answer->createAnswer(
            $incomingData['answer'],
            $isGoodAnswer,
            $incomingData['feedback'],
            $incomingData['score'],
            $question->countAnswers() + 1,
            null,
            null,
            '0@@0@@0@@0'
        );
        $answer->save();
        $question->save($exercise);

        return $question->id;
    }
}
