<?php

/**
 * @author: Reinier Gombert
 * @date: 12-dec-2016
 */
class FormInputs
{

    private $wLabel = 2;
    private $wInput = 10;
    private $aInputs;
    private $btnReset;

    public function __construct()
    {
        $this->aInputs = array();
        $this->btnReset = false;
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

    /**
     * Add a text input to the form
     * 
     * @param string $name  Name of the text input
     */
    public function addTextInput($name, $columnNameTable, $type = "text")
    {
        $this->aInputs[] = array(
            'name' => $name,
            'type' => 'text',
            'text' => '
                <label for="input' . $name . '" class="col-lg-' . $this->wLabel . ' control-label">' . $name . '</label>
                <div class="col-lg-' . $this->wInput . '">
                    <input type="' . $type . '" class="form-control" id="input-' . $name . '" name="input-' . $name . '" data-column="' . $columnNameTable . '" placeholder="' . $name . '" required>
                </div>
            ');
    }

    /**
     * Add a textarea to the form
     * 
     * @param string $name
     */
    public function addTextArea($name, $columnNameTable)
    {
        $this->aInputs[] = array(
            'name' => $name,
            'type' => 'textarea',
            'text' => '
                <label for="input' . $name . '" class="col-lg-' . $this->wLabel . ' control-label">' . $name . '</label>
                <div class="col-lg-' . $this->wInput . '">
                    <textarea class="form-control" id="input-' . $name . '" name="input-' . $name . '" data-column="' . $columnNameTable . '" placeholder="' . $name . '" required></textarea>
                </div>
            ');
    }

    /**
     * Add a group of radio's to the form
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
                <div class="radio">
                    <label>
                        <input type="radio" name="radio-' . $name . '" id="radio-' . $name . '-' . $i++ . '" data-column="' . $columnNameTable . '" value="' . $val . '" required> ' . $val . '
                    </label>
                </div>
            ';
        }

        $this->aInputs[] = array(
            'name' => $name,
            'type' => 'radio',
            'text' => $radios
        );
    }

    public function addButton($name, $text = "Verzenden", $class = "primary")
    {
        $this->aInputs[] = array(
            'name' => $name,
            'type' => 'button',
            'text' => '<button type="submit" class="btn btn-' . $class . '" id="button-' . $name . '" name="button-' . $name . '">' . $text . '</button>
        ');
    }

    public function addResetButton($text = "Reset")
    {
        $this->btnReset = $text;
    }

    /**
     * Build an input
     * 
     * @param string $type  Type of the input
     * @param string $name  Name of the input. Used for the label and such.
     * @param string $text  Text of the input
     * @return string       The input, complete with label, names and values.
     */
    private function build($type, $name, $text)
    {
        if ($type === "text" || $type === "textarea")
        {
            $return = '
                <div class="form-group">
                    ' . $text . '
                </div>
            ';
        }
        else if ($type === "radio")
        {
            $return = '
                <div class="form-group">
                    <label class="col-lg-' . $this->wLabel . ' control-label">' . $name . '</label>
                    <div class="col-lg-' . $this->wInput . '">
                        ' . $text . '
                    </div>
                </div>
            ';
        }
        else if ($type === "button")
        {
            $return = '
                <div class="form-group">
                    <div class="col-lg-' . $this->wInput . ' col-lg-offset-' . $this->wLabel . ' text-right">
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
            $return .= $this->build($input["type"], $input["name"], $input["text"]);
        }

        return $return;
    }

}
