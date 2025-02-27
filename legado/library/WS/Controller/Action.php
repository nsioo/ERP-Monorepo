<?php

/**
 * Classe genenérica da camada Controller
 * @author Fernando Henrique (nand0.gauch0@gmail.com)
 * @version 1.0
 * @package WS
 */
class WS_Controller_Action extends Zend_Controller_Action {

    /**
     *
     */
    protected $options;
    protected $module;
    protected $model;
    protected $form;
    public $actions = array(
        'delete' => 'itemDel',
        'edit' => 'btEditar'
    );
    public $buttons = array(
        'add' => true,
        'del' => true
    );

    /**
     * Inicialização do controller
     */
    public function init() {
        parent::init();

        /**
         * Captura o nome do Módulo, usado para gerar as urls
         */
        $this->module = $this->_request->getModuleName();
        $this->view->module = $this->_request->getModuleName();

        /**
         * Captura o nome do controller, é usado para montar o titulo
         */
        $this->view->controller = $this->controller = $this->_request->getControllerName();

        /**
         * Captura o nome do controller, é usado para montar os submodulos
         */
        $this->view->action = $this->action = $this->_request->getActionName();
        /**
         * Joga o model para a view, é necessário para capturar algumas
         * informações, como textos dos botões, títulos e variáveis
         */
        if (isset($this->model)):
            $this->view->model = $this->model;
        endif;

        if (isset($this->actions)):
            $this->view->actions = $this->actions;
        endif;

        if (isset($this->buttons)):
            $this->view->buttons = $this->buttons;
        endif;

        /**
         * Joga os parametros para a view
         */
        $this->view->data = $this->_request->getParams();

        if ($this->_request->getParam('submodulo') || $this->_request->getParam('print')):
            $this->_helper->layout->disableLayout();
            $this->options['noRedirect'] = true;
            $this->view->noLayout = true;
        endif;

        if ($this->_request->getParam('print')):
            $this->_helper->layout()->setLayout('print');
        endif;

        if ($this->_request->getParam('save')):
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
        endif;

        if (isset($this->form)) :
            $this->view->form = $this->form;
            $cancelar = $this->form->getElement('cancelar');
            if ($cancelar):
                if ($this->_request->getParam('submodulo')):
                    $cancelar->setAttrib('disabled', 'disabled');
                endif;
            endif;
        endif;

        $busca = $this->_request->getParam('busca');
        if (!empty($busca)):
            $this->model->_searchWord = $busca;
        endif;

        $page = $this->_request->getParam('page');
        if (!empty($page)):
            $this->model->_page = $page;
        endif;
    }

    /**
     * Método para exibir a mensagem no modo de sticker
     * @param string $tipo
     * @param string $mensagem
     */
    public function alerta($tipo, $mensagem) {
        /*
          echo '<script type="text/javascript">
          $(function(){

          $("#legal").html("legal");
          });
          </script>';
          return false;
         */
        $mensagem = str_replace("\n", ' ', $mensagem);
        $mensagem = str_replace("\r", ' ', $mensagem);
        $mensagem = str_replace("<br />", ' ', $mensagem);
        echo '<script type="text/javascript">
    $(function(){
        $.jGrowl("' . $mensagem . '", { theme: "' . $tipo . '", life: 10000 });
    });
</script>';

        $submodulo = $this->_request->getParam('submodulo');
        if ($tipo == 'sucess' && !empty($submodulo)):
            echo '<script>
    $(function(){
        $(".modal").each(function(){
            if($(this).dialog("isOpen")){
                $(this).dialog("close");
                return false;
            }
        });
    });
</script>';
        endif;
    }

    /**
     * Função default de ação dos módulos, usado quando envia o formulário
     * @param array $data
     * @param Zend_Controller_Action $object
     * @return null
     */
    public function action(array $data, Zend_Controller_Action $object) {
        $data = $this->model->adjustToDb($data);
        if (!empty($data['id'])):
            $id = $data['id'];
            try {
                $object->model->_db->atualiza($data, $object->model->getOption('actions', 'update'), $object->model->_db->getTableName());
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('sucess' => $object->model->getOption('messages', 'update')));
                else:
                    $this->alerta('sucess', $object->model->getOption('messages', 'update'));
                endif;
                if (!isset($object->options['noRedirect'])):
                    if (isset($object->options['linkUpdate'])):
                        $this->_redirect($object->options['linkUpdate']);
                    else:
                        if (isset($object->options['noForm'])):
                            $this->_redirect('/' . $this->module . '/' . $this->view->controller);
                        else:
                            $this->_redirect('/' . $this->module . '/' . $this->view->controller . '/formulario/' . $id);
                        endif;
                    endif;
                endif;
                $data = $this->model->adjustToForm($data);
                $object->form->populate($data);
            } catch (Exception $e) {
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('error' => $e->getMessage()));
                else:
                    $this->alerta('error', $e->getMessage());
                endif;
                $data = $this->model->adjustToForm($data);
                $object->form->populate($data)->markAsError();
            }
        else:
            try {
                $id = $object->model->_db->insere($data, $object->model->getOption('actions', 'add'), $object->model->_db->getTableName());
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('sucess' => $object->model->getOption('messages', 'add')));
                    $this->view->id = $id;
                else:
                    $this->alerta('sucess', $object->model->getOption('messages', 'add'));
                endif;
                if (!isset($object->options['noRedirect'])):
                    if (isset($object->options['linkInsert'])) :
                        $this->_redirect($object->options['linkInsert']);
                    else :
                        if (isset($object->options['noList'])):
                            $this->_redirect('/' . $this->module . '/' . $this->view->controller . '/formulario/' . $id);
                        else:
                            $this->_redirect('/' . $this->module . '/' . $this->view->controller);
                        endif;
                    endif;
                endif;
                $data['id'] = $id;
                $data = $this->model->adjustToForm($data);
                $object->form->populate($data);
                return $id;
            } catch (Exception $e) {
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('error' => $e->getMessage()));
                else:
                    $this->alerta('error', $e->getMessage());
                endif;
                $data = $this->model->adjustToForm($data);
//$object->form->populate($data)->markAsError();
            }
        endif;
    }

    /**
     * Método padrão para exibição dos erros no popup
     * @param Zend_Controller_Action $object
     */
    public function error($object) {
        $data = $this->_request->getPost();
        if (!isset($object->options['noRedirect'])):
            $this->_helper->FlashMessenger(array('error' => 'Preencha todos os campos obrigatórios!'));
        else:
            $this->alerta('error', 'Preencha todos os campos obrigatórios');
        endif;
        $data = $this->model->adjustToForm($data);
        $object->form->populate($data)->markAsError();
    }

    /**
     * Médoto default de exclusão de itens
     * @param Zend_Controller_Action $object
     */
    public function exclude($object) {
        if ($this->_request->isPost()):
            $data = $this->_request->getPost();
            $LDados = $this->_request->getParams();
            try {
                $object->model->_db->deleta($data['item_del'], $object->model->getOption('actions', 'del'), $LDados, $object->model->_db->getTableName());
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('sucess' => $object->model->getOption('messages', 'del')));
                else:
                    $this->alerta('sucess', $object->model->getOption('messages', 'del'));
                endif;
            } catch (Exception $e) {
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('error' => $e->getMessage()));
                else:
                    $this->alerta('error', $e->getMessage());
                endif;
            }
        endif;

        if ($this->_request->isGet()):
            $data = $this->_request->getParams();
            try {
                $object->model->_db->deleta($data['id'], $object->model->getOption('actions', 'del'), $data, $object->model->_db->getTableName());
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('sucess' => $object->model->getOption('messages', 'del')));
                else:
                    $this->alerta('sucess', $object->model->getOption('messages', 'del'));
                endif;
            } catch (Exception $e) {
                if (!isset($object->options['noRedirect'])):
                    $this->_helper->FlashMessenger(array('error' => $e->getMessage()));
                else:
                    $this->alerta('error', $e->getMessage());
                endif;
            }
        endif;

        if (!isset($this->options['noRedirect'])):
            if (isset($this->options['linkDelete'])) :
                $this->_redirect($this->options['linkDelete']);
            else :
                $this->_redirect('/' . $this->module . '/' . $this->view->controller);
            endif;
        endif;
    }

    /**
     * Método padrão para a action index buscando a listagem de itens
     */
    public function indexAction() {
        if (isset($this->model->_layoutList)):
            if ($this->model->_layoutList == 'basic'):
                $items = $this->model->basicSearch();
                $this->view->items = $items;
                $this->renderScript('default/basic.phtml');
            elseif ($this->model->_layoutList == 'basic-controller'):
                $items = $this->model->basicSearch();
                $this->view->items = $items;
                $this->renderScript($this->controller . '/index.phtml');
            elseif ($this->model->_layoutList == 'dataTable'):
                $this->renderScript('default/datatable.phtml');
            else:
                $this->renderScript('default/' . $this->model->_layoutList . '.phtml');
            endif;
        endif;
    }

    public function dataAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $items = $this->model->listagem();
        $fields = $this->model->getViewFields();
        foreach ($items AS $dados):
            $data = null;
            $dados = $this->model->adjustToView($dados);
            foreach ($this->actions AS $key => $action):
                if ($action == 'btEditar'):
                    $data[] = '<a href = "/' . $this->module . '/' . $this->controller . '/formulario/' . $dados['id'] . '" title = "' . $this->model->getOption('buttons', $key) . '" class = "btEditar">' . $this->model->getOption('buttons', $key) . '</a>';
                elseif ($action == 'btExcluir'):
                    $data[] = '<button type="submit" id="btExcluir" name="excluir" title="' . $this->model->getOption('buttons', $key) . '">' . $this->model->getOption('buttons', $key) . '</button>';
                elseif ($action == 'itemDel'):
                    $data[] = '<input type="checkbox" value="' . $dados['id'] . '" name="item_del[]" class="itemDel" />';
                endif;
                /*
                  $data[] = $this->view->partial('partials/' . $action . '.phtml', array(
                  'message' => $this->model->getOption('buttons', $key),
                  'id' => $dados['id'],
                  'controller' => $this->controller,
                  'module' => $this->module
                  ));
                 */
            endforeach;
            foreach ($fields AS $key => $field):
                $data[] = $dados[$key];
            endforeach;
            $retorno[] = $data;
        endforeach;
        $item['aaData'] = $retorno;
        echo Zend_Json::encode($item);
    }

    /**
     * Método padrão para a action busca
     */
    public function buscaAction() {

        $items = $this->model->basicSearch();
        $this->view->items = $this->model->_items = $items;

        $this->_helper->layout->disableLayout();
        $this->renderScript('default/busca.phtml');
    }

    /**
     * Método padrão para a action formulario validando o form e executando as ações
     */
    public function formularioAction() {
        if (!empty($this->model->_params)) :
            $this->form->populate($this->model->_params);
        endif;

        if ($this->_request->isPost()):
            if ($this->form->isValid($this->_request->getPost())) :
                $data = $this->form->getValues();

                if (!empty($this->model->_params)) :
                    foreach ($this->model->_params AS $key => $param):
                        $data[$key] = $param;
                    endforeach;
                endif;

                $id = $this->action($data, $this);
                if (!empty($id)) :
                    $data['id'] = $id;
                endif;
            else :
                $data = $this->_request->getPost();
                if (!empty($this->model->_params)) :
                    foreach ($this->model->_params AS $key => $param):
                        $data[$key] = $param;
                    endforeach;
                endif;

                $this->view->data = $data;
                $this->error($this);
            endif;
        else:
            $id = $this->_request->getParam('id');
            if (!empty($this->model->_params[' id'])):
                $id = $this->model->_params['id'];
            endif;
            if (!empty($id)):
                $data = $this->model->find($id);
                if (!empty($data)):
                    $data = $this->model->adjustToForm($data);
                    $this->form->populate($data);
                    $data['encontrouDados'] = true;
                endif;
            endif;
            if (!empty($this->model->_params)) :
                foreach ($this->model->_params AS $key => $param):
                    $data[$key] = $param;
                endforeach;
            endif;
        endif;

        if (isset($data)):
            $this->view->data = $data;
        endif;

        $this->view->form = $this->form;

        if ($this->model->_layoutForm == 'basic'):
            $this->renderScript('default/formulario.phtml');
        endif;
    }

    /**
     * Método padrão para a action excluir
     */
    public function excluirAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->exclude($this);
    }

}
