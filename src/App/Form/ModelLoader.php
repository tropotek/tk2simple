<?php
namespace App\Form;

use \Tk\Form;
use \Tk\Db;
use \Tk\Db\Model;

/**
 * Class LoadObject
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ModelLoader
{

    /**
     *
     * @param Form $form
     * @param Model $model
     * @return Model
     * @throws \Tk\Form\Exception
     */
    static public function loadObject($form, $model)
    {
        if (!$model instanceof Model) {
            throw new \Tk\Form\Exception('Invalid object type for parameter.');
        }
        /* @var $element \Tk\Form\Type\Iface */
        foreach ($form->getFieldList() as $name => $element) {
            if (!$name) continue;
            $val = $element->getValue();
            if ($element->isArray() && is_Array($val)) {
                foreach ($val as $n => $v) {
                    if (property_exists($model, $n)) {
                        $model->$n = $v;
                    }
                }
            } else {
                if (property_exists($model, $name)) {
                    $model->$name = $val;
                }
            }
        }
        return $model;
    }


}