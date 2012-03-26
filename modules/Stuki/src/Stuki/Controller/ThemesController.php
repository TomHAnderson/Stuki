<?php

namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Theme\Exception as Exception,
    Stuki\Form\Themes\Insert as InsertForm
    ;

class ThemesController extends ActionController
{
    public function indexAction()
    {
        $themes = $this->getLocator()->get('modelThemes');

        return array(
            'allThemes' => $themes->findAll()
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
        $modelThemes = $this->getLocator()->get('modelThemes');

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            try {
                // Add renderer
                $values = $form->getValues();
                $modelThemes->insert($values['alias'], $values['template']);

                // Redirect to /renderers
                $this->plugin('redirect')->toUrl('/themes');

            } catch (Exception\InvalidArgumentException $e) {
                $form->getElement('template')->addError($e->getMessage());
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return array(
            'form' => $form
        );
    }

    public function deleteAction() {
        $themes = $this->getLocator()->get('modelThemes');
        $theme = $themes->find((int)$this->getRequest()->query()->get('theme_key'));
        $themes->delete($theme);

        // Redirect to /renderers
        $this->plugin('redirect')->toUrl('/themes');
    }
}
