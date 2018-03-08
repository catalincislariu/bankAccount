<?php

namespace AppBundle\Services;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class Utils
 * @package AppBundle\Services
 */
class Utils
{
    /**
     * Utils constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $helper
     * @param $questionData
     * @param $input
     * @param $output
     * @return string
     */
    public function getUserInput($helper, $questionData, $input, $output) : string
    {
        if(is_array($questionData)) {
            $question = new ChoiceQuestion(
                $questionData["0"],
                $questionData["1"],
                $questionData["2"]
            );
            $question->setErrorMessage("Number %s is invalid.");
        } else {
            $question = new Question($questionData);
        }

        $number = $helper->ask($input, $output, $question);

        return $number;
    }
}