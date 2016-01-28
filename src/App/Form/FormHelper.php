<?php
/*
 * Created by PhpStorm.
 * User: mifsudm
 * Date: 8/18/15
 * Time: 2:20 PM
 */

namespace App\Form;

use \Tk\Form;
use \Tk\Form\Type;
use \Tk\Form\Field;


/**
 * Use this helper for purely inline rendered forms.
 *
 * This helper will create a from from the DOM template
 *
 * All types will be set as a \Tk\Form\Type\String, however the buttons will
 * be set as \Tk\Form\Type\Event objects
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 * 
 * @deprecated  Not sure we need this with the new form obnject.
 */
class FormHelper
{

    /**
     *
     *
     * @param \Dom\Form $domForm
     * @return Form
     * @throws \Tk\Form\Exception
     */
    static public function createForm(\Dom\Form $domForm)
    {
        $form = new Form($domForm->getId());

        foreach($domForm->getElementNames() as $name) {
            $el = $domForm->getFormElement($name);
            if (!$el->getName()) {
                throw new \Tk\Form\Exception('Type does not have a name attribute. Please check your Form HTML.');
            }
            if (strtolower($el->getType()) == 'submit') {
                $form->addField(new Field\Button($el->getName()));
            } else if (strtolower($el->getType()) == 'file') {
                $form->addField(new Field\File($el->getName()));
            } else {
                $form->addField(new Field\Input($el->getName()));
            }
        }

        return $form;
    }

    
    static function modelToArray($model)
    {
        
    }
    
    
    
    
}