<?php
/**
 * ConsultaSaldo Form
 * @author  <your name here>
 */
class ConsultaSaldo extends TWindow
{
    protected $form;
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Consulta Saldo de C/C');
        parent::setCloseAction(new TAction([$this, 'onClose']));
        parent::setSize(0.4, 175);
        $this->form = new BootstrapFormBuilder('form_ConsultaSaldo');

        // form fields
        $dtlanc = new TDate('dtlanc');
        $ctOrigem = new TDBCombo('ctOrigem',_DATABASE_, 'Finconta', 'id', 'descricao','descricao');
        $sdOrigem = new TEntry('sdOrigem');

        // sizes
        $dtlanc->setSize('100%');
        $ctOrigem->setSize('100%');
        $sdOrigem->setSize('100%');

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

        // add the fields
        $row = $this->form->addFields( 
            [$lb0=new TLabel('Data','red'),             $dtlanc],
            [$lb1=new TLabel('Conta de Origem','red'),  $ctOrigem],
            [$lb2=new TLabel('Saldo'),                  $sdOrigem]);
        $lb0->setSize('100%');
        $row->layout = ['col-sm-4', 'col-sm-5', 'col-sm-3'];

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);        
        parent::add($container);
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
            TForm::sendData('form_ConsultaSaldo', $obj);
            TTransaction::close();
        }
    }

    public function onShow() {}

    public static function onClose() {
        AdiantiCoreApplication::loadPage('LancamentosList','onShow');
    }    
}
?>
