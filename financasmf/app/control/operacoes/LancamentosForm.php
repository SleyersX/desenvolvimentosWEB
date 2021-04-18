<?php
/**
 * LancamentosForm Registration
 * @author  <your name here>
 */
class LancamentosForm extends TPage
{
    protected $form;
    use Adianti\Base\AdiantiStandardFormTrait;
    
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase(_DATABASE_);
        $this->setActiveRecord('Finlanca');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Lancamentos');
        $this->form->setFormTitle('Lançamentos Financeiros');

        // filters
        $fContas =new TCriteria();
        $fContas->add(new TFilter('ativa','=','Y'));
        $fCResumo =new TCriteria();
        $fCResumo->add(new TFilter('ativa','=','Y'));

        // create the form fields
        $id = new TEntry('id');
        $manter = new TCheckGroup('manter');
        $dtlanc = new TDate('dtlanc');
        $finconta_id = new TDBCombo('finconta_id',_DATABASE_,'Finconta','id','descricao','descricao', $fContas);
        $finccusto_id = new TDBCombo('finccusto_id', _DATABASE_, 'Finccusto', 'id', 'descricao','descricao');
        $finfilial_id = new TDBCombo('finfilial_id', _DATABASE_, 'Finfilial', 'id', 'descricao','descricao');
        $finplanoconta_id = new TDBCombo('finplanoconta_id', _DATABASE_, 'Finplanoconta', 'id', 'nome_fmt','ordem');  // grp3, nome
        $descricao = new TEntry('descricao');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', _DATABASE_, 'Pessoa', 'id', 'nome', 'nome');
        $debcred = new TRadioGroup('debcred');
        $valor = new TEntry('valor');
        $nrdoc = new TEntry('nrdoc');
        $obs = new TText('obs');

        // set sizes
        $id->setSize('100%');
        $dtlanc->setSize('100%');
        $finconta_id->setSize('100%');
        $finccusto_id->setSize('100%');
        $finfilial_id->setSize('100%');
        $finplanoconta_id->setSize('100%');
        $descricao->setSize('100%');        
        $pessoa_id->setSize('100%');
        $valor->setSize('100%');
        $nrdoc->setSize('100%');
        $obs->setSize('100%','80');

        // validations
        $dtlanc->addValidation('Data', new TRequiredValidator);
        $finconta_id->addValidation('Conta financeira', new TRequiredValidator);
        $finccusto_id->addValidation('Centro de Custos', new TRequiredValidator);
        $finfilial_id->addValidation('Filial', new TRequiredValidator);
        $finplanoconta_id->addValidation('Conta Resumo', new TRequiredValidator);
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $debcred->addValidation('Débito/Crédito', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);
        
        // custom        
        $id->setEditable(FALSE);
        $manter->addItems(['S'=>'']);
        $manter->setLayout('horizontal');
        $dtlanc->setMask('dd/mm/yyyy'); 
        $dtlanc->setDatabaseMask('yyyy-mm-dd');
        $debcred->addItems(['D'=>'Debito','C'=>'Credito']);
        $debcred->setValue('D');
        $debcred->setLayout('horizontal');
        $valor->setNumericMask(2,',','.', TRUE);
        $finplanoconta_id->enableSearch();
        $pessoa_id->setMinLength(1);
        $pessoa_id->setMask('{nome} ({nconhecido})');
        $descricao->setMaxLength(80);
        $nrdoc->setMaxLength(20);
        
        // add the fields
        $row = $this->form->addFields(
            [ $l0 = new TLabel('ID'), $id],
            [ $l1 = new TLabel('Filial','red'), $finfilial_id],
            [ $l2 = new TLabel('Data','red'),  $dtlanc ],
            [ $l3 = new TLabel('Conta Financeira','red'), $finconta_id ],
            [ $l4 = new TLabel('Centro de Custos','red'), $finccusto_id],
            [ $l5 = new TLabel('D/C','red'), $debcred ]);
        $l0->setSize('100%');
        $l1->setSize('100%');
        $l2->setSize('100%');
        $l3->setSize('100%');
        $l4->setSize('100%');
        $l5->setSize('100%');
        $row->layout = ['col-sm-1','col-sm-2','col-sm-2','col-sm-3','col-sm-2','col-sm-2'];

        $row = $this->form->addFields(
            [ $l0 = new TLabel('Conta Resumo','red'), $finplanoconta_id ],
            [ $l2 = new TLabel('Descrição','red'),    $descricao ],            
            [ $l4 = new TLabel('Valor','red'), $valor ],
            [ $l1 = new TLabel('Nº Documento'), $nrdoc ]);
        $l0->setSize('100%');
        $l1->setSize('100%');
        $l2->setSize('100%');
        $l4->setSize('100%');
        $row->layout = ['col-sm-4','col-sm-4','col-sm-2','col-sm-2'];

        $row = $this->form->addFields([ $l0 = new TLabel('Pessoa'), $pessoa_id ]);
        $l0->setSize('100%');
        $row->layout = ['col-sm-4'];

        $this->form->addFields( [ new TLabel('Observações','blue',10,'U')]);
        $this->form->addFields( [ $obs ] );
        $this->form->addFields( [ $manter, new TLabel('Manter dados apos gravar','blue',8,'I') ] );

        // create the form actions
        if( is_null( TSession::getValue('locked')) )
        {
            $btn = $this->form->addAction(_t('Save & New'), new TAction([$this, 'onSaveNew']),'bs:floppy-saved');
            $btn->class = 'btn btn-sm btn-primary';
            $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
            $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        }
        $this->form->addAction(_t('Back'),new TAction(['LancamentosList','onReload']),'fa:arrow-circle-o-left blue');    

        // vertical box container
        $this->alertBox = new TElement('div');
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'LancamentosList'));
        $container->add($this->alertBox);
        $container->add($this->form);
        parent::add($container);
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $this->form->validate();
            
            $object = new Finlanca;
            $data = $this->form->getData();
            $object->fromArray( (array) $data);

            $cf = new Finconta($object->conta_id);
            if($data->dtlanc < $cf->dtini)
                throw new Exception("Data inválida ! Lançamentos para esta conta devem ser a partir de ".TDate::date2br($cf->dtini));

            $pc = new Finplanoconta($object->finplanoconta_id);
            if(strlen($pc->ordem) < strlen(_NIVEISPC_))
                throw new Exception("Conta Resumo inválida !");

            if($pc->tipolanc <> 'T') 
            {
                if($pc->tipolanc == 'D' && $object->debcred <> 'D' ) {
                    throw new Exception("São permitidos apenas lançamentos de <strong>DÉBITO</strong> para esta Conta Resumo");                    
                }                    
                elseif($pc->tipolanc == 'C' && $object->debcred <> 'C' ) {
                    throw new Exception("São permitidos apenas lançamentos de <strong>CRÉDITO</strong> para esta Conta Resumo");
                }
            } 

            if($object->debcred == 'D')
                $object->valor = (float) $object->valor * -1;
   
            $object->origem = 1;
            $object->store();
            $data->id = $object->id;
            
            $this->form->setData($data);
            TTransaction::close();
            $this->onMsgBox('success', TAdiantiCoreTranslator::translate('Record saved'));
            $result = TRUE;
        }
        catch (Exception $e)
        {            
            $this->onMsgBox('danger', $e->getMessage());
            $this->form->setData( $this->form->getData());
            TTransaction::rollback();
            $result = FALSE;
        }
        return $result;
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                TTransaction::open(_DATABASE_);
                $object = new Finlanca($key);
                
                if( !empty($object->idlock) ) {
                    $origens=['Digitação', 'Transferência entre Contas','Baixa de Contas a Pagar/Receber'];
                    if( $object->origem <> 1)
                    {
                        $this->onMsgBox('danger', 'Lançamento originado de '.$origens[(int) $object->origem-1].', edições não serão consideradas');
                    }   
                }   
                if($object->debcred == 'D')
                    $object->valor = (float) $object->valor * -1;

                $this->form->setData($object);
                TTransaction::close();
            }
            else {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSaveNew( $param )
    {
        $manter = isset($param['manter']);
        if($this->onSave($param) && !$manter) {
            $this->form->clear();
        }
        else
        {
            $dados = new stdClass;
            $dados->id = '';
            TForm::sendData('form_Lancamentos', $dados);
        }
    }

    public function onMsgBox($tipo, $msg) 
    {
        $alert = new TAlert($tipo, $msg);
        $alert->id = 'msgBox';
        if($tipo <> 'danger') 
        {
            $time = $tipo == 'danger' ? 5000 : 2000;
            TScript::create( "$('#msgBox').fadeOut(".$time.");");            
        }
        $this->alertBox->add($alert);
        TScript::create("window.scrollTo(0, 0);");
    }
}
