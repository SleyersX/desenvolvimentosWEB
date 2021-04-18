<?php
/**
 * FinccustoList Listing
 * @author  <your name here>
 */
class FinccustoList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;

    const TABELA = 'Finccusto';
    const RETORNO = 'FinccustoList';
    const TITULO = 'Centros de Custos';
    const SIGLA = TRUE;     // TRUE or NULL
    const FORMULARIO = 'DefaultFormView';   // default
    const FORMEDT = 'DefaultForm';

    use Adianti\base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase(_DATABASE_);
        $this->setActiveRecord(self::TABELA);
        $this->setDefaultOrder('lower(descricao)', 'asc');
        //$this->setCriteria();
        $this->setNoQueryDelete();
        $this->setNoMsgAfterDelete();

        TSession::setValue('TABELA', self::TABELA);
        TSession::setValue('RETORNO', self::RETORNO);
        TSession::setValue('TITULO', self::TITULO);
        TSession::setValue('SIGLA', self::SIGLA);
        TSession::setValue('FORMULARIO', self::FORMULARIO);

        //$this->addFilterField('id', '=', 'id');
        $this->addFilterField('lower(descricao)', 'like', strtolower('descricao'));
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_FinccustoList');
        $this->form->setFormTitle(self::TITULO);

        // create the form fields
        $id = new TEntry('id');
        $id->setSize('50');
        $descricao = new TEntry('descricao');
        $descricao->setSize('100%');

        // add the fields
        //$this->form->addFields( [ new TLabel('ID') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Descrição') ], [ $descricao ] );

        $this->form->setData( TSession::getValue('FinccustoList_filter_data') );
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addActionLink(_t('New'), new TAction([self::FORMEDT, 'onClear']), 'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $column_id = new TDataGridColumn('id', 'ID', 'left', '10%');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        if(self::SIGLA) {
            $column_sigla = new TDataGridColumn('sigla', 'Sigla', 'left');            
        }
        $column_descricao->setAction(new TAction([$this, 'onReload']), ['order' => 'descricao']);

        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        if(self::SIGLA) {
            $this->datagrid->addColumn($column_sigla);
        }

        // inline editing
        $descricao_edit = new TDataGridAction([$this, 'onInlineEdit']);
        $descricao_edit->setField('id');
        $column_descricao->setEditAction($descricao_edit);
        
        if(self::SIGLA) 
        {
            $sigla_edit = new TDataGridAction([$this, 'onInlineEdit']);
            $sigla_edit->setField('id');
            $column_sigla->setEditAction($sigla_edit);
        }

        // create EDIT action
        $action_edit = new TDataGridAction([self::FORMEDT, 'onEdit']);
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);

        // create DELETE action
        $action_del = new TDataGridAction([$this, 'onDelete']);
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 80%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        parent::add($container);
    }

    public function Delete($param)
    {
        try
        {
            $key=$param['key'];
            TTransaction::open($this->database);

            $count = Finlanca::where('finccusto_id', '=', $param['id'])->count();
            if( $count > 0 ) {
                throw new Exception('Este Centro de Custos possui lançamentos vinculados a ele, não pode ser excluido');
            }
            else {
                $class = $this->activeRecord;
                $object = new $class($key, FALSE);
                $object->delete();
            }

            TTransaction::close();
            $this->onReload( $param );
            if(is_null( $this->noMsgOnDelete ))
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
