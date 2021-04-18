<?php
/**
 * LancamentosTransf Form
 * @author  <your name here>
 */
class LancamentosTransf extends TWindow
{
    protected $form;
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Transferências entre C/C');
        parent::setCloseAction(new TAction([$this, 'onClose']));
        parent::setSize(0.7, 350);
        $this->form = new BootstrapFormBuilder('form_LancamentosTransf');

        // form fields
        $dtlanc = new TDate('dtlanc');
        $ctOrigem = new TDBCombo('ctOrigem',_DATABASE_, 'Finconta', 'id', 'descricao','descricao');
        $sdOrigem = new TEntry('sdOrigem');
        $ctDestino = new TDBCombo('ctDestino',_DATABASE_, 'Finconta', 'id', 'descricao','descricao');
        $sdDestino = new TEntry('sdDestino');
        $descOrigem = new TEntry('descOrigem');
        $descDestino = new TEntry('descDestino');
        $nrdoc  = new TEntry('nrdoc');
        $valor = new TEntry('valor');
        $obs = new TEntry('obs');

        // sizes
        $dtlanc->setSize('100%');
        $ctOrigem->setSize('100%');
        $sdOrigem->setSize('100%');
        $ctDestino->setSize('100%');
        $sdDestino->setSize('100%');
        $descOrigem->setSize('100%');
        $descDestino->setSize('100%');
        $nrdoc->setSize('100%');
        $valor->setSize('100%');
        $obs->setSize('100%');        

        // custom        
        $change_ctOrigem = new TAction([$this, 'onChangeCtOrigem']);

        $dtlanc->setMask('dd/mm/yyyy');
        $dtlanc->setDatabaseMask('yyyy-mm-dd');
        $dtlanc->addValidation('data', new TRequiredValidator );
        $dtlanc->setExitAction($change_ctOrigem);
        $ctOrigem->addValidation('conta de Origem', new TRequiredValidator );
        $ctOrigem->setChangeAction($change_ctOrigem);
        $sdOrigem->setEditable(FALSE);
        $sdOrigem->style="text-align: right";
        $ctOrigem->addValidation('conta de destino', new TRequiredValidator );
        $ctDestino->setChangeAction($change_ctOrigem);
        $sdDestino->setEditable(FALSE);
        $sdDestino->style="text-align: right";
        $descOrigem->setMaxLength(120);
        $descDestino->setMaxLength(120);
        $valor->setNumericMask(2,',','.', TRUE);
        $valor->addValidation('valor', new TRequiredValidator );
        $obs->setMaxLength(120);

        // add the fields
        $row = $this->form->addFields( 
            [$lb0=new TLabel('Data','red'),             $dtlanc],
            [$lb1=new TLabel('Conta de Origem','red'),  $ctOrigem],
            [$lb2=new TLabel('Saldo'),                  $sdOrigem],
            [$lb3=new TLabel('Conta de Destino','red'), $ctDestino],
            [$lb4=new TLabel('Saldo'),                  $sdDestino]);
        $lb0->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-3', 'col-sm-2'];

        $row = $this->form->addFields( 
            [$lb0=new TLabel('Valor','red'),            $valor],
            [$lb1=new TLabel('Hist C/C Origem','red'),  $descOrigem],
            [$lb3=new TLabel('Hist C/C Destino','red'), $descDestino]);
        $lb0->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-5'];

        $row = $this->form->addFields( 
            [$lb0=new TLabel('Nº Doc'),      $nrdoc],
            [$lb0=new TLabel('Observações'), $obs]
        );
        $lb0->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-10'];


        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']),'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'), new TAction([$this, 'onClear']), 'bs:plus-sign green');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);        
        parent::add($container);
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $this->form->validate();
           
            $data = $this->form->getData();

            // contas não poder ser iguais - ok
            if($data->ctOrigem == $data->ctDestino){
                throw new Exception("Contas inválidas !<br>As contas devem ser diferentes");
            }
            // Conferir datas de saldo inicial - ok
            $cto = new Finconta($data->ctOrigem);
            if($cto->dtini > $data->dtlanc){
                throw new Exception("Data inválida !<br>Lançamentos para conta de <strong>origem</strong> devem ser a partir de ".TDate::date2br($cto->dtini));
            }
            $ctd = new Finconta($data->ctDestino);
            if($ctd->dtini > $data->dtlanc){
                throw new Exception("Data inválida !<br>Lançamentos para conta de <strong>destino</strong> devem ser a partir de ".TDate::date2br($ctd->dtini));
            }
            // conferir saldo na origem - ok
            $sdCto = $cto->getSaldoCC($data->dtlanc) - (float) $data->valor;
            if($sdCto < 0 )
                throw new Exception('Saldo insuficiente para esta transferência');
            
            $idTransf = substr(md5(date('Y-m-d H:i:s').$data->valor),0,40);

            // Gravar Lanc credito no destino
            $object = new Finlanca;
            $object->origem = 2;
            $object->userinc  = TSession::getValue('userid');
            $object->dtlanc  = $data->dtlanc;
            $object->finconta_id = $data->ctDestino;
            $object->descricao = $data->descDestino;
            $object->debcred = 'C';
            $object->nrdoc = $data->nrdoc;
            $object->valor = (float) $data->valor;
            $object->obs = $data->obs;
            $object->idlock = $idTransf;
            $object->store();

            // Gravar Lanc debito na origem  
            $object = new Finlanca;
            $object->origem  = 2;
            $object->userinc  = TSession::getValue('userid');
            $object->dtlanc  = $data->dtlanc;
            $object->finconta_id = $data->ctOrigem;
            $object->descricao = $data->descOrigem;;
            $object->debcred = 'D';
            $object->nrdoc = $data->nrdoc;
            $object->valor = (float) $data->valor * -1;
            $object->obs = $data->obs;
            $object->idlock = $idTransf;
            $object->store();

            TTransaction::close();
            
            $action = new TAction([$this, 'onClear'], []);
            new TMessage('info',TAdiantiCoreTranslator::translate('Record saved'), $action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData());            
            TTransaction::rollback();
        }
    }
    
    public function onClear($param)
    {
        $this->form->clear(TRUE);
        $data = new stdClass;
        $data->dtlanc = date('d/m/Y');
        $this->form->setData($data);
    }
    
    public static function onChangeCtOrigem($param)
    {
        if($param['ctOrigem'] && $param['dtlanc'])
        {
            TTransaction::open(_DATABASE_);
            $obj = new StdClass;
            $cto = new Finconta($param['ctOrigem']);
            $sd = $cto->getSaldoCC(TDate::date2us($param['dtlanc']));
            $obj->sdOrigem = TBLFuncoes::fmtNumber($sd, 2, 'R$');
            $obj->descDestino = 'Transf recebida da C/C '.$cto->descricao;
            TForm::sendData('form_LancamentosTransf', $obj);
            TTransaction::close();
        }
        
        if($param['ctDestino'] && $param['dtlanc'])
        {
            TTransaction::open(_DATABASE_);
            $obj = new StdClass;
            $ctd = new Finconta($param['ctDestino']);
            $sd = $ctd->getSaldoCC(TDate::date2us($param['dtlanc']));
            $obj->sdDestino = TBLFuncoes::fmtNumber($sd, 2, 'R$');
            $obj->descOrigem = 'Transf feito para C/C '.$ctd->descricao;
            TForm::sendData('form_LancamentosTransf', $obj);
            TTransaction::close();
        }
    }

    public static function onClose() {
        AdiantiCoreApplication::loadPage('LancamentosList','onReload');
    }    
}
