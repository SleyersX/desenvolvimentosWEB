<?php
/**
 * FincontaFormList Registration
 * @author  <your name here>
 */
class FincontaFormList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    
    use Adianti\Base\AdiantiStandardFormListTrait;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase(_DATABASE_);
        $this->setActiveRecord('Finconta');
        $this->setDefaultOrder('descricao', 'asc');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Conta');
        $this->form->setFormTitle('Contas Financeiras');
        
        // create the form fields
        $id = new TEntry('id');
        $sigla = new TEntry('sigla');
        $descricao = new TEntry('descricao');
        $ativa = new TRadioGroup('ativa');
        $dtini = new TDate('dtini');
        $sdini = new TEntry('sdini');

        // set sizes
        $id->setSize('40');
        $sigla->setSize('100%');
        $descricao->setSize('100%');
        $dtini->setSize('100%');
        $sdini->setSize('100%');

        // custom
        $id->setEditable(FALSE);
        $sigla->setMaxLength(10);
        $sigla->forceUpperCase();
        $descricao->setMaxLength(40);
        $dtini->setMask('dd/mm/yyyy'); 
        $dtini->setDatabaseMask('yyyy-mm-dd'); 
        $ativa->addItems(['Y'=>'&nbsp;&nbsp;Sim&nbsp;&nbsp;&nbsp;', 'N'=>'&nbsp;&nbsp;Não']);
        $ativa->setLayout('horizontal');
        $sdini->setNumericMask(2,',','.', TRUE);

        // validations
        $sigla->addValidation('Sigla', new TRequiredValidator);
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $ativa->addValidation('Conta ativa', new TRequiredValidator);
        $dtini->addValidation('Data Inicial', new TRequiredValidator);
        $sdini->addValidation('Saldo Inicial', new TRequiredValidator);


        // add the fields
        $this->form->addFields( [ new TLabel('ID') ], [ $id ],
                                [ new TLabel('Sigla') ], [ $sigla ],
                                [ new TLabel('Ativa') ], [ $ativa ] );
        $this->form->addFields( [ new TLabel('Descricao') ], [ $descricao ],
                                [ new TLabel('Data Inicial') ], [ $dtini ],
                                [ new TLabel('Saldo Inicial') ], [ $sdini ]  );
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_sigla = new TDataGridColumn('sigla', 'Sigla', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_ativa = new TDataGridColumn('ativa', 'Ativa', 'left');
        $column_dtini = new TDataGridColumn('dtini', 'Data Inicial', 'left');
        $column_dtini->setTransformer( function($value) {
            return '<i class="fa fa-calendar red"/> '.TDate::date2br(substr($value,0,10));
        });
        $column_sdini = new TDataGridColumn('sdini', 'Saldo Inicial', 'right');
        $column_sdini->setTransformer( function($value) {
            return TBLFuncoes::fmtNumber($value, 2, 'R$');
        });
        $column_ativa->setTransformer( function($value, $object, $row) {
            $class = ($value=='N') ? 'danger' : 'success';
            $label = ($value=='N') ? _t('No') : _t('Yes');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });        

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_sigla);
        $this->datagrid->addColumn($column_ativa);
        $this->datagrid->addColumn($column_dtini);
        $this->datagrid->addColumn($column_sdini);

        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        $action1->setLabel(_t('Edit'));
        $action1->setImage('fa:pencil-square-o blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setLabel(_t('Delete'));
        $action2->setImage('fa:trash-o red fa-lg');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
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
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }

    public function onDelete($param)
    {
        try
        {
            TTransaction::Open(_DATABASE_);
            $count = Finlanca::where('finconta_id', '=', $param['id'])->count();
            TTransaction::close();
            if( $count > 0 ) {
                throw new Exception('Esta conta possui lançamentos vinculados a ela, não pode ser excluida');
            }
            else {
                $action = new TAction([$this, 'Delete'], $param);
                new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
        }
        catch (Exception $e){
            new TMessage('error', TBLFuncoes::msgErrors($e));
        }
        return;
    }

}
