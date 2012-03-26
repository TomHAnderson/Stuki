<?php

/**
 * This model is used to construct searches and manage the search index
 */
namespace Attachments\Model;

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

class SearchIndex extends Model
{

    private $index;

    /**
     * Open or create the search index
     */
    public function getIndex() {
        if (!$this->index) {
            try {
                $this->index = LuceneBase::open(APPLICATION_PATH . '/data/AttachmentsSearch');
            } catch (\Exception $e) {
                $this->index = LuceneBase::create(APPLICATION_PATH . '/data/AttachmentsSearch');
            }
        }

        QueryParser::setDefaultEncoding("utf-8");
        DefaultAnalyzer::setDefault(new Utf8NumCaseInsensitive());


        return $this->index;
    }

    /**
     * Index or re-index an EAV entity
     */
    public function index(\Attachments\Entity\Attachments $attachment) {
        $this->delete($attachment);
        $this->insert($attachment);

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

    public function delete(\Attachments\Entity\Attachments $attachment) {
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
                new Lucene\Index\Term($attachment->getKey(), 'attachment_key')
            ), $sign);

        $res = $index->find($query);
        foreach ($res as $hit) {
            // hit->id is the internal lucene document number
            $index->delete($hit->id);
        }

        return true;
    }

    public function insert(\Attachments\Entity\Attachments $attachment) {
        if ($doc = $this->buildDocument($attachment)) {
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
    public function buildDocument(\Attachments\Entity\Attachments $attachment) {

        $index = $this->getIndex();

        // This is text and not unindexed so we can find items in the index
        // Only added on the first iteration
        $doc = new Lucene\Document();
        $initialRun = true;
        $fields = array();

        $field = Document\Field::Text(
            'attachment_key', $attachment->getKey()
        );
        $doc->addField($field);

        $field = Document\Field::Text(
            'entity_key', $attachment->getEntity()->getKey()
        );
        $doc->addField($field);

        $field = Document\Field::Unstored(
            'filename', $attachment->getFileName()
        );
        $doc->addField($field);

        $field = Document\Field::Unstored(
            'path', $attachment->getPath()
        );
        $doc->addField($field);

        $field = Document\Field::Unstored(
            'description', $attachment->getDescription()
        );
        $doc->addField($field);

        $field = Document\Field::Unstored(
            'uploaded_at', $attachment->getUploadedAt()->format('Y-m-d')
        );
        $doc->addField($field);

        return $doc;
    }
}
