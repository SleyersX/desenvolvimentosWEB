<?php
/**
 * FinplanocontaList Listing
 * @author  <your name here>
 */
class FinplanocontaList extends TPage
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
        $this->setActiveRecord('Finplanoconta');
        $this->setDefaultOrder('ordem', 'asc');
        // $this->setCriteria($criteria); // define a standard filter

        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('ordem', 'like', 'ordem');
        $this->addFilterField('nome', 'like', 'nome');
        $this->addFilterField('nomecurto', 'like', 'nomecurto');
        $this->addFilterField('tipolanc', 'like', 'tipo');
        $this->addFilterField('ativa', 'like', 'ativa');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Finplanoconta');
        $this->form->setFormTitle('Plano de Contas');

        // create the form fields
        $id = new TEntry('id');
        $ordem = new TEntry('ordem');
        $nome = new TEntry('nome');
        $nomecurto = new TEntry('nomecurto');
        $tipolanc = new TCombo('tipolanc');
        $ativa = new TCombo('ativa');

        // set sizes
        $id->setSize('50%');
        $ordem->setSize('100%');
        $nome->setSize('100%');
        $nomecurto->setSize('100%');
        $tipolanc->setSize('100%');
        $ativa->setSize('100%');

        // custom
        $ordem->setMask(_NIVEISPC_);
        $ordem->placeholder = 'Formato: '._NIVEISPC_;
        $ordem->addValidation('Nº de Ordem', new TNrOrdemValidator);
        //$ativa->setLayout('horizontal');
        $ativa->addItems(['Y'=>'Sim','N'=>'Não']);
        $nome->setMaxLength(50);
        $nome->addValidation('Descrição', new TRequiredValidator);
        $nomecurto->setMaxLength(30);
        $nomecurto->addValidation('Descrição curta', new TRequiredValidator);
        $tipolanc->addValidation('Descrição', new TRequiredValidator);
        $tipolanc->addItems(['D'=>'Debitos','C'=>'Creditos','T'=>'Débitos/Créditos']);

        // add the fields
        $row = $this->form->addFields( 
                [ $l0=new TLabel('ID'), $id ],
                [ $l1=new TLabel('Conta ativa ?'), $ativa ],
                [ $l2=new TLabel('Nº de Ordem'), $ordem ],
                [ $l3=new TLabel('Lançamentos aceitos'), $tipolanc ]);
        $l0->setSize('100%');
        $l1->setSize('100%');
        $l2->setSize('100%');
        $l3->setSize('100%');
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];
        
        $row=$this->form->addFields(
            [new TLabel('Descrição'), $nome],
            [new TLabel('Descrição curta'), $nomecurto]);
        $row->layout = ['col-sm-7','col-sm-5'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Finplanoconta_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FinplanocontaForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction('Listagem',  new TAction(['FinPlanoContasReport', 'onShow']), 'fa:print blue');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_ordem = new TDataGridColumn('ordem', 'Nº Ordem', 'left');
        $column_nome = new TDataGridColumn('nome', 'Descrição', 'left');
        $column_nomecurto = new TDataGridColumn('nomecurto', 'Descrição Abreviada', 'left');
        $column_tipolanc = new TDataGridColumn('tipolanc', 'Tipo Lanc', 'center');
        $column_ativa = new TDataGridColumn('ativa', 'Ativa', 'center');

        $column_ativa->setTransformer( function($value, $object, $row) {
            $class = ($value=='N') ? 'danger' : 'success';
            $label = ($value=='N') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });

        $column_tipolanc->setTransformer( function($value) {
            if($value == 'D') $ret='Débitos';
            elseif($value == 'C') $ret='Créditos';
            else $ret='Deb/Cred';
            return $ret;
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_ordem);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_nomecurto);
        $this->datagrid->addColumn($column_tipolanc);
        $this->datagrid->addColumn($column_ativa);

        // creates the datagrid column actions
        $column_ordem->setAction(new TAction([$this, 'onReload']), ['order' => 'ordem']);
        $column_nome->setAction(new TAction([$this, 'onReload']), ['order' => 'nome']);

        // inline editing
        $nome_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        $nome_edit->setField('id');
        $column_nome->setEditAction($nome_edit);
        
        $nomecurto_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        $nomecurto_edit->setField('id');
        $column_nomecurto->setEditAction($nomecurto_edit);
        
        // create EDIT action
        $action_edit = new TDataGridAction(['FinplanocontaForm', 'onEdit']);
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setFields(['id','ordem']);
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        parent::add($container);
    }

    public function Delete($param)
    {        
        try
        {
            TTransaction::open(_DATABASE_);
            $regs = Finplanoconta::where('ordem','LIKE', "{$param['ordem']}%")->load();
            if(sizeof($regs) > 1) {
                throw new Exception("Esta conta não pode ser excluida pois possui outra(s) vinculada(s) a ela");
            }
            else
            {
                $count = Finlanca::where('finplanoconta_id', '=', $param['id'])->count();
                if( $count > 0 ) {
                    throw new Exception('Esta conta possui lançamentos vinculados a ela, não pode ser excluida');
                }
                else {
                    $reg = new Finplanoconta($param['id']);
                    $reg->delete();
                }
                TTransaction::close();
                $this->onReload( $param );
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }  

    public function onShow() {}
}
