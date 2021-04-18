<?php
/**
 * FinpagrecBxForm Form
 * @author  <your name here>
 */
class FinpagrecBxForm extends TPage
{
    protected $form;
    private $alertBox;
    
    public function __construct( $param )
    {
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
        $this->form->setFormTitle('Baixa de Titulos');

        // filter
        $filter = new TCriteria();
        $filter->add(new TFilter('ativa','=','Y'));

        // campos do contas a pagar/receber
        $id = new TEntry('id');
        $pagrec = new TEntry('pagrec');
        $nrdoctit = new TEntry('nrdoctit');
        $parcela = new TEntry('parcela');
        $dtvencto = new TDate('dtvenc');
        $valortit = new TEntry('valortit');
        $credordevedor = new TEntry('credordevedor');
        $referencia = new TEntry('referencia');        
        $veiculo = new TEntry('veiculo');
        // campos do lançamento financeiro
        $dtlanc = new TDate('dtlanc');
        $finconta_id = new TDBCombo('finconta_id', _DATABASE_, 'Finconta', 'id', 'descricao','descricao', $filter);
        $finccusto_id = new TDBCombo('finccusto_id', _DATABASE_, 'Finccusto', 'id', 'descricao','descricao');
        $finplanoconta_id = new TDBCombo('finplanoconta_id', _DATABASE_,'Finplanoconta','id','nome_fmt','ordem', $filter);
        $descricao = new TEntry('descricao');
        $nrdoc = new TEntry('nrdoc');
        $acresc = new TEntry('acresc');
        $desconto = new TEntry('desconto');
        $valor = new TEntry('valor');
        $obs = new TEntry('obs');

        $pessoa_id = new THidden('pessoa_id');

        // sizes
        $id->setSize('100%');
        $nrdoctit->setSize('100%');
        $parcela->setSize('100%');
        $referencia->setSize('100%');
        $dtvencto->setSize('100%');
        $valortit->setSize('100%');
        $credordevedor->setSize('100%');
        //
        $dtlanc->setSize('100%');
        $finconta_id->setSize('100%');
        $finccusto_id->setSize('100%');
        $finplanoconta_id->setSize('100%');
        $descricao->setSize('100%');
        $valor->setSize('100%');        
        $acresc->setSize('100%');
        $desconto->setSize('100%');
        $obs->setSize('96%');

        // custom        
        $exit_Liquido = new TAction(array($this, 'onVlLiquido'));

        $id->setEditable(FALSE);
        $pagrec->setEditable(FALSE);
        $nrdoctit->setEditable(FALSE);
        $parcela->setEditable(FALSE);
        $dtvencto->setEditable(FALSE);
        $dtvencto->setMask('dd/mm/yyyy'); 
        $dtvencto->setDatabaseMask('yyyy-mm-dd');
        $valortit->setEditable(FALSE);
        $valortit->setNumericMask(2,',','.', true);
        $credordevedor->setEditable(FALSE);
        $referencia->setEditable(FALSE);
        //
        $dtlanc->setMask('dd/mm/yyyy'); 
        $dtlanc->setDatabaseMask('yyyy-mm-dd');
        $finplanoconta_id->enableSearch();
        $descricao->setMaxLength(80);

        $acresc->setNumericMask(2,',','.', true);
        $acresc->setExitAction($exit_Liquido);
        $desconto->setNumericMask(2,',','.', TRUE);
        $desconto->setExitAction($exit_Liquido);
        $valor->setEditable(FALSE);
        $valor->setNumericMask(2,',','.', TRUE);

        $nrdoc->setTip('Nº documento que efetivou o pagamento/recebimento');

        // validation
        $dtlanc->addValidation('Data', new TRequiredValidator);
        $finconta_id->addValidation('Conta Financeira', new TRequiredValidator);
        $finccusto_id->addValidation('Centro de Custos', new TRequiredValidator);
        $finplanoconta_id->addValidation('Conta Resumo', new TRequiredValidator);
        $descricao->addValidation('Descrição', new TRequiredValidator);

        // add the fields
        $row = $this->form->addFields(
                [$lb0=new TLabel('ID'), $id, $pessoa_id],
                [$lb1=new TLabel('Título'), $pagrec],
                [$lb2=new TLabel('Nº Documento'), $nrdoctit],
                [$lb3=new TLabel('Parcela'), $parcela],
                [$lb4=new TLabel('Vencimento'), $dtvencto],
                [$lb5=new TLabel('Valor do titulo','blue',12,'B'), $valortit]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $lb3->setSize('100%');
        $lb4->setSize('100%');
        $lb5->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields(
                [$lb0=new TLabel('Credor/Devedor'), $credordevedor],
                [$lb1=new TLabel('Referência'), $referencia]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $row->layout = ['col-sm-4', 'col-sm-4'];
        //$row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $this->form->addFields( [new TLabel('Dados da Baixa','blue','12','B')]);
        $row = $this->form->addFields(
            [ $l1 = new TLabel('Data','red'),  $dtlanc ],
            [ $l2 = new TLabel('Conta Financeira','red'), $finconta_id ],
            [ $l3 = new TLabel('Centro de Custos','red'), $finccusto_id],
            [ $l4 = new TLabel('Conta Resumo','red'), $finplanoconta_id]);
        $l1->setSize('100%');
        $l2->setSize('100%');
        $l3->setSize('100%');
        $l4->setSize('100%');
        $row->layout = ['col-sm-2','col-sm-3','col-sm-3','col-sm-4'];

        $row = $this->form->addFields(
            [ $l0 = new TLabel('Descrição','red'), $descricao ],            
            [ $l1 = new TLabel('Nº Documento'), $nrdoc ],
            [ $l2 = new TLabel('Descontos'), $desconto ],
            [ $l3 = new TLabel('Acréscimos'), $acresc ],
            [ $l4 = new TLabel('Valor Liquido'), $valor ]);
        $l0->setSize('100%');
        $l1->setSize('100%');
        $l2->setSize('100%');
        $l3->setSize('100%');
        $l4->setSize('100%');
        $row->layout = ['col-sm-4','col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        $this->form->addFields([ new TLabel('Obs','blue',10), $obs]);
       
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this,'onSave']),  '
            fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Back'), new TAction(['FinpagrecList', 'onReload']), 'fa:arrow-left red');

        // vertical box container
        $this->alertBox = new TElement('div');
        $container = new TVBox;
        $container->style = 'width: 90%';
        //$container->add(TBreadCrumb::create(['Operações','Finanças','Baixa de Títulos']));
        $container->add($this->alertBox);
        $container->add($this->form);
        parent::add($container);
    }

    public function onClear($param){
        $this->form->clear(TRUE);
    }

    public function onMsg($tipo, $msg) 
    {
        $this->alertBox->add(new TAlert($tipo, $msg));
        TScript::create("window.scrollTo(0, 0);");
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $this->form->validate();
            $data = $this->form->getData();

            $object = new Finlanca;
            $object->fromArray( (array) $data);

            $object->debcred = 'C';
            if(substr($param['pagrec'],2,1) == 'P') {
                $object->valor = (float) $object->valor * -1;
                $object->debcred = 'D';
            }

            $cf = new Finconta($object->conta_id);
            if($data->dtlanc < $cf->dtini)
                throw new Exception("Data inválida ! Lançamentos para esta conta devem ser a partir de ".TDate::date2br($cf->dtini));

            $pc = new Finplanoconta($object->finplanoconta_id);
            if(strlen($pc->ordem) < strlen(_NIVEISPC_))
                throw new Exception("Conta Resumo inválida !");

            if($pc->tipolanc <> 'T') {
                if($pc->tipolanc == 'D' && $object->debcred <> 'D' )
                    throw new Exception("São permitidos apenas lançamentos de <strong>DÉBITO</strong> para esta Conta Resumo");
                elseif($pc->tipolanc == 'C' && $object->debcred <> 'C' )
                    throw new Exception("São permitidos apenas lançamentos de <strong>CRÉDITO</strong> para esta Conta Resumo");
            }

            $object->id = NULL;
            $object->origem = 3;
            $object->idlock = substr(md5(date('Y-m-d H:i:s').$data->valor),0,40);            
            $object->store();

            // Atualizar o registro de Contas Pag/Rec 
            $pagrec = new Finpagrec($param['id']);
            $pagrec->finlanca_id = $object->id;
            $pagrec->dtbaixa = $object->dtlanc;
            $pagrec->store();

            $this->form->setData($data);
            TTransaction::close();
            $this->onMsg('success', 'Título baixado com sucesso !');
            TApplication::gotoPage('FinpagrecList', 'onReload');
        }
        catch (Exception $e)
        {            
            $this->onMsg('danger', $e->getMessage());
            $this->form->setData( $this->form->getData());
            TTransaction::rollback();
        }
    }
    
    public function onBaixar( $param )
    {
        try
        {
            if (isset($param['key'])) 
            {
                $key = $param['key'];
                TTransaction::open(_DATABASE_);
                $object = new Finpagrec($key);
                $obj = new stdClass;
                $obj->id = $object->id;
                $obj->pagrec = $object->tipo == 'P' ? 'A Pagar':'A Receber';
                $obj->nrdoctit = $object->nrdoc;
                $obj->parcela = '';
                if($object->nrparc or $object->totparc)
                    $obj->parcela = $object->nrparc.'/'.$object->totparc;
                $obj->valortit = $object->valor;
                $obj->dtvenc = $object->dtvenc;
                $obj->credordevedor = $object->pessoa->nome;
                $obj->referencia = $object->referencia;

                $obj->dtlanc = date('d/m/Y');
                $obj->nrdoc = '';
                $obj->descricao = $object->referencia;
                $obj->finccusto_id = $object->finccusto_id;
                $obj->finplanoconta_id = $object->finplanoconta_id;
                $obj->valor = $object->valor;
                $obj->pessoa_id = $object->pessoa_id;

                $this->form->setData($obj);
                TTransaction::close();
            }
            else {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onVlLiquido($param)
    {
        $obj = new stdClass;
        $obj->valor = TBLFuncoes::fmtNumber(
                        TBLFuncoes::numberUS($param['valortit']) - 
                        TBLFuncoes::numberUS($param['desconto']) + 
                        TBLFuncoes::numberUS($param['acresc'])
                      );
        TForm::sendData('form_'.__CLASS__, $obj);
    }
}
