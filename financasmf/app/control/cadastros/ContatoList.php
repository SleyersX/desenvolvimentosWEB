<?php
/**
 * ContatoList Listing
 * @author  <your name here>
 */
class ContatoList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        $this->setDatabase(_DATABASE_);
        $this->setActiveRecord('Pessoa');
        $this->setDefaultOrder('nome', 'asc');
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('nome', 'like', 'nome');
        $this->addFilterField('cidade', 'like', 'cidade');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Pessoa');
        $this->form->setFormTitle('Pessoas & Empresas');

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cidade = new TEntry('cidade');

        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');
        $cidade->setSize('100%');

        // add the fields
        $row = $this->form->addFields( 
            [ $lb0=new TLabel('ID'), $id],
            [ $lb1=new TLabel('Nome'), $nome],
            [ $lb2=new TLabel('Cidade'), $cidade]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-4'];

     
        $this->form->setData( TSession::getValue('Pessoa_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ContatoForm', 'onEdit']), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_telefone = new TDataGridColumn('telefone', 'Telefone', 'left');
        $column_celular = new TDataGridColumn('celular', 'Celular', 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_contato = new TDataGridColumn('contato', 'Contato', 'left');
        $column_cidade = new TDataGridColumn('cidade', 'Cidade/Estado', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_telefone);
        $this->datagrid->addColumn($column_celular);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_contato);
        $this->datagrid->addColumn($column_cidade);
        
        // create EDIT action
        $action_edit = new TDataGridAction(['ContatoForm', 'onEdit']);
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
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
        $container->style = 'width: 100%';
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

            $count = Finlanca::where('pessoa_id', '=', $param['id'])->count();
            if( $count > 0 ) {
                throw new Exception('Este contato possui lançamentos vinculados a ele, não pode ser excluido');
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
