<?php
namespace App\Db;

use Tk\Exception;
use \Tk\Db\Tool;
use \Tk\Db\Map\Model;
use \Tk\Db\Map\Mapper;
use \Tk\Db\Map\ArrayObject;


/**
 * Class UserMapper
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SupervisorMap extends Mapper
{
    /**
     * Map the data from a DB row to the required object
     *
     * Input: array (
     *   'tblColumn' => 'columnValue'
     * )
     *
     * Output: Should return an \stdClass or \Tk\Model object
     *
     * @param array $row
     * @param null|mixed $obj If null then \stdClass will be returned
     * @return \stdClass|\Tk\Db\Map\Model
     * @since 2.0.0
     */
    public function map($row, $obj = null)
    {
        $obj = new Supervisor();
        $obj->id = $row['id'];
        $obj->courseId = $row['courseId'];
        $obj->title = $row['title'];
        $obj->firstName = $row['firstName'];
        $obj->lastName = $row['lastName'];
        $obj->graduationYear = $row['graduationYear'];
        $obj->status = $row['status'];
        $obj->private = ($row['private'] == 1);
        if ($row['modified'])
            $obj->modified = new \DateTime($row['modified']);
        if ($row['created'])
            $obj->created = new \DateTime($row['created']);

        return $obj;
    }

    /**
     * Un-map an object to an array ready for DB insertion.
     * All fields and types must match the required DB types.
     *
     * Input: This requires a \Tk\Db\Map\Model or \stdClass object as input
     *
     * Output: array (
     *   'tblColumn' => 'columnValue'
     * )
     *
     * @param \Tk\Db\Map\Model|\stdClass $obj
     * @param array $array
     * @return array
     * @since 2.0.0
     */
    public function unmap($obj, $array = array())
    {
        $arr = array(
            'id' => $obj->id,
            'courseId' => $obj->courseId,
            'title' => $obj->title,
            'firstName' => $obj->firstName,
            'lastName' => $obj->lastName,
            'graduationYear' => $obj->graduationYear,
            'status' => $obj->status,
            'private' => (int)$obj->private,
            'modified' => $obj->modified->format('Y-m-d H:i:s'),
            'created' => $obj->created->format('Y-m-d H:i:s')
        );
        return $arr;
    }

    /**
     * @param array|\stdClass|Model $row
     * @return Supervisor
     */
    static function mapForm($row, $obj = null)
    {
        if (!$obj) {
            $obj = new Supervisor();
        }
        //$obj->id = $row['id'];
        if (isset($row['courseId']))
            $obj->courseId = $row['courseId'];
        if (isset($row['title']))
            $obj->title = $row['title'];
        if (isset($row['firstName']))
            $obj->firstName = $row['firstName'];
        if (isset($row['lastName']))
            $obj->lastName = $row['lastName'];
        if (isset($row['graduationYear']))
            $obj->graduationYear = $row['graduationYear'];
        if (isset($row['status']))
            $obj->status = $row['status'];
        if (isset($row['private']))
            $obj->private = ($row['private'] == 'private');

        if (isset($row['modified']))
            $obj->modified = new \DateTime($row['modified']);
        if (isset($row['created']))
            $obj->created = new \DateTime($row['created']);

        return $obj;
    }


    /**
     * @param int $courseId
     * @param Tool $tool
     * @return ArrayObject
     */
    public function findByCourseId($courseId, $tool = null)
    {
        return $this->findFiltered(array('courseId' => $courseId), $tool);
    }

    /**
     * Find filtered records
     *
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        $this->setAlias('a');

        $from = sprintf('%s %s, %s b ', $this->getDb()->quoteParameter($this->getTable()), $this->getAlias(), $this->getDb()->quoteParameter('user'));
        $where = '';
        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.%s LIKE %s OR ', $this->getDb()->quoteParameter('firstName'), $this->getDb()->quote($kw));
            $w .= sprintf('a.%s LIKE %s OR ', $this->getDb()->quoteParameter('lastName'), $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) {
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['status'])) {
            if (!is_array($filter['status'])) {
                $filter['status'] = array($filter['status']);
            }
            $statusStr = '';
            foreach ($filter['status'] as $s) {
                if (!trim($s)) continue;
                $statusStr .= sprintf('a.status =  %s OR ', $this->getDb()->quote($s));
            }
            if ($statusStr) {
                $where .= '(' . substr($statusStr, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['firstName'])) {
            $where .= sprintf('a.%s = %s AND ', $this->getDb()->quoteParameter('firstName'), $this->getDb()->quote($filter['firstName']));
        }

        if (!empty($filter['courseId'])) {
            $where .= sprintf('a.%s = %s AND ', $this->getDb()->quoteParameter('courseId'), (int)$filter['courseId']);
        }

        if (!empty($filter['created'])) {
            $where .= sprintf('a.created > %s AND ', $this->getDb()->quote($filter['created']));
        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        return $this->selectFrom($from, $where, $tool);
    }

}