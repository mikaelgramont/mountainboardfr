<?php
class Lib_Form_Element_Multiselect extends Zend_Form_Element_Multiselect
{
    /**
     * Translate an array of boolean values into a pseudo-bitfield data
     *
     * @param string $value
     * @return string
     */
    public function getValueFromDatabase($value)
    {
        if(empty($value)){
            return array();
        }

        $return = array();

        $length = strlen($value);
        for($i = 1; $i <= $length; $i++){
            $char = $value[$i - 1];

            if($char === '1'){
                $return[] = $i;
            }
        }

        return $return;
    }

     /**
     * Translate a pseudo-bitfield data into an array of boolean values
     *
     * @param string $value
     * @return string
     */
   public function getFormattedValueForDatabase($value)
    {
        if(empty($value)){
            return '';
        }

        if(!is_array($value)){
            return '';
        }

        $array = array();

        foreach($this->options as $index => $option){
        	// Just so code editors won't complain $option was never used:
        	$option = $option;
            if(in_array($index, $value)){
                $array[$index] = 1;
            } else {
                $array[$index] = 0;
            }
        }
        $return = implode('', $array);
        return $return;
    }
}