<?php


namespace App\Model;

class Card
{
    public $value;
    public $type;
    public $labeledValue;


    public function __construct($value,$type,$labeledValue)
    {
        $this->value = $value;
        $this->type = $type;
        $this->labeledValue = $labeledValue;
    }

}