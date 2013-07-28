<?php
class Lib_Form_Decorator_CustomErrors extends Zend_Form_Decorator_Abstract
{
    /**
     * Render errors
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $errors = $element->getMessages();

        $i = 0;
        foreach($errors as &$error){
            if($i == 0){
                $error = ucfirst($error);
            }
            $i++;

            if($i < count($errors)){
                $error .= ', ';
            }
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();

        $viewHelper = $view->getHelper('FormErrors');
        $viewHelper->setElementStart('<span%s>')
                   ->setElementEnd('</span>')
                   ->setElementSeparator(' ');

        $this->_options['id'] = $element->getId().'Error';

        $errors = $viewHelper->formErrors($errors, $this->getOptions());

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $errors;
            default:
            case self::PREPEND:
                return $errors . $separator . $content;
        }
    }
}
