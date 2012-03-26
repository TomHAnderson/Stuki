<?php

namespace Attachments\Controller;

use Zend\Mvc\Controller\ActionController,
    Attachments\Form\Insert as InsertForm,
    Attachments\Form\Update as UpdateForm
    ;

class IndexController extends ActionController
{
    public function uploadAction() {

        # FIXME:  move into config file
        ini_set('memory_limit', '256M');

        $modelEntities = $this->getLocator()->get('modelEntities');
        $modelAttachments = $this->getLocator()->get('modelAttachments');

        // Get current entity
        if (!$entity_key = $this->request->query()->get('entity_key'))
            throw new \Stuki\Exception('An entity key is required');
        if (!$entity = $modelEntities->find((int)$entity_key))
            throw new \Stuki\Exception('Entity not found');

        // Get file meta data
        if (!$filename = $this->request->headers()->get('x-file-name')->getFieldValue())
            throw new \Stuki\Exception('File name missing');
#        $filesize = $this->request->headers()->get('x-file-size')->getFieldValue();
#        $filetype = $this->request->headers()->get('x-file-type')->getFieldValue();

        // Decode the content
        $post = $this->request->post()->get('file-data');
        $content = base64_decode($post, true);

        // Get the file comment
        $description = $this->request->post()->get('description');

        // Fetch save location;
        $path = $modelAttachments->generateFilePath($filename, realpath(APPLICATION_PATH . '/data/Attachments'));

        // Write the file
        touch($path . '/' . $filename);
        $fhand = fopen($path . '/' . $filename, 'w');
        fwrite($fhand, $content);
        fclose($fhand);

        /**
         * Save the whole path to the file to the database so we can trust the complete path
         * based on the attahments key
         */

        // Add the file to the database
        $modelAttachments->insert($entity, array(
            'filename' => $filename,
            'path' => $path . '/' . $filename,
            'description' => $description
        ));

        // Should return a json object
        return array(
            'success' => true,
            'code' => 0,
            'message' => 'File Uploaded',
            'originalFileName' => $filename
        );


//        {"success":false,"code":5,"message":"Uploaded File Type Not Supported (sorry)","originalFileName":"ia.html"}


        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelEntities = $this->getLocator()->get('modelEntities');

        $attributeSets = $modelAttributeSets->findAll();
        if (!$root = $modelEntities->getRoot()) {
            $root = new \Entities\Entities;
        }

        return array(
            'rootEntity' => $root->getKey(),
            'attributeSets' => $attributeSets
        );
    }

    public function insertAction() {

        $modelEntities = $this->getLocator()->get('modelEntities');
        $modelAttachments = $this->getLocator()->get('modelAttachments');

        // Get current entity
        if (!$entity_key = $this->request->query()->get('entity_key'))
            throw new \Stuki\Exception('An entity key is required');
        if (!$entity = $modelEntities->find((int)$entity_key))
            throw new \Stuki\Exception('Entity not found');

        $form = new InsertForm();

        if ($this->getRequest()->isPost()
            AND $form->isValid($this->getRequest()->post()->toArray())
            AND $file = $_FILES['file1']
            AND $file['tmp_name']) {

            $filename = $file['name'];

            // Set save path on form file element
            $path = $modelAttachments->generateFilePath($filename, realpath(APPLICATION_PATH . '/data/Attachments'));
            $form->getElement('file1')->setDestination($path);

            // This triggers file saving
            $values = $form->getValues(); # trigger
            $description = $values['description'];

            // Add the file to the database
            $attachment = $modelAttachments->insert($entity, array(
                'filename' => $filename,
                'path' => $path . '/' . $filename,
                'description' => $description
            ));

#            return $this->plugin('redirect')->toUrl('/attachments/insert?entity_key=' . $entity->getKey());
            // Reset the form
            $form = new InsertForm();
        }

        return array(
            'form' => $form,
            'entity' => $entity,
            'attachment' => (isset($attachment)) ? $attachment: ''
        );
    }

    /**
     * Update an attachment to create an audit log for this file
     */
    public function updateAction() {

        $modelAttachments = $this->getLocator()->get('modelAttachments');

        // Get current entity
        if (!$attachment_key = $this->request->query()->get('attachment_key'))
            throw new \Stuki\Exception('An attachment key is required');
        if (!$attachment = $modelAttachments->find((int)$attachment_key))
            throw new \Stuki\Exception('Attachment not found');

        $form = new UpdateForm();

        if ($this->getRequest()->isPost()
            AND $form->isValid($this->getRequest()->post()->toArray())
            AND $file = $_FILES['file1']
            AND $file['tmp_name']) {

            $filename = $file['name'];

            // Set save path on form file element
            $path = $modelAttachments->generateFilePath($filename, realpath(APPLICATION_PATH . '/data/Attachments'));
            $form->getElement('file1')->setDestination($path);

            // This triggers file saving
            $values = $form->getValues(); # trigger

            // Add the file to the database
            $attachment = $modelAttachments->update($attachment, array(
                'filename' => $filename,
                'path' => $path . '/' . $filename,
                'description' => $values['description']
            ));

            return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' .
                $attachment->getEntity()->getKey());
        } elseif (!$this->getRequest()->isPost()) {
            $form->setDefaults(array(
                'description' => $attachment->getDescription()
            ));
        }

        return array(
            'form' => $form,
            'attachment' => $attachment
        );
    }

    public function deleteAction() {
        $modelAttachments = $this->getLocator()->get('modelAttachments');

        // Get current entity
        if (!$attachment_key = $this->request->query()->get('attachment_key'))
            throw new \Stuki\Exception('An attachment key is required');
        if (!$attachment = $modelAttachments->find((int)$attachment_key))
            throw new \Stuki\Exception('Attachment not found');

        $entity = $attachment->getEntity();

        // Delete the attachment
        $modelAttachments->delete($attachment);

        return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $entity->getKey());

    }

    public function downloadAction() {
        $modelAttachments = $this->getLocator()->get('modelAttachments');

        // Get current entity
        if (!$attachment_key = $this->request->query()->get('attachment_key'))
            throw new \Stuki\Exception('An attachment key is required');
        if (!$attachment = $modelAttachments->find((int)$attachment_key))
            throw new \Stuki\Exception('Attachment not found');

        $entity = $attachment->getEntity();
        $path = $attachment->getPath();

        /* get mime-type */
        $finfo = new \finfo(FILEINFO_MIME, "/usr/share/file/magic/magic"); // return mime type ala mimetype extension
        if (!$finfo) {
            echo "Opening fileinfo database failed";
            exit();
        }
        $mime = $finfo->file($path);

        header('Content-Length:' . @filesize($path));
        header('Content-Type:' . $mime);
        header('Content-Disposition:inline; filename="' . basename($path) . '"');

        # FIXME:  add X-Sendfile support?
#            if ($this->config->files->useSendfile) {
#                header("X-Sendfile:" . $path);
#            } else {

        # FIXME:  this should disable the view and not just die out
        readfile($path);
        die();

        return array();
    }


    public function searchAction() {
        $search = $this->getLocator()->get('modelSearch');
        $attachments = $this->getLocator()->get('modelAttachments');
        $index = $this->getLocator()->get('modelAttachmentsSearchIndex');
        $search->setIndex($index->getIndex());
        $search->setField('attachment_key');

        $terms = $this->getRequest()->query()->get('terms');

        $error = '';
        $results = array();
        try {
            if ($terms) {
                foreach ($search->search($terms) as $attachments_key) {
                    $results[] = $attachments->find((int)$attachments_key);
                }
            }
        } catch (Lucene\Exception $e) {
            $error = $e->getMessage();
        }

        return array(
            'terms' => $terms,
            'attachments' => $results,
            'searchError' => $error
        );
    }
}
