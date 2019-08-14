<?php

class Smilla_PostChShippingLabel_Model_Source_Codeoptions
{
  public function toOptionArray()
  {
    return array(
      array('value' => 'ECO', 'label' =>'PostPac Economy'),
      array('value' => 'PRI', 'label' =>'PostPac Priority'),
      array('value' => 'A7', 'label' =>'A7'),
    );
  }
}