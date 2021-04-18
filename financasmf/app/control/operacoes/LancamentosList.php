<?php
/**
 * LancamentosList Listing
 * @author  Adalberto Lima Vitorino (blsistemas50@gmail.com)
 */
class LancamentosList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_Lancamentos');
        $this->form->setFormTitle('Lançamentos Financeiros');

        // create the form fields
        $id = new TEntry('id');
        $button = TButton::create('btndirect', [$this, 'onEditID'], '', 'fa:pencil-square-o blue fa-lg');  
        $data1 = new TDate('data1');
        $data2 = new TDate('data2');
        $finconta_id = new TDBCombo('finconta_id', _DATABASE_, 'Finconta', 'id', 'descricao','descricao');
        $finccusto_id = new TDBCombo('finccusto_id', _DATABASE_, 'Finccusto', 'id', 'descricao','descricao');
        $finfilial_id = new TDBCombo('finfilial_id', _DATABASE_, 'Finfilial', 'id', 'descricao','descricao');
        $finplanoconta_id = new TDBCombo('finplanoconta_id',_DATABASE_,'Finplanoconta','id','nome_fmt','ordem');
        $pessoa_id = new TDBUniqueSearch('pessoa_id',_DATABASE_,'Pessoa','id','nome','nome');
        $descricao = new TEntry('descricao');
        $debcred = new TRadioGroup('debcred');
        $nrdoc = new TEntry('nrdoc');
        $valor = new TEntry('valor');

        // sizes
        $id->setSize('70%'); 
        $finconta_id->setSize('100%'); 
        $descricao->setSize('100%'); 
        $finccusto_id->setSize('100%'); 
        $finfilial_id->setSize('100%'); 
        $finplanoconta_id->setSize('100%'); 
        $nrdoc->setSize('100%'); 
        $valor->setSize('100%'); 
        $data1->setSize('45%'); 
        $data2->setSize('45%'); 
        
        $data1->setMask('dd/mm/yyyy'); 
        $data1->setDatabaseMask('yyyy-mm-dd');
        $data2->setMask('dd/mm/yyyy'); 
        $data2->setDatabaseMask('yyyy-mm-dd');
        $finplanoconta_id->enableSearch();
        $valor->setNumericMask(2,',','.', TRUE);
        $debcred->addItems(['D'=>'Débitos','C'=>'Créditos']);
        $debcred->setLayout('horizontal');
        $pessoa_id->setMinLength(1);
        $pessoa_id->setSize('100%');
        $pessoa_id->setMask('{nome} ({nconhecido})');

        // add the fields
        $row = $this->form->addFields(
                        [$lb0=new TLabel('ID'), $id, $button],
                        [$lb1=new TLabel('Período'), $data1, new TLabel('até'), $data2],
                        [$lb2=new TLabel('Débitos/Créditos'), $debcred],
                        [$lb3=new TLabel('Nº Documento'), $nrdoc],
                        [$lb4=new TLabel('Valor'), $valor]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-2','col-sm-4','col-sm-2','col-sm-2','col-sm-2'];            
        $row = $this->form->addFields(
            [$lb0=new TLabel('Conta Financeira'), $finconta_id],
            [$lb1=new TLabel('Centro de Custos'), $finccusto_id],
            [$lb2=new TLabel('Conta Resumo'), $finplanoconta_id]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];            

        $row=$this->form->addFields(
            [$lb0=new TLabel('Descrição'), $descricao],
            [$lb1=new TLabel('Pessoa'), $pessoa_id],
            [$lb2=new TLabel('Filial'), $finfilial_id]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];            

        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue('Lancamentos_filter_data'));
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addAction(_t('New'), new TAction(['LancamentosForm','onEdit']), 'bs:plus-sign green');
        $this->form->addAction(_t('Clear'), new TAction([$this,'onClear']), 'fa:eraser blue');
        $this->form->addAction('Transf entre Contas',new TAction(['LancamentosTransf','onClear']),'fa:retweet blue');
        $this->form->addAction('Saldo de C/C',new TAction(['ConsultaSaldo','onClear']),'fa:bank green');

        $btn = $this->form->addAction('Ajuste', new TAction(['LancamentoAjResumo','onShow']), 'fa:wrench orange');
        $btn->title = 'Ajusta Contas Resumo em lançamentos';
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // creates the datagrid columns
        $column_check = new TDataGridColumn('check', '', 'center');
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_dtlanc = new TDataGridColumn('dtlanc', 'Data', 'left');
        $column_conta_id = new TDataGridColumn('finconta->sigla','Conta Financeira','left');
        $column_finccusto_id = new TDataGridColumn('finccusto->descricao','C.Custos', 'left');
        $column_finplanoconta_id = new TDataGridColumn('finplanoconta->ordem','C.Resumo', 'center');
        $column_descricao = new TDataGridColumn('descricao','Descrição','left');
        $column_nrdoc = new TDataGridColumn('nrdoc', 'Nº Docto', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_origem = new TDataGridColumn('origem', 'Orig', 'left');
        $column_origem->title = 'Origem do lançamento:<br>DG - Digitado<br>TF - Transferido entre C/C<br>PR - Contas a Pagar/Receber';

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_check);
        $this->datagrid->addColumn($column_origem);
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dtlanc);
        $this->datagrid->addColumn($column_conta_id);
        $this->datagrid->addColumn($column_finccusto_id);
        $this->datagrid->addColumn($column_finplanoconta_id);        
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_nrdoc);
        $this->datagrid->addColumn($column_valor);

        $column_origem->setTransformer( function($value) {
            $ret = ['','DG','TF','PR'];
            return $ret[$value];
        });

        $column_dtlanc->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_valor->setTransformer( function($value, $object, $row) {
            $class = ((float) $value < 0) ? 'danger' : 'info';
            $value = ((float) $value < 0) ? ((float) $value * -1):$value;
            $label = TBLFuncoes::fmtNumber($value, 2, 'R$');
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });
        
        // create EDIT action
        $action_edit = new TDataGridAction([$this, 'onEditView']);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setFields(['id','idlock']);
        //$action_edit->setDisplayCondition( [$this,'editLanc']);
        $this->datagrid->addAction($action_edit);
        
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $this->datagrid->disableDefaultClick();
        
        // put datagrid inside a form
        $this->formgrid = new TForm;
        $this->formgrid->add($this->datagrid);
        
        // creates the delete collection button
        $this->deleteButton = new TButton('delete_collection');
        $this->deleteButton->setAction(new TAction(array($this, 'onDeleteCollection')), AdiantiCoreTranslator::translate('Delete selected'));
        $this->deleteButton->setImage('fa:remove red');

        $this->formgrid->addField($this->deleteButton);
        
        $gridpack = new TVBox;
        $gridpack->style = 'width: 100%';
        $gridpack->add($this->formgrid);
        $gridpack->add($this->deleteButton)->style = 'background:whiteSmoke;border:1px solid #cccccc; padding: 3px;padding: 5px;';        
        $this->transformCallback = array($this, 'onBeforeLoad');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($gridpack);
        $container->add($this->pageNavigation);
        parent::add($container);
    }

    public function onClear($param)
    {
        $this->form->clear();
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();        
        TSession::setValue('LancamentosList_filter_id', NULL);
        TSession::setValue('LancamentosList_filter_dtlanc', NULL);
        TSession::setValue('LancamentosList_filter_conta_id', NULL);
        TSession::setValue('LancamentosList_filter_finplanoconta_id', NULL);
        TSession::setValue('LancamentosList_filter_finccusto_id', NULL);
        TSession::setValue('LancamentosList_filter_finfilial_id', NULL);
        TSession::setValue('LancamentosList_filter_descricao', NULL);
        TSession::setValue('LancamentosList_filter_nrdoc', NULL);
        TSession::setValue('LancamentosList_filter_valor', NULL);
        TSession::setValue('LancamentosList_filter_debcred', NULL);
        TSession::setValue('LancamentosList_filter_pessoa_id', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "{$data->id}");
            TSession::setValue('LancamentosList_filter_id', $filter);
        }
        TSession::setValue('LancamentosList_filter_dtlanc', TBLFuncoes::makeFilterIntervalDate('dtlanc', $data->data1, $data->data2));

        if (isset($data->finconta_id) AND ($data->finconta_id)) {
            $filter = new TFilter('finconta_id', 'like', "%{$data->finconta_id}%");
            TSession::setValue('LancamentosList_filter_conta_id', $filter);
        }
        if (isset($data->finplanoconta_id) AND ($data->finplanoconta_id)) {
            $filter = new TFilter('finplanoconta_id','=',"{$data->finplanoconta_id}");
            TSession::setValue('LancamentosList_filter_finplanoconta_id',$filter);
        }
        if (isset($data->finccusto_id) AND ($data->finccusto_id)) {
            $filter = new TFilter('finccusto_id','like',"%{$data->finccusto_id}%");
            TSession::setValue('LancamentosList_filter_finccusto_id', $filter);
        }
        if (isset($data->finfilial_id) AND ($data->finfilial_id)) {
            $filter = new TFilter('finfilial_id','like',"%{$data->finfilial_id}%");
            TSession::setValue('LancamentosList_filter_finfilial_id', $filter);
        }
        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao','like',"%{$data->descricao}%");
            TSession::setValue('LancamentosList_filter_descricao', $filter);
        }
        if (isset($data->nrdoc) AND ($data->nrdoc)) {
            $filter = new TFilter('nrdoc', 'like', "%{$data->nrdoc}%");
            TSession::setValue('LancamentosList_filter_nrdoc', $filter);
        }
        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%");
            TSession::setValue('LancamentosList_filter_valor', $filter);
        }
        if (isset($data->debcred) AND ($data->debcred)) {
            $filter = new TFilter('debcred', '=', "{$data->debcred}");
            TSession::setValue('LancamentosList_filter_debcred', $filter);
        }
        if (isset($data->pessoa_id) AND ($data->pessoa_id)) {
            $filter = new TFilter('pessoa_id', '=', "{$data->pessoa_id}");
            TSession::setValue('LancamentosList_filter_pessoa_id', $filter);
        }
        $this->form->setData($data);
        TSession::setValue('Lancamentos_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open(_DATABASE_);            
            $repository = new TRepository('Finlanca');
            $limit = 10;
            $criteria = new TCriteria;
            
            if (empty($param['order'])) {
                $param['order'] = 'dtlanc desc, id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('LancamentosList_filter_id')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_id'));
            }
            if (TSession::getValue('LancamentosList_filter_dtlanc')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_dtlanc'));
            }
            if (TSession::getValue('LancamentosList_filter_conta_id')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_conta_id'));
            }
            if (TSession::getValue('LancamentosList_filter_finplanoconta_id')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_finplanoconta_id'));
            }
            if (TSession::getValue('LancamentosList_filter_finccusto_id')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_finccusto_id'));
            }
            if (TSession::getValue('LancamentosList_filter_finfilial_id')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_finfilial_id'));
            }
            if (TSession::getValue('LancamentosList_filter_descricao')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_descricao'));
            }
            if (TSession::getValue('LancamentosList_filter_nrdoc')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_nrdoc'));
            }
            if (TSession::getValue('LancamentosList_filter_valor')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_valor'));
            }
            if (TSession::getValue('LancamentosList_filter_debcred')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_debcred'));
            }
            if (TSession::getValue('LancamentosList_filter_pessoa_id')) {
                $criteria->add(TSession::getValue('LancamentosList_filter_pessoa_id'));
            }
            $objects = $repository->load($criteria, FALSE);            
            if (is_callable($this->transformCallback)){
                call_user_func($this->transformCallback, $objects, $param);
            }            

            $this->datagrid->clear();
            if ($objects){
                foreach ($objects as $object){
                    if(!empty($object->idlock)) {
                        if( ($object->origem == 2 && $object->debcred == 'C') OR $object->origem == 3)
                            unset($object->check);
                    }
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onDeleteCollection( $param )
    {
        $data = $this->formgrid->getData();
        $this->formgrid->setData($data);
        
        if ($data)
        {
            $selected = array();
            
            foreach ($data as $index => $check)
            {
                if ($check == 'on') {
                    $selected[] = substr($index,5);
                }
            }            
            if ($selected)
            {
                $param['selected'] = json_encode($selected);
                $action = new TAction(array($this, 'deleteCollection'));
                $action->setParameters($param);
                new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
        }
    }
    
    public function deleteCollection($param)
    {
        $selected = json_decode($param['selected']);
        try
        {
            TTransaction::open(_DATABASE_);
            if ($selected)
            {
                foreach ($selected as $id) 
                {
                    $object = new Finlanca($id);
                    if(empty($object->idlock)) {
                        $object->delete( $id );
                    }
                    else {
                        Finlanca::where('idlock','=',$object->idlock)->delete();
                    }
                }
                $posAction = new TAction(array($this, 'onReload'));
                $posAction->setParameters( $param );
                new TMessage('info', AdiantiCoreTranslator::translate('Records deleted'), $posAction);
            }
            TTransaction::close();
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onBeforeLoad($objects, $param)
    {
        $deleteAction = $this->deleteButton->getAction();
        $deleteAction->setParameters($param);        
        $gridfields = array( $this->deleteButton );        
        foreach ($objects as $object)
        {
            $object->check = new TCheckButton('check' . $object->id);
            $object->check->setIndexValue('on');
            $gridfields[] = $object->check;
        }        
        $this->formgrid->setFields($gridfields);
    }

    public function editLanc( $object ) {
        return is_null($object->idlock);
    }

    public function delLanc( $object ) {
        return is_null($object->idlock);
    }

    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch'))))) {
            if (func_num_args() > 0){
                $this->onReload( func_get_arg(0) );
            }
            else{
                $this->onReload();
            }
        }
        parent::show();
    }

    public function onShow() {}

    public function onEditView($param)
    {
        TSession::setValue('locked', NULL);
        if(isset($param['idlock']) && $param['idlock'])
            TSession::setValue('locked', TRUE);
        TApplication::loadPage('LancamentosForm', 'onEdit', $param);
    }

    public function onEditID( $param ) 
    {        
        if( isset($param['id']) && $param['id'] )
        {
            try
            {
                TTransaction::open(_DATABASE_);
                $lanc = FinLanca::find($param['id']);
                if( !$lanc ) {
                    throw new Exception('ID não localizado');
                }
                $param['key'] = $param['id'];
                AdiantiCoreApplication::loadPage('LancamentosForm', 'onEdit', $param);
                TTransaction::close();
            }
            catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }        
        }
    }    

}
