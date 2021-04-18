<?php
/**
 * FinpagrecList Listing
 * @author  <your name here>
 */
class FinpagrecList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Finpagrec');
        $this->form->setFormTitle('Contas a Pagar/Receber');
        
        // create the form fields
        $id = new TEntry('id');
        $button = TButton::create('btndirect', [$this, 'onEditID'], '', 'fa:pencil-square-o blue fa-lg');  
        $tipo = new TCombo('tipo');
        $finccusto_id = new TDBCombo('finccusto_id', _DATABASE_, 'Finccusto', 'id', 'descricao');
        $finfilial_id = new TDBCombo('finfilial_id', _DATABASE_, 'Finfilial', 'id', 'descricao');
        $dtvc1 = new TDate('dtvc1');    // dtvenc
        $dtvc2 = new TDate('dtvc2');
        $dtbx1 = new TDate('dtbx1');    // dtbaixa
        $dtbx2 = new TDate('dtbx2');
        $nrdoc = new TEntry('nrdoc');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', _DATABASE_, 'Pessoa', 'id', 'nome');
        $referencia = new TEntry('referencia');

        // set sizes
        $id->setSize('70%');
        $tipo->setSize('100%');
        $finccusto_id->setSize('100%');
        $finfilial_id->setSize('100%');
        $dtvc1->setSize('40%');
        $dtvc2->setSize('40%');
        $dtbx1->setSize('40%');
        $dtbx2->setSize('40%');
        $nrdoc->setSize('100%');
        $pessoa_id->setSize('100%');
        $referencia->setSize('100%');

        // custom
        $tipo->addItems(['P'=>'Pagar','R'=>'Receber']);
        $dtvc1->setMask('dd/mm/yyyy'); 
        $dtvc1->setDatabaseMask('yyyy-mm-dd');
        $dtvc2->setMask('dd/mm/yyyy'); 
        $dtvc2->setDatabaseMask('yyyy-mm-dd');
        $dtbx1->setMask('dd/mm/yyyy'); 
        $dtbx1->setDatabaseMask('yyyy-mm-dd');
        $dtbx2->setMask('dd/mm/yyyy'); 
        $dtbx2->setDatabaseMask('yyyy-mm-dd');
        $pessoa_id->setMinLength(1);

        // add the fields
        $row = $this->form->addFields(
            [$lb0=new TLabel('ID'), $id, $button],
            [$lb1=new TLabel('Pagar/Receber'), $tipo],
            [$lb3=new TLabel('Centro de Custos'), $finccusto_id],
            [$lb4=new TLabel('Filial'), $finfilial_id]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb3->setSize('100%');
        $lb4->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields(            
            [$lb0=new TLabel('Período Vencimento'), $dtvc1, new TLabel('até'), $dtvc2],
            [$lb1=new TLabel('Período de Baixas'),  $dtbx1, new TLabel('até'), $dtbx2],
            [$lb3=new TLabel('Nº Documento'), $nrdoc]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb3->setSize('100%');
        $row->layout = ['col-sm-5', 'col-sm-5', 'col-sm-2'];

        $row = $this->form->addFields(
            [$lb0=new TLabel('Credor/Devedor'), $pessoa_id],            
            [$lb2=new TLabel('Referência'), $referencia]);
        $lb0->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];            

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Finpagrec_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FinpagrecForm', 'onEdit']), 'fa:plus green');
        $btn = $this->form->addAction(_t('Clear'),  new TAction([$this, 'onClear']), 'fa:eraser');
        $btn->title = 'Limpa os filtros do formulario';
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_tipo = new TDataGridColumn('tipo', 'Situação', 'center');
        $column_pessoa_id = new TDataGridColumn('pessoa->nome', 'Credor/Devedor', 'left');
        $column_pessoa_id->setAction(new TAction([$this, 'onReload']), ['order' => 'pessoa_id']);
        $column_referencia = new TDataGridColumn('referencia', 'Referência', 'left');
        $column_dtvenc = new TDataGridColumn('dtvenc', 'Vencto', 'left');
        $column_dtvenc->setAction(new TAction([$this, 'onReload']), ['order' => 'dtvenc']);
        $column_nrdoc = new TDataGridColumn('nrdoc', 'Nrdoc', 'left');
        $column_parcela = new TDataGridColumn('nrparc', 'Parcela', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_nrdoc->setAction(new TAction([$this, 'onReload']), ['order' => 'nrdoc']);
        $column_dtbaixa = new TDataGridColumn('dtbaixa', 'Baixa/Receb', 'center');
        $column_dtbaixa->setAction(new TAction([$this, 'onReload']), ['order' => 'dtbaixa']);

        // grid transformer
        $column_tipo->setTransformer( function($value, $object, $row) {   
            if( empty($object->dtbaixa) ) {
                $class = $value=='P' ? 'danger'  : 'success';
                $label = $value=='P' ? 'a Pagar' : 'a Receber';
            }
            else {
                $class = 'warning';
                $label = $value=='P' ? 'Pago' : 'Recebido';
            }
            $div = new TElement('span');
            $div->class="label label-{$class}";
            $div->style="text-shadow:none; font-size:12px; font-weight:lighter";
            $div->add($label);
            return $div;
        });
        $column_dtvenc->setTransformer( function($value) {
             $date = new DateTime($value);
             return $date->format('d/m/Y');
        });
        $column_parcela->setTransformer( function($value, $object) {
            $ret = '';
            if( !empty($object->nrparc) OR !empty($object->totparc)) {
                $ret = $object->nrparc.' / '.$object->totparc;
            }
            return $ret;
        });
        $column_valor->setTransformer( function($value) {
            return TBLFuncoes::fmtNumber($value);
        });
        $column_dtbaixa->setTransformer( function($value) {
            if($value) {
                $date = new DateTime($value);
                return $date->format('d/m/Y');                
            }
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_pessoa_id);
        $this->datagrid->addColumn($column_referencia);
        $this->datagrid->addColumn($column_nrdoc);
        $this->datagrid->addColumn($column_parcela);
        $this->datagrid->addColumn($column_dtvenc);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_dtbaixa);

        $action_edit = new TDataGridAction([$this, 'onEditX']);
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setFields(['id','dtbaixa']);
        $this->datagrid->addAction($action_edit);

        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $action_del->setDisplayCondition([$this, 'displayBaixar']);
        $this->datagrid->addAction($action_del);
        
        $action_bxa = new TDataGridAction(['FinpagrecBxForm', 'onBaixar']);
        $action_bxa->setLabel('Baixar título');
        $action_bxa->setImage('fa:money green fa-lg');
        $action_bxa->setField('id');
        $action_bxa->setDisplayCondition([$this, 'displayBaixar']);
        $this->datagrid->addAction($action_bxa);

        $action_est = new TDataGridAction([$this, 'onEstorno']);
        $action_est->setLabel('Estornar baixa ou recebimento');
        $action_est->setImage('fa:reply orange');
        $action_est->setField('id');
        $action_est->setDisplayCondition([$this, 'displayEstorno']);
        $this->datagrid->addAction($action_est);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->enableCounters();
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));  
        parent::add($container);
    }

    public function onClear($param)
    {
        $this->form->clear();
    }
    
    public function onSearch()
    {
        $data = $this->form->getData();

        // clear session filters
        TSession::setValue('FinpagrecList_filter_id',   NULL);
        TSession::setValue('FinpagrecList_filter_tipo',   NULL);
        TSession::setValue('FinpagrecList_filter_pessoa_id',   NULL);
        TSession::setValue('FinpagrecList_filter_dtvenc',   NULL);
        TSession::setValue('FinpagrecList_filter_nrdoc',   NULL);
        TSession::setValue('FinpagrecList_filter_referencia',   NULL);
        TSession::setValue('FinpagrecList_filter_finccusto_id',   NULL);
        TSession::setValue('FinpagrecList_filter_finfilial_id',   NULL);
        TSession::setValue('FinpagrecList_filter_dtbaixa',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id");
            TSession::setValue('FinpagrecList_filter_id', $filter);
        }
        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', '=', "{$data->tipo}");
            TSession::setValue('FinpagrecList_filter_tipo', $filter);
        }
        if (isset($data->pessoa_id) AND ($data->pessoa_id)) {
            $filter = new TFilter('pessoa_id', '=', "$data->pessoa_id");
            TSession::setValue('FinpagrecList_filter_pessoa_id', $filter);
        }
        TSession::setValue('FinpagrecList_filter_dtvenc', TBLFuncoes::makeFilterIntervalDate('dtvenc', $data->dtvc1, $data->dtvc2));

        if (isset($data->nrdoc) AND ($data->nrdoc)) {
            $filter = new TFilter('nrdoc', 'like', "%{$data->nrdoc}%");
            TSession::setValue('FinpagrecList_filter_nrdoc', $filter);
        }
        if (isset($data->referencia) AND ($data->referencia)) {
            $filter = new TFilter('referencia', 'like', "%{$data->referencia}%");
            TSession::setValue('FinpagrecList_filter_referencia',   $filter);
        }
        if (isset($data->finccusto_id) AND ($data->finccusto_id)) {
            $filter = new TFilter('finccusto_id', '=', "$data->finccusto_id");
            TSession::setValue('FinpagrecList_filter_finccusto_id', $filter);
        }
        if (isset($data->finfilial_id) AND ($data->finfilial_id)) {
            $filter = new TFilter('finfilial_id', '=', "$data->finfilial_id");
            TSession::setValue('FinpagrecList_filter_finfilial_id', $filter);
        }
        TSession::setValue('FinpagrecList_filter_dtbaixa', TBLFuncoes::makeFilterIntervalDate('dtbaixa', $data->dtbx1, $data->dtbx2));
        $this->form->setData($data);
        TSession::setValue('Finpagrec_filter_data', $data);
        
        $param = array();
        $param['offset']=0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $repository = new TRepository('Finpagrec');
            $limit = 10;
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'dtvenc';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('FinpagrecList_filter_id')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_id'));
            }
            if (TSession::getValue('FinpagrecList_filter_tipo')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_tipo'));
            }
            if (TSession::getValue('FinpagrecList_filter_pessoa_id')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_pessoa_id'));
            }
            if (TSession::getValue('FinpagrecList_filter_dtvenc')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_dtvenc'));
            }
            if (TSession::getValue('FinpagrecList_filter_nrdoc')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_nrdoc'));
            }
            if (TSession::getValue('FinpagrecList_filter_referencia')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_referencia'));
            }
            if (TSession::getValue('FinpagrecList_filter_finccusto_id')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_finccusto_id'));
            }
            if (TSession::getValue('FinpagrecList_filter_finfilial_id')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_finfilial_id'));
            }
            if (TSession::getValue('FinpagrecList_filter_dtbaixa')) {
                $criteria->add(TSession::getValue('FinpagrecList_filter_dtbaixa'));
            }
            $objects = $repository->load($criteria, FALSE);
            if (is_callable($this->transformCallback)) {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
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
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public static function Delete($param)
    {
        try
        {
            $key=$param['key'];
            TTransaction::open(_DATABASE_);
            $object = new Finpagrec($key, FALSE);
            $object->delete();
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) ) {
            if (func_num_args() > 0) {
                $this->onReload( func_get_arg(0) );
            }
            else {
                $this->onReload();
            }
        }
        parent::show();
    }

    public function onEditID( $param ) 
    {        
        if( isset($param['id']) && $param['id'] )
        {
            try
            {
                TTransaction::open(_DATABASE_);
                $lanc = Finpagrec::find($param['id']);
                if( !$lanc ) {
                    throw new Exception('ID não localizado');
                }
                $param['key'] = $param['id'];
                AdiantiCoreApplication::loadPage('FinpagrecForm', 'onEdit', $param);
                TTransaction::close();
            }
            catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }        
        }
    }    
    public function displayBaixar($object)
    {
        return !($object->dtbaixa);
    }

    public function displayEstorno($object)
    {
        return ($object->dtbaixa);
    }

    public function onEditX($param)
    {
        TSession::setValue('titbaixado', NULL);
        if( $param['dtbaixa'] )
            TSession::setValue('titbaixado', $param['dtbaixa']);
        TApplication::loadPage('FinpagrecForm', 'onEdit', $param);
    }

    public function onEstorno($param)
    {
        $actS = new TAction([$this, 'EstornarBaixa'], $param);
        $actN = new TAction([$this, 'onReload'], $param);
        new TQuestion('Confirma o estorno da baixa deste título ?', $actS, $actN);
    }

    public function EstornarBaixa($param)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $object = new Finpagrec($param['id']);
            $objLanc = new Finlanca($object->finlanca_id);
            $objLanc->delete();

            $object->dtbaixa = NULL;
            $object->finlanca_id = NULL;
            $object->store();

            TTransaction::close();
            new TMessage('info', 'Baixa estornada');
            $this->onReload($param);
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }    
}
