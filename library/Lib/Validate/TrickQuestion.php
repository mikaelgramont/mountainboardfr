<?php
class Lib_Validate_TrickQuestion extends Zend_Validate_Abstract implements Zend_Validate_Interface
{
	protected $_answer = 'slide';

	const WRONGANSWER = 'wrongAnswer';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::WRONGANSWER => "wrongAnswer"
    );
	
	public function __construct($answer)
	{
		$this->_answer = $answer;
	}
	
	public function isValid($value)
	{
		if($value !== $this->_answer){
			$this->_error(self::WRONGANSWER);
			return false;
		}
		
		return true;
	}
}