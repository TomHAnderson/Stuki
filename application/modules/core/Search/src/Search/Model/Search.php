<?php
/**
 * This model is used to construct searches and manage the search index
 */
namespace Search\Model;

use Stuki\Model\Model,
    Zend\Search\Lucene,
    Zend\Search\Lucene\Search\Query,
    Zend\Search\Lucene\Search\QueryParser,
    Stuki\Search\Index
    ;

class Search extends Model
{
    private $query;
    private $index;
    private $sort;
    private $field = 'entity_key';

    public function __construct() {
//        parent::__construct();

        $this->query = new Query\Boolean();
    }

    public function setIndex(Lucene\Index $index) {
        $this->index = $index;
    }

    public function getIndex() {
        return $this->index;
    }

    /**
     * Set the field $keys[] indexes when returning
     * search results.  What field from the search index is returned?
     */
    public function setField($field) {
        $this->field = $field;
    }

    /**
     * Add a search term to the pending query
     *
     * Signs: true - required
              false - not required
              null - indifferent
     */
    public function addTerm($field, $value, $sign = null) {
        $this->query->addSubquery(
            new Query\Term(
                new Lucene\Index\Term($value, $field)
            ), $sign);

        return true;
    }

    public function addSort($field, $order = '', $type = '') {
        if (!$type) $type = SORT_REGULAR;
        if (!$order) $order = SORT_ASC;

        $this->sort[] = array(
            'field' => $field,
            'order' => $order,
            'type' => $type
        );

        return true;
    }

    // Add the string to the query
    public function addString($string) {
        $this->query->addSubquery(QueryParser::parse($string), true);

        return true;
    }

    /**
     * A debugging function to show exactly what's being sent to search
     */
    public function dump($string = '') {
        return $this->search($string, true);
    }

    public function search($string = '', $dump = false) {
        $index = $this->getIndex();

        if ($string) {
            $this->addString($string);
        }

        if ($dump) {
            print_r($this->query->rewrite($index)->getQueryTerms());
            die();
        }

        // If the search is too small it will throw an exception
        if ($this->sort) {
            $params[] = $this->query;
            foreach ($this->sort as $s) {
                $params[] = $s['field'];
                $params[] = $s['type'];
                $params[] = $s['order'];
            }
            $res = call_user_func_array(array($index, "find"), $params);
        } else {
            $res = $index->find($this->query);
        }

        // Build result array and save to cache
        $keys = array();
        foreach ($res as $hit) {
            $document = $hit->getDocument();
#            print_r($document->getFieldNames());

            $keys[] = $hit->{$this->field};
        }
        return $keys;
    }
}
