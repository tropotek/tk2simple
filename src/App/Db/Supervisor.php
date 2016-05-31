<?php
namespace App\Db;

/**
 * Class
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Supervisor extends \Tk\Db\Map\Model
{
    /**
     * @var int
     */
    public $id = 0;
    /**
     * @var int
     */
    public $courseId = 0;

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $firstName = '';

    /**
     * @var string
     */
    public $lastName = '';

    /**
     * @var string
     */
    public $graduationYear = '';

    /**
     * @var string
     */
    public $status = '';

    /**
     * @var bool
     */
    public $private = true;

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;



}