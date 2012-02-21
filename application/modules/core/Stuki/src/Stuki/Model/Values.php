<?php
/**
 */

namespace Stuki\Model;

use Stuki\Model\Model as StukiModel;

class Values extends StukiModel {

    /**
     * Search all values and return all results
     */
    public function findBy($search) {
        $res = array();
        $res = array_merge($res, $this->getEm()->getRepository('Entities\ValuesArray')->findBy($search));
        $res = array_merge($res, $this->getEm()->getRepository('Entities\ValuesDatetime')->findBy($search));
        $res = array_merge($res, $this->getEm()->getRepository('Entities\ValuesDecimal')->findBy($search));
        $res = array_merge($res, $this->getEm()->getRepository('Entities\ValuesInteger')->findBy($search));
        $res = array_merge($res, $this->getEm()->getRepository('Entities\ValuesText')->findBy($search));
        $res = array_merge($res, $this->getEm()->getRepository('Entities\ValuesVarchar')->findBy($search));

        uasort($res, array($this, 'sortValues'));

        return $res;
    }

    /**
     * Search all values and return one result
     */
    public function findOneBy($search) {
        $res = $this->findBy($search);
        if (sizeof($res) > 1) throw new \Stuki\Exception('findOneBy returned more than one row');

        if ($res) return $res[0];

        return false;
    }

    /**
     * With a given entity, return the formatted display value
     * for a column
     */
    public function formatValue(\Stuki\Entity\Value $value) {
        $model = new $value->attribute->renderer->model('dummy');
        return new \Stuki\Model\Result(true, $model->formatValue($value->value));
    }

    /**
     * A function to sort values by their attribute's sort
     */
    private function sortValues($a, $b) {
        $aSort = $a->attribute->sort;
        $bSort = $b->attribute->sort;

        if ($aSort == $bSort) return 0;
        return ($aSort < $bSort) ? -1: 1;
    }

    public function insert($values) {
/*
        $user = new \Stuki\Entity\Users;
        foreach ($values as $key => $val) {
            $user->$key = $val;
        }
        $this->em->persist($user);
        $this->em->flush();

        return new \Stuki\Model\Result(true, $user);
*/
    }

    public function delete($key) {
/*
        if ($record = $this->wrapper->find($key)) {
            $this->em->remove($record);
            $this->em->flush();
        }

        return new \Stuki\Model\Result(true, true);
*/
    }

}