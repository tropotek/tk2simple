<?php
namespace Tk\Form\Field;


class Checkbox extends Input
{

    
    
    
    /**
     * __construct
     *
     * @param string $name
     * @throws Exception
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setType('checkbox');
    }



    /**
     * Is the value checked
     *
     * @return bool
     */
    public function isSelected($val = '')
    {
        $arr = $this->getValue();
        if (!empty($arr[$this->getName()]) && $arr[$this->getName()] == $this->getName()) {
            return true;
        }
        return false;
    }
    

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $t = parent::getHtml();
        
        if ($this->isSelected()) {
            $t->setAttr('element', 'checked', 'checked');
        }
        $t->setAttr('element', 'value', $this->getName());
        
        return $t;
    }
    
    
    
}