<?php
namespace App\Db;

/**
 * Class UserMapper
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SupervisorMap extends \Tk\Db\Mapper
{


    public function unmap($obj)
    {
        $arr = array(
            'id' => $obj->id,
            'courseId' => $obj->courseId,
            'title' => $obj->title,
            'firstName' => $obj->firstName,
            'lastName' => $obj->lastName,
            'graduationYear' => $obj->graduationYear,
            'private' => (int)$obj->private,
            'modified' => $obj->modified->format('Y-m-d H:i:s'),
            'created' => $obj->created->format('Y-m-d H:i:s')
        );
        return $arr;
    }


    public function map($row)
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
     * @param int $courseId
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findByCourseId($courseId, $tool = null)
    {
        return $this->select(array('courseId' => $courseId));
    }


    /**
     * Find filtered records
     *
     * @param array $filter
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        $this->setAlias('a');

        $from = sprintf('`%s` %s, `user` b ', $this->getTable(), $this->getAlias());
        $where = '';
        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.`firstName` LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('a.`lastName` LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.`id` = %d OR ', $id);
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
                $statusStr .= sprintf('a.`status` =  %s OR ', $this->getDb()->quote($s));
            }
            if ($statusStr) {
                $where .= '(' . substr($statusStr, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['firstName'])) {
            $where .= sprintf('a.`firstName` = %s AND ', $this->getDb()->quote($filter['firstName']));
        }

        if (!empty($filter['courseId'])) {
            $where .= sprintf('a.`courseId` = %s AND ', (int)$filter['courseId']);
        }

        if (!empty($filter['created'])) {
            $where .= sprintf('a.`created` > %s AND ', $this->getDb()->quote($filter['created']));
        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        return $this->selectFrom($from, $where, $tool);
    }

}