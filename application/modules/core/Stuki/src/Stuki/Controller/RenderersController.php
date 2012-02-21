<?php

namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Renderer\Exception as Exception,
    Stuki\Form\Renderers\Test as TestForm,
    Stuki\Form\Renderers\Insert as InsertForm
    ;

class RenderersController extends ActionController
{
    public function indexAction()
    {
        $renderers = $this->getLocator()->get('modelRenderers');

        return array(
            'renderers' => $renderers->findAll()
        );
    }

    /**
     * Add a new renderer.  Since renderers must be tested for a valid
     * class this is it's own action (instead of at the bottom of index)
     * and it makes adding a new one more deliberate.
     */
    public function insertAction() {
        $form = new InsertForm();
        $request = $this->getRequest();
        $renderers = $this->getLocator()->get('modelRenderers');

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            try {
                // Add renderer
                $values = $form->getValues();
                $renderers->insert($values['alias'], $values['class']);

                // Redirect to /renderers
                $this->plugin('redirect')->toUrl('/renderers');

            } catch (Exception\InvalidArgumentException $e) {
                $form->getElement('class')->addError($e->getMessage());
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return array(
            'form' => $form
        );
    }

    public function deleteAction() {
        $renderers = $this->getLocator()->get('modelRenderers');
        $renderer = $renderers->find((int)$this->getRequest()->query()->get('renderer_key'));
        $renderers->delete($renderer);

        // Redirect to /renderers
        $this->plugin('redirect')->toUrl('/renderers');
    }

    /**
     * This tests a renderer by creating a form
     * with the given renderer, applying the value and
     * returning the error array
     */
    public function testAction() {
        $request = $this->getRequest();

        $renderers = $this->getLocator()->get('modelRenderers');

        if (!$renderer = $renderers->find((int)$request->query()->get('renderer_key'))) {
            throw new \Stuki\Exception('No renderer key or an invalid key was provided');
        }

        // Create the renderer
        $element = $renderer->getClassObject('value');
        $element->setLabel($renderer->getAlias());
        $element->setParameters(array());
        $element->postCreate(); # FIXME: change to listener


        // Create the test form and attach renderer
        $form = new TestForm();
        $form->addElement($element);

        // Add submit here so it ends up after element.  May be a more Zend
        // friendly way to do this.
        $submit = $form->createElement('submit', 'submit');
        $submit->setLabel("Test");
        $submit->setIgnore(true);
        $submit->tabindex = 8;
        $form->addElement($submit);

        // A simple flag; set setDescription below
        $isFormOk = false;

        if ($request->isPost()) {
            if ($form->isValid($request->post()->toArray())) {
                $isFormOk = true;
                #not working?
                #$form->setDescription('Form is valid');
            }
        }

        return array(
            'form' => $form,
            'element' => $element,
            'isFormOk' => $isFormOk
        );
    }
}
