<?php

/**
 * @author: Reinier Gombert
 * @date: 12-dec-2016
 */
class FormInputs
{

    private $wLabel = 2;
    private $wInput = 10;
    private $questionNumber;
    private $aInputs;
    private $aHelpBlocks;
    private $btnReset;
    private $mandatoryNotification;

    public function __construct()
    {
        $this->aInputs = array();
        $this->aHelpBlocks = array();
        $this->btnReset = false;
        $this->questionNumber = 1;
        $this->mandatoryNotification = true;
    }

    /**
     * Set the width of the bootstrap column for the labels.
     * Together with InputWidth it should be a sum of 12.
     * 
     * @param int $val  Number between 1 & 12
     */
    public function setLabelWidth($val)
    {
        $this->wLabel = $val;
    }

    /**
     * Set the width of the bootstrap column for the inputs.
     * Together with LabelWidth it should be a sum of 12.
     * 
     * @param int $val  Number between 1 & 12
     */
    public function setInputWidth($val)
    {
        $this->wInput = $val;
    }

    public function disableMandatoryNotification()
    {
        $this->mandatoryNotification = false;
    }

    /**
     * Add a text input to the form
     * 
     * @param string $name  Name of the text input
     */
    public function addTextInput($name, $columnNameTable, $required = false, $type = "text")
    {
        $this->aInputs[] = array(
            'name' => $name,
            'tag' => $columnNameTable,
            'type' => 'text',
            'text' => '
                <label for="input' . $columnNameTable . '" class="col-lg-' . $this->wLabel . ' control-label">' . ($required ? '*' : '') . ' ' . $name . '</label>
                <div class="col-lg-' . $this->wInput . '">
                    <input type="' . $type . '" class="form-control" id="input-' . $columnNameTable . '" name="input-' . $columnNameTable . '" data-column="' . $columnNameTable . '" ' . ($required ? 'required' : '') . '>
                
            '); // missing </div> will be added later, in case there is a help-block
    }

    /**
     * Add a text input to the form
     * 
     * @param string $name  Name of the text input
     */
    public function addDateInput($name, $columnNameTable, $required = false)
    {
        $this->aInputs[] = array(
            'name' => $name,
            'tag' => $columnNameTable,
            'type' => 'date',
            'text' => '
                <label for="input' . $columnNameTable . '" class="col-lg-' . $this->wLabel . ' control-label">' . ($required ? '*' : '') . ' ' . $name . '</label>
                <div class="col-lg-' . $this->wInput . '">
                    <div class="input-group date datetimepicker">
                        <input type="text" class="form-control" id="input-' . $columnNameTable . '" name="input-' . $columnNameTable . '" data-column="' . $columnNameTable . '" placeholder="dd-mm-jjjj" ' . ($required ? 'required' : '') . '>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    
            '); // missing </div> will be added later, in case there is a help-block
    }

    /**
     * Add a textarea to the form
     * 
     * @param string $name
     */
    public function addTextArea($name, $columnNameTable, $required = false)
    {
        $this->aInputs[] = array(
            'name' => $name,
            'tag' => $columnNameTable,
            'type' => 'textarea',
            'text' => '
                <label for="input' . $columnNameTable . '" class="col-lg-' . $this->wLabel . ' control-label">' . ($required ? '*' : '') . ' ' . $name . '</label>
                <div class="col-lg-' . $this->wInput . '">
                    <textarea class="form-control" id="input-' . $columnNameTable . '" name="input-' . $columnNameTable . '" data-column="' . $columnNameTable . '" ' . ($required ? 'required' : '') . '></textarea>
                
            '); // missing </div> will be added later, in case there is a help-block
    }

    /**
     * Add a group of radio's to the form.
     * Radio's are always required
     * 
     * @param string $name      Name of the radioGroup
     * @param array $aValues    Values of the radio buttons
     */
    public function addRadioGroup($name, $columnNameTable, $aValues)
    {
        $radios = '';
        $i = 1;
        foreach ($aValues as $val)
        {
            $radios .= '
                <div class="radio-inline">
                    <label>
                        <input type="radio" name="radio-' . $columnNameTable . '" id="radio-' . $columnNameTable . '-' . $i++ . '" data-column="' . $columnNameTable . '" value="' . $val . '"  required> ' . $val . '
                    </label>
                </div>
            ';
        }

        $this->aInputs[] = array(
            'name' => $name,
            'tag' => $columnNameTable,
            'type' => 'radio',
            'text' => $radios
        );
    }

    /**
     * Add a Question with multiple choice answers to the form.
     * 
     * @param string $question  The question for the user. (will be auto-numbered)
     * @param array $aAnswers   Associative array in the format: AnswerID => Answer
     * @param int $optionSelected   The value of the answerID of the option to be pre-selected.
     */
    public function addMultipleChoiceQuestion($question, $aAnswers, $optionSelected = null)
    {
        $radios = '';
        $i = 1;
        foreach ($aAnswers as $answerID => $val)
        {
            $radios .= '
                <div class="radio">
                    <label>
                        <input type="radio" name="radio-PossibleAnswerID" id="radio-PossibleAnswerID-' . $i++ . '" data-column="PossibleAnswerID" value="' . $answerID . '"  required ' . ($optionSelected == $answerID ? 'checked' : '') . '> ' . $val . '
                    </label>
                </div>
            ';
        }

        $this->aInputs[] = array(
            'name' => $question,
            'tag' => "PossibleAnswerID",
            'type' => 'multiple-choice',
            'text' => $radios
        );
    }

    public function addOpenQuestion($question, $answer = "")
    {
        $this->aInputs[] = array(
            'name' => $question,
            'tag' => "Answer",
            'type' => 'open-question',
            'text' => '<input type="text" class="form-control" id="input-Answer" name="input-Answer" data-column="Answer" required value="' . $answer . '">'
        );
    }

    public function addButton($name, $text = "Verzenden", $class = "primary")
    {
        $this->aInputs[] = array(
            'name' => $name,
            'tag' => $name,
            'type' => 'button',
            'text' => '<button type="submit" class="btn btn-' . $class . '" id="button-' . $name . '" name="button-' . $name . '">' . $text . '</button>'
        );
    }

    public function addResetButton($text = "Reset")
    {
        $this->btnReset = $text;
    }

    public function addLegend($text)
    {
        $this->aInputs[] = array(
            'name' => 'Legend',
            'tag' => $text,
            'type' => 'legend',
            'text' => $text
        );
    }

    public function addHelpBlock($tag, $text)
    {
        $this->aHelpBlocks[$tag] = $text;
    }

    /**
     * Build an input
     * 
     * @param string $type  Type of the input
     * @param string $name  Name of the input. Used for the label and such.
     * @param string $text  Text of the input
     * @param string $tag   Tag of the input
     * @return string       The input, complete with label, names and values.
     */
    private function build($type, $name, $text, $tag)
    {
        if ($type === "text" || $type === "textarea" || $type === "date")
        {
            $return = '
                <div class="form-group">
                    ' . $text;
            // check for help-block for this input
            if (array_key_exists($tag, $this->aHelpBlocks))
            {
                $return .= '<span class="help-block">' . $this->aHelpBlocks[$tag] . '</span> ';
            }
            // add missing </div>, in case there is a help-block
            $return .= '
                    </div>
                </div>
            ';
        }
        else if ($type === "radio")
        {
            $return = '
                <div class="form-group">
                    <label class="col-lg-' . $this->wLabel . ' control-label">* ' . $name . '</label>
                    <div class="col-lg-' . $this->wInput . '">
                        ' . $text;
            // check for help-block for this input
            if (array_key_exists($tag, $this->aHelpBlocks))
            {
                $return .= '<span class="help-block">' . $this->aHelpBlocks[$tag] . '</span> ';
            }
            $return .= '
                    </div>
                </div>
            ';
        }
        else if ($type === "multiple-choice" || $type === "open-question")
        {
            $return = '
                <div class="form-group">
                    <label class="col-lg-' . $this->wLabel . ' control-label">' . $this->questionNumber++ . '.</label>
                    <div class="col-lg-' . $this->wInput . '"><label class="control-label">' . $name . '</label>
                        ' . $text;
            // check for help-block for this input
            if (array_key_exists($tag, $this->aHelpBlocks))
            {
                $return .= '<span class="help-block">' . $this->aHelpBlocks[$tag] . '</span> ';
            }
            $return .= '
                    </div>
                </div>
            ';
        }
        else if ($type === "button")
        {
            $return = '
                <div class="form-group">
                    <span class="col-lg-' . $this->wLabel . ' help-block text-right">' . ($this->mandatoryNotification ? '* = verplichte velden' : '') . '</span>
                    <div class="col-lg-' . $this->wInput . ' text-right">
                        ';
            if ($this->btnReset)
            {
                $return .= '<button type="reset" class="btn btn-default btnReset">' . $this->btnReset . '</button> ';
            }
            $return .= $text . '
                    </div>
                </div>
            ';
        }
        else if ($type === "legend")
        {
            $return = '<legend>' . $text . '</legend>';
        }

        return $return;
    }

    /**
     * Create a form body with all inputs supplied.
     * 
     * @return string
     */
    public function createFormBody()
    {
        $return = '';

        foreach ($this->aInputs as $input)
        {
            $return .= $this->build($input["type"], $input["name"], $input["text"], $input["tag"]);
        }

        return $return;
    }

}
