<?php

/**
 * Classe genenérica da camada business, útil para criação de módulos
 * para sistemas de gerenciamento
 *
 * @author Fernando Henrique (nand0.gauch0@gmail.com)
 * @version 0.9
 * @package WS
 */
class WS_Model {

    /**
     * [pt-br] Título do módulo
     * [en] Title of module
     * @var string $_title
     */
    public $_title;

    /**
     * [pt-br] Nome do objeto no singular
     * [en] Name of the object in the singular
     * @var string $_singular
     */
    public $_singular;

    /**
     * [pt-br] Nome do objeto no plural
     * [en] Name of the object in the plural
     * @var string $_plural
     */
    public $_plural;

    /**
     * [pt-br] Campos que irão no form gerado automaticamente
     * [en] Fields that will form the automatically generated
     * @var array $_formFields
     */
    public $_formFields;

    /**
     * [pt-br] Campos que irão na view da index
     * [en] Fields that will views of index
     * @var array $_viewFields
     */
    public $_viewFields;

    /**
     * [pt-br]
     * [en]
     * @var array $_adjustFields
     */
    public $_adjustFields;

    /**
     * [pt-br]
     * [en]
     * @var array $_orderFields
     */
    public $_orderFields;

    /**
     * [pt-br]
     * [en]
     * @var array $_searchFields
     */
    public $_searchFields;

    /**
     * [pt-br] Mensagens para o FlashMessenger
     * [en] Messages for the FlashMessenger
     * @var array $_messages
     */
    public $_messages;

    /**
     * [pt-br] Mensagens para o log
     * [en] Messages for the log
     * @var array $_actions
     */
    public $_actions;

    /**
     * [pt-br] Mensagens para os botões
     * [en] Messages for the buttons
     * @var array $_buttons
     */
    public $_buttons;

    /**
     * [pt-br] Dados extras para popular o formulário
     * [en] Extra data to populate the form
     * @var array $_params
     */
    public $_params;

    /**
     * [pt-br] Objeto de database que será usado para executar as querys
     * [en] Object database that will be used to run the queries
     * @var Zend_Db_Table $_db
     */
    public $_db;

    /**
     * [pt-br] Id da database, para consultas agrupadas
     * [en] Database id, for join querys
     * @var string $_primary
     */
    public $_primary = 'id';

    /**
     * [pt-br] Classe do Zend_Db_Table abstrata que será aberta quando não for
     * necessário nenhuma classe de tabela específica
     * [en] Zend_Db_Table abstract class that will open when class is not used
     * any specific table
     * @var Zend_Db_Table $_defaultTable
     */
    protected $_defaultTable;

    /**
     * [pt-br] Tipo de layout genérico da index (basic|datatables)
     * [en] Generic type of index layout
     * @var string $_layoutList
     */
    public $_layoutList;

    /**
     * [pt-br] Tipo de layout genérico do formulario (basic|datatables)
     * [en] Generic type of index layout
     * @var string $_layoutForm
     */
    public $_layoutForm = 'basic';

    /**
     * [pt-br] Deverá aparecer o botão de adicionar item
     * [en] You should see the button to add item
     * @var string $_layoutType
     */
    public $_addButton = true;

    /**
     * [pt-br] Query padrão para as buscas
     * [en] Default query for searches
     * @var Zend_Db_Table_Select $_basicSearch
     */
    public $_basicSearch;

    /**
     * [pt-br] String que será usada no filtro de busca
     * [en] String that will be used in the filter search
     * @var string $_searchWord
     */
    public $_searchWord;

    /**
     * [pt-br] Página atual da listagem de itens
     * [en] Page current of items list
     * @var integer $_page
     */
    public $_page = 1;

    /**
     * [pt-br] ítens
     * [en] Page current of items list
     * @var integer $_page
     */
    public $_items;

    /**
     * [pt-br] �tens por p�gina
     * [en] Items per page
     * @var integer $_itemsPerPage
     */
    public $_itemsPerPage = 10;

    /**
     * [pt-br] Inicializa as variáveis para ter um pacote genérico de mensagens de exibição
     * [en] Initializes the variables to have a generic package of display messages
     * @return null
     */
    public function __construct() {
        $this->_messages = array(
            'add' => $this->_singular . ' inserido com sucesso!',
            'update' => $this->_singular . ' atualizado com sucesso!',
            'del' => $this->_plural . ' excluído(s) com sucesso!',
            'empty' => 'Nenhum ' . $this->_singular . ' encontrado!'
        );
        $this->_actions = array(
            'add' => 'Inseriu um ' . $this->_singular . '!',
            'update' => 'Alterou um ' . $this->_singular . '!',
            'del' => 'Excluiu um ou mais ' . $this->_plural . '!',
        );
        $this->_buttons = array(
            'add' => 'Novo ' . $this->_singular,
            'edit' => 'Editar o ' . $this->_singular,
            'delete' => 'Excluir o ' . $this->_singular,
            'view' => 'Visualizar o ' . $this->_singular,
            'go' => 'Ir para o ' . $this->_singular
        );
        $this->setFormFields();
        $this->setViewFields();
        $this->setSearchFields();
        $this->setAdjustFields();
        $this->setOrderFields();
        $this->setBasicSearch();
    }

    /**
     * [pt-br] Método para transformar as mensagens para o feminino
     * [en] Method to transform the messages to the female
     * @return array
     */
    public function turningFemale() {
        $this->_messages = array(
            'add' => $this->_singular . ' inserida com sucesso!',
            'update' => $this->_singular . ' atualizada com sucesso!',
            'del' => $this->_plural . ' excluídas com sucesso!',
            'empty' => 'Nenhuma ' . $this->_singular . ' encontrada!'
        );
        $this->_actions = array(
            'add' => 'Inseriu uma ' . $this->_singular . '!',
            'update' => 'Alterou uma ' . $this->_singular . '!',
            'del' => 'Excluiu uma ou mais ' . $this->_plural . '!'
        );
        $this->_buttons = array(
            'add' => 'Nova ' . $this->_singular,
            'edit' => 'Editar a ' . $this->_singular,
            'delete' => 'Excluir a ' . $this->_singular,
            'view' => 'Visualizar a ' . $this->_singular,
            'go' => 'Ir para a ' . $this->_singular
        );
    }

    /**
     * [pt-br] Método para buscar o default de acesso ao banco de dados
     * [en] Method to get the default access to the database
     * @return Zend_Db_Table_Adapter
     */
    public static function getDefaultAdapter() {
        return Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * [pt-br] Médo para gerar a busca que irá parecer na listagem de itens
     * [en] Method to generate a search that will appear on the list of items
     * @return Zend_Db_Table_Select
     */
    public function setBasicSearch() {
        if (isset($this->_db)):
            $this->_basicSearch = $this->_db->select()->from($this->_db->getTableName());
        endif;
    }

    /**
     * [pt-br] Método para aplicar os filtros necessários na busca
     * [en] Method to apply the necessary filters in search
     * @return array
     */
    public function basicSearch($param = NULL) {
        $this->setBasicSearch();

        if (isset($this->_params)):
            foreach ($this->_params AS $key => $value):
                $this->_basicSearch->where($key . ' = ?', $value);
            endforeach;
        endif;

        if (isset($this->_paramsN)):
            foreach ($this->_paramsN AS $key => $value):
                $this->_basicSearch->where($key . ' != ?', $value);
            endforeach;
        endif;

        if (!empty($this->_orderFields)):
            foreach ($this->_orderFields AS $key => $order):
                $this->_basicSearch->order("$key $order");
            endforeach;
        endif;

        if (isset($this->_db)):
            if ((isset($this->_layoutList) && $this->_layoutList == 'basic') || ($param['return'] == 'paginator') && isset($param['return'])):
                $this->_items = $this->applySearchFilters();
            else:
                $this->_items = $this->_basicSearch->query()->fetchAll();
            endif;
        endif;

        $return = $this->_items;

        if (!empty($return)):
            return $return;
        endif;
    }

    /**
     * [pt-br] Método mágico para listar as opções de uma variável criada no model
     * [en] Magic method to list the options for a variable created in the model
     * @return Array
     */
    public function listOptions($var) {
        $variavel = '_' . strtolower($var);
        if (isset($this->$variavel)):
            return $this->$variavel;
        endif;
    }

    /**
     * [pt-br] Método mágico para buscar o valor de uma variável
     * [en] Magic method to fetch the value of a variable
     * @return string
     */
    public function getOption($var, $key = null) {
        $variavel = '_' . strtolower($var);
        $variavel = $this->$variavel;

        $retorno = NULL;
        if (isset($key)):
            if (isset($variavel[$key])):
                $retorno = $variavel[$key];
            endif;
        else:
            $retorno = $variavel;
        endif;

        return $retorno;
    }

    /**
     * [pt-br] Método mágico para setar o valor de uma variável
     * [en] Magic method to set the value of a variable
     * @return string
     */
    public function setOption($var, $content) {
        $variavel = '_' . $var;
        $this->$variavel = $content;
    }

    /**
     * Método para retornas os campos utilizados nos forms e ajustes
     * @return array
     */
    public function setViewFields() {
        return false;
    }

    public function getViewFields() {
        return $this->_viewFields;
    }

    public function setFormFields() {
        return false;
    }

    public function getFormFields() {
        return $this->_formFields;
    }

    public function setOrderFields() {
        return false;
    }

    public function getOrderFields() {
        return $this->_OrderFields;
    }

    public function setAdjustFields() {
        return false;
    }

    public function getAdjustFields() {
        return $this->_adjustFields;
    }

    public function getSearchFields() {
        return $this->_searchFields;
    }

    public function setSearchFields() {
        return false;
    }

    /**
     * [pt-br] Método para inserir os filtros nas buscas
     * [en] Method to insert filters in searches
     * @return Zend_Paginator
     */
    public function applySearchFilters() {
        $this->setSearchFields();
        if (!empty($this->_searchWord)):
            if (!empty($this->_searchFields)):
                foreach ($this->_searchFields AS $key => $adjust):
                    switch ($adjust):
                        case 'money':
                            $value = WS_Money::adjustToDb($this->_searchWord);
                            if (!empty($value)):
                                $this->_basicSearch->orWhere("$key LIKE ?", '%' . $value . '%');
                            endif;
                            break;
                        case 'date':
                            $value = WS_Date::adjustToDb($this->_searchWord);
                            if (!empty($value)):
                                $this->_basicSearch->orWhere("$key = ?", $value);
                            endif;
                            break;
                        case 'datetime':
                            $value = WS_Date::adjustToDbWithHour($this->_searchWord);
                            if (!empty($value)):
                                $this->_basicSearch->orWhere("$key = ?", $value);
                            endif;
                            break;
                        case 'getKey':
                            $value = $this->getKey($key, $this->_searchWord);
                            if (!empty($value)):
                                $this->_basicSearch->orWhere("$key = ?", $value);
                            endif;
                            break;
                        default:
                            $this->_basicSearch->orWhere("$key LIKE ?", '%' . $this->_searchWord . '%');
                            break;
                    endswitch;
                endforeach;
            endif;
        endif;

        $adapter = new Zend_Paginator_Adapter_DbSelect($this->_basicSearch);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($this->_itemsPerPage);
        $paginator->setCurrentPageNumber($this->_page);
        $this->_items = $paginator;
        return $paginator;
    }

    public function getKey($var, $value) {
        $variavel = '_' . strtolower($var);
        $variavel = $this->$variavel;
        if (!empty($variavel)):
            foreach ($variavel AS $key => $var):
                if ($var == $value):
                    return $key;
                    continue;
                endif;
            endforeach;
        endif;
    }

    /**
     * [pt-br] Método para ajustar os dados para a listagem
     * [en] Method for adjusting the data to the list
     * @param array $data
     * @return array
     */
    public function adjustToView(array $data) {
        if (!isset($data['class'])):
            $data['class'] = '';
        endif;
        $data = $this->deserializaOpcoes($data);
        if (!empty($this->_adjustFields)):
            foreach ($this->_adjustFields AS $key => $field):
                if (isset($data[$key]) && !is_null($data[$key])):
                    switch ($field):
                        case 'date':
                            $data[$key] = WS_Date::adjustToView($data[$key]);
                            break;
                        case 'datetime':
                            $data[$key] = WS_Date::adjustToViewWithHour($data[$key]);
                            break;
                        case 'money':
                            $data[$key] = WS_Money::adjustToView($data[$key]);
                            break;
                        case 'getOption':
                            $data[$key] = $this->getOption($key, $data[$key]);
                            break;
                        default:
                            break;
                    endswitch;
                endif;
            endforeach;
        endif;

        return $data;
    }

    /**
     * [pt-br] Método para ajustar os dados para o formulário
     * [en] Method for adjusting the data for the form
     * @param array $data
     * @return array
     */
    public function adjustToForm(array $data) {
        $data = $this->deserializaOpcoes($data);

        if (isset($this->_adjustFields)):
            foreach ($this->_adjustFields AS $key => $adjust):
                if (isset($data[$key]) && !is_null($data[$key]) && !empty($data[$key])):
                    switch ($adjust):
                        case 'date':
                            $date = WS_Date::adjustToView($data[$key]);
                            if ($date):
                                $data[$key] = $date;
                            endif;
                            break;
                        case 'datetime':
                            $data[$key] = WS_Date::adjustToViewWithHour($data[$key]);
                            break;
                        default:
                            break;
                    endswitch;
                endif;
            endforeach;
        endif;
        return $data;
    }

    /**
     * [pt-br] Método para ajustar os dados para o banco de dados
     * [en] Method for adjusting the data to the database
     * @param array $data
     * @return array
     */
    public function adjustToDb(array $data) {
        if (isset($this->_adjustFields)):
            foreach ($this->_adjustFields AS $key => $adjust):
                if (isset($data[$key]) && !is_null($data[$key]) && !empty($data[$key])):
                    switch ($adjust):
                        case 'date':
                            $date = WS_Date::adjustToDb($data[$key]);
                            if ($date):
                                $data[$key] = $date;
                            else:
                                unset($data[$key]);
                            endif;
                            break;
                        case 'datetime':
                            $data[$key] = WS_Date::adjustToDbWithHour($data[$key]);
                            break;
                        case 'slug':
                            $data[$key] = WS_Text::slug($data[$key]);
                            break;
                        case 'money':
                            $data[$key] = WS_Money::adjustToDb($data[$key]);
                            break;
                        case 'int':
                            if (!is_int(intval($data[$key]))):
                                $data[$key] = 0;
                            else:
                                $data[$key] = intval($data[$key]);
                            endif;
                            break;
                        default:
                            break;
                    endswitch;
                else:
                    unset($data[$key]);
                endif;
            endforeach;
        endif;

        $data = $this->serializaOpcoes($data);

        return $data;
    }

    /**
     * [pt-br] M�todo para serializar um campo espec�fico
     * [en] Method to serialize a especific field
     * @params arrat data
     * @return array
     */
    public function serializaOpcoes(array $data) {
        foreach ($this->_formFields AS $key => $field):
            if (isset($field['serialize'])):
                $options[$key] = $data[$key];
                unset($data[$key]);
            endif;
            if (isset($options)):
                $data['serial_opts'] = serialize($options);
            endif;
        endforeach;
        return $data;
    }

    /**
     * [pt-br] M�todo para serializar um campo espec�fico
     * [en] Method to serialize a especific field
     * @params arrat data
     * @return array
     */
    public function deserializaOpcoes(array $data) {
        if (isset($data['serial_opts'])):
            $options = unserialize($data['serial_opts']);
            if (!empty($options)):
                foreach ($options AS $key => $value):
                    $data[$key] = $value;
                endforeach;
                unset($data['serial_opts']);
            endif;
        endif;
        return $data;
    }

    /**
     * [pt-br] Método para buscar um registro no banco de dados
     * [en] Method to search in the item database for one registry
     * @param integer $id
     * @return array
     */
    public function find($id) {
        $this->_basicSearch->where("$this->_primary = ?", $id);
        $item = $this->_basicSearch->query()->fetch();
        if (!empty($item)):
            return $item;
        endif;
    }

    /**
     * [pt-br] Método para buscar um ou mais registros no banco de dados através de um ou mais parâmetros
     * [en] Method to seek one or more records in the database through one or more parameters
     * @param array $params
     * @return array
     */
    public function findBy(array $params) {
        $sql = clone ($this->_basicSearch);
        foreach ($params AS $param => $value):
            $sql->orWhere("$param = ?", $value);
        endforeach;

        if (!empty($this->_orderFields)):
            foreach ($this->_orderFields AS $key => $order):
                $sql->order("$key $order");
            endforeach;
        endif;

        $items = $sql->query()->fetchAll();
        return $items;
    }

    /**
     * [pt-br] Método para buscar um ou mais registros no banco de dados através de um ou mais parâmetros
     * [en] Method to seek one or more records in the database through one or more parameters
     * @param array $params
     * @return array
     */
    public function findByAnd(array $params) {
        $sql = clone ($this->_basicSearch);
        foreach ($params AS $param => $value):
            $sql->where("$param = ?", $value);
        endforeach;

        if (!empty($this->_orderFields)):
            foreach ($this->_orderFields AS $key => $order):
                $sql->order("$key $order");
            endforeach;
        endif;

        echo $sql->__toString();

        $items = $sql->query()->fetchAll();
        return $items;
    }

    /**
     * [pt-br] Método utilizado para retornar um valor de um array de multiplos níveis
     * [en] Method used to return a value from an array of multiple levels
     * @param array $arrays
     * @param array $target
     * @return array
     */
    public function arrayImplode($arrays, &$target = array()) {
        foreach ($arrays as $item) {
            if (is_array($item)) {
                $this->arrayImplode($item, $target);
            } else {
                $target[] = $item;
            }
        }
        return $target;
    }

    public function listagem() {
        $sql = clone $this->_basicSearch;
        if (!empty($this->_orderFields)):
            foreach ($this->_orderFields AS $key => $order):
                $sql->order("$key $order");
            endforeach;
        endif;

        return $sql->query()->fetchAll();
    }

}
