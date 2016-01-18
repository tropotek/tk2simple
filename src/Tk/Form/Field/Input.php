<?php
namespace Tk\Form\Field;


/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Input extends Iface
{
    
    private $type = 'text';


    /**
     * Set the input type value
     * 
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() 
    {
        return $this->type;
    }
    

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        $xhtml = <<<XHTML
<input type="text" var="element"/>
XHTML;
        $t = \Dom\Loader::load($xhtml);
        
        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        $t->setAttr('element', 'type', $this->getType());
        $t->setAttr('element', 'name', $this->getName());

        // All other attributes
        foreach($this->getAttrList() as $key => $val) {
            if ($val == '' || $val == null) {
                $val = $key;
            }
            $t->setAttr('element', $key, $val);
        }

        // Element css class names
        foreach($this->getCssList() as $v) {
            $t->addClass('element', $v);
        }

        if ($this->isRequired()) {
            $t->setAttr('element', 'required', 'required');
        }

        // set the field value
        if ($t->getVarElement('element')->nodeName == 'input' ) {
            $value = $this->getValue();
            if ($value && !is_array($value)) {
                $t->setAttr('element', 'value', $value);
            }
        }
        
        
        return $t;
    }
    
    
}