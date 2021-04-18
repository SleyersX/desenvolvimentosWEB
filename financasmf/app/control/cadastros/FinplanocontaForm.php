<?php
/**
 * FinplanocontaForm Registration
 * @author  <your name here>
 */
class FinplanocontaForm extends TWindow
{
    private $form;
    
    function __construct()
    {
        parent::__construct();
        parent::setTitle('Plano de Contas');
        parent::setCloseAction(new TAction([$this, 'onClose']));
        $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
        parent::setSize(0.6, 290);

        // create the form fields
        $id = new TEntry('id');
        $ordem = new TEntry('ordem');
        $nome = new TEntry('nome');
        $nomecurto = new TEntry('nomecurto');
        $tipolanc = new TCombo('tipolanc');
        $ativa = new TRadioGroup('ativa');

        // set sizes
        $id->setSize('50%');
        $ordem->setSize('100%');
        $nome->setSize('100%');
        $nomecurto->setSize('100%');
        $tipolanc->setSize('100%');

        // custom
        $id->setEditable(FALSE);
        $ordem->setMask(_NIVEISPC_);
        $ordem->placeholder = 'Formato: '._NIVEISPC_;
        $ativa->addItems(['Y'=>'Sim','N'=>'Não']);
        $ativa->setValue('Y');
        $ativa->setLayout('horizontal');
        $nome->setMaxLength(50);
        $nome->setExitAction(new TAction([$this, 'onExitNome']));
        $nomecurto->setMaxLength(30);
        $tipolanc->addItems(['D'=>'Debitos','C'=>'Creditos','T'=>'Débitos/Créditos']);

        // validations
        $ativa->addValidation('Conta ativa', new TRequiredValidator);
        $ordem->addValidation('Nº de Ordem', new TRequiredValidator);
        $ordem->addValidation('Nº de Ordem', new TNrOrdemValidator);
        $tipolanc->addValidation('Lançamentos aceitos', new TRequiredValidator);
        $nome->addValidation('Descrição', new TRequiredValidator);
        $nomecurto->addValidation('Descrição curta', new TRequiredValidator);

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

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'),  new TAction([$this, 'onClear']), 'fa:eraser red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);        
        parent::add($container);
    }

    public function onClear() {
        $this->form->clear();
    }

    public static function onClose() {
        AdiantiCoreApplication::loadPage('FinplanocontaList', 'onReload');
    }

    public function onEdit($param) 
    {
        try
        {
            TTransaction::open(_DATABASE_);      
            if(isset($param['id']))
                $object = new Finplanoconta($param['id']);
            else
                $object = new Finplanoconta;
            $this->form->setData($object);
            TTransaction::close();
        }
        catch (Exception $e) {
            TTransaction::rollback();
            $this->onClear();
        }
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(_DATABASE_);            
            $data = $this->form->getData();
            $this->form->validate();

            $object = new Finplanoconta;
            $object->fromArray((array) $data);
            $object->store();

            // ajusta contas descendentes caso tenha
            Finplanoconta::where('ordem', 'LIKE', "{$param['ordem']}%")
                    ->set('ativa', $param['ativa'])
                    ->set('tipolanc', $param['tipolanc'])
                    ->update();

            TTransaction::close();
            
            //$action = new TAction([$this, 'onClose']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onClear();
        }
        catch (Exception $e)
        {
            $this->form->setData($this->form->getData());
            new TMessage('error', TBLFuncoes::msgErrors($e)); //$e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onExitNome($param)
    {
        if( isset($param['id']) && empty($param['id']) && !empty($param['nome']) )
        {
            $obj = new stdClass;
            $obj->nomecurto = substr($param['nome'],0,29);
            TForm::sendData('form_'.__CLASS__, $obj);
        }
    }
}
