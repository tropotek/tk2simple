<?php
namespace Tk\Form\Event;



/**
 * Class Text
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Button extends Iface
{

    /**
     * @var string
     */
    protected $icon = '';

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function getHtml()
    {
        // TODO: Implement getHtml() method.
        $xhtml = <<<XHTML
<button type="submit" class="btn btn-sm" var="element"><i var="icon" choice="icon"></i> <span var="text">Submit</span></button>
XHTML;
        $t = \Dom\Loader::load($xhtml);
        
        if ($t->isParsed()) return '';

        if (!$t->keyExists('var', 'element')) {
            return '';
        }

        // Field name attribute
        //$t->setAttr('element', 'type', $this->getType());
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

        $t->insertText('text', $this->getLabel());
        if ($this->getIcon()) {
            $t->setChoice('icon');
            $t->addClass('icon', $this->getIcon());
        }
        
        return $t;
    }
}