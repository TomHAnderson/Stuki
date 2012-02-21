<?

namespace Attachments\Form;

use Stuki\Form;

class Insert extends Form {

    function init() {
        $this->setAttrib('enctype', 'multipart/form-data');

        // description
        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description')
                 ->setDescription('A description of this file');
        $description->rows = 3;
        $description->cols = 80;

        // Add file upload
        // randomly assign this item to a subdirectory
#        $rand_chr = chr(rand(97, 122));
#        if (!file_exists(APPLICATION_PATH . "/pictures/$rand_chr/$edit_item_key/")) {
#            mkdir(APPLICATION_PATH . "/pictures/$rand_chr/$edit_item_key/");
#        }
        $adapter = new \Zend\File\Transfer\Adapter\Http(array('magicFile' => '/usr/share/misc/magic'));

        $upload = $this->createElement('file', 'file1'); #new \Zend\Form\Element\File('file1');
        $upload->setTransferAdapter($adapter);
        $upload->setLabel("Attachment")
               ->setDescription('Select a file to attach to this entity')
        ;

        // Create submit button
        $submit = $this->createElement('submit', 'upload');
        $submit->setLabel("Upload");

        // Add elements to form:
        $this->addElement($upload);
        $this->addElement($description);
        $this->addElement($submit);
    }
}
