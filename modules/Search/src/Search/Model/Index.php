<?php

/**
 * This model is used to construct searches and manage the search index
 */
namespace Search\Model;

use Stuki\Model\Model,
    Zend\Search\Lucene\Search\QueryParser,
    Zend\Search\Lucene\Analysis\Analyzer,
    Zend\Search\Lucene\Analysis\Analyzer\Common\Utf8Num,
    Zend\Search\Lucene\Search\Query\Boolean,
    Zend\Search\Lucene\Analysis\Analyzer\Analyzer as DefaultAnalyzer,
    Zend\Search\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive as Utf8NumCaseInsensitive,
    Zend\Search\Lucene\Lucene as LuceneBase,
    Zend\Search\Lucene,
    Zend\Search\Lucene\Document,
    Stuki\Renderer\Parameters
    ;

class Index extends Model
{

    private $index;

    /**
     * Open or create the search index
     */
    public function getIndex() {
        if (!$this->index) {
            try {
                $this->index = LuceneBase::open(APPLICATION_PATH . '/data/Search');
            } catch (\Exception $e) {
                $this->index = LuceneBase::create(APPLICATION_PATH . '/data/Search');
            }
        }

        QueryParser::setDefaultEncoding("utf-8");
        DefaultAnalyzer::setDefault(new Utf8NumCaseInsensitive());


        return $this->index;
    }

    /**
     * Index or re-index an EAV entity
     */
    public function index(\Entities\Entities $entity) {
        $this->delete($entity);
        $this->insert($entity);

        return true;
    }

    /**
     * Optimize the search index
     */
    public function optimize() {
        $index = $this->getIndex();
        $index->optimize();

        return true;
    }

    public function delete($entity) {
        $index = $this->getIndex();
        $query = new Boolean();

        /**
         * The sign is specified as:
         *     TRUE  - subquery is required
         *     FALSE - subquery is prohibited
         *     NULL  - subquery is neither prohibited, nor required
         */
        $sign = null;
        $query->addSubquery(
            new Lucene\Search\Query\Term(
                new Lucene\Index\Term($entity->getKey(), 'entity_key')
            ), $sign);

        $res = $index->find($query);
        foreach ($res as $hit) {
            // hit->id is the internal lucene document number
            $index->delete($hit->id);
        }

        return true;
    }

    public function insert(\Entities\Entities $entity) {
        if ($doc = $this->buildDocument($entity)) {
            $this->getIndex()->addDocument($doc);
        }

/*
        // Optimize if we have too many files
        $dir = dir(APPLICATION\PATH . '/../Data/search');
        while (false !== ($dir->read())) {
            $file\count ++;
        }
        if ($file\count > 200) {
            $adapter = new \Stuki\Queue\Adapter\Doctrine2($options = array());
            $queue = new \Zend\Queue($adapter, array('name' => 'Work Queue'));
            $queue->send("OPTIMIZE:0:0");
        }
*/
        return true;
    }

    /**
     * Build a search document
     */
    public function buildDocument(\Entities\Entities $entity, Lucene\Document $doc = null) {
        static $fields;
        $initialRun = false;

        $index = $this->getIndex();

        if (!$doc) {
            // This is text and not unindexed so we can find items in the index
            // Only added on the first iteration
            $doc = new Lucene\Document();
            $initialRun = true;
            $fields = array();
        }

        foreach ($entity->getValues() as $value) {
            $renderer = $value->getAttribute()->getRenderer()->getClassObject('searchindex');

            // Set renderer parameters
            if ($renderer instanceof Parameters)
                $renderer->setParameters($value->getAttribute()->getParameters());

            switch ($renderer->getDataType()) {
                case 'datetime':
                case 'decimal':
                case 'integer':
                case 'varchar':
                    $field = Document\Field::Unstored(
                        $value->getAttribute()->getCode(),
                        $renderer->formatSearchValue($value->getValue())
                    );
                    $doc->addField($field);
                    break;
                case 'array':
                    # array renderers must provide valid search values through formatSearchValue
                    # throw new \Stuki\Exception("Arrays are not yet handled in search indexer");
                case 'text':
                    $field = Document\Field::Unstored(
                        $value->getAttribute()->getCode(),
                        $renderer->formatSearchValue($value->getValue())
                    );
                    $doc->addField($field);
                    break;
                default:
                    throw new \Stuki\Exception("Invalid datatype");
            }
        }

        if ($initialRun) {
            $field = Document\Field::Text(
                'entity_key',
                $entity->getKey()
            );
            $doc->addField($field);

            $field = Document\Field::Unstored(
                'entity_title',
                $entity->getTitle()
            );
            $doc->addField($field);

#            // Add all item fields to the search
#            $doc->addField(\Zend\Search\Lucene\Field::Unstored('ref\attribute\set', $entity->attributeSet->attribute\set\key));
#            $doc->addField(\Zend\Search\Lucene\Field::Unstored('ref\entity', $entity->parent->entity\key));
##            $doc->addField(\Zend\Search\Lucene\Field::Unstored('ref\user', $entity->user->user\key));
#            $doc->addField(\Zend\Search\Lucene\Field::Text('attribute\set\name', $entity->attributeSet->name));

#            // Include just the parent with each doc
#            $doc = $this->wrapper->buildDocument($index, $entity->parent, $doc);
#            $fields = array(); # Reset fields
        }

    return $doc;
    }
}
