<?php
class Example_Form extends Zend_Form
{
    public function __construct($options = null)
    {
         parent::__construct($options);

        $this->setMethod('POST')
             ->setAction('')
             ->setName('example');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Pseudo')
                 ->setRequired(true)
                 ->addFilter('Alnum')
                 ->addFilter('StringToLower')
                 ->addValidator('NotEmpty');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
              ->setRequired(true)
              ->addFilter('StringToLower')
              ->addValidator('EmailAddress');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Envoi');

        $this->addElements(array(
            $username,
            $email,
            $submit
        ));
    }

    public function customize()
    {
            $this->clearDecorators();
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => '<ul>'))
                 ->addDecorator('Form');

            $this->setElementDecorators(array(
                array('ViewHelper'),
                array('Errors'),
                array('Description'),
                array('Label', array('separator'=>' ')),
                array('HtmlTag', array('tag' => 'li', 'class'=>'element-group')),
            ));

            $submit = $this->getElement('submit');

            // buttons do not need labels
            $submit->setDecorators(array(
                array('ViewHelper'),
                array('Description'),
                array('HtmlTag', array('tag' => 'li', 'class'=>'submit-group')),
            ));
    }
}