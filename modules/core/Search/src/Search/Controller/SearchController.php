<?php

namespace Search\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Search\Lucene
;

class SearchController extends ActionController
{
    public function indexAction() {
        $search = $this->getLocator()->get('modelSearch');
        $index = $this->getLocator()->get('modelSearchIndex');
        $entities = $this->getLocator()->get('modelEntities');
        $search->setIndex($index->getIndex());

        $terms = $this->getRequest()->query()->get('terms');

        $error = '';
        $results = array();
        try {
            if ($terms) {
                foreach ($search->search($terms) as $entity_key) {
                    $results[] = $entities->find($entity_key);
                }
            }
        } catch (Lucene\Exception $e) {
            $error = $e->getMessage();
        }

        return array(
            'terms' => $terms,
            'entities' => $results,
            'searchError' => $error
        );
    }

    public function reindexAction() {
        $modelQueue = $this->getLocator()->get('modelQueue');
        $queue = $modelQueue->getQueue();
        $queue->send('REINDEXALL');
    }

    public function optimizeAction() {
        $modelQueue = $this->getLocator()->get('modelQueue');
        $queue = $modelQueue->getQueue();
        $queue->send('OPTIMIZE');
    }
}
