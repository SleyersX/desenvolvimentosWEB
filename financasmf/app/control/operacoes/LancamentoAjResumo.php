<?php
/**
 * Ajusta contas resumo
 * @author  <your name here>
 */
class LancamentoAjResumo extends TWindow
{
    protected $form;
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::setTitle('Ajusta Contas Resumo em lançamentos');
        parent::setCloseAction(new TAction([$this, 'onClose']));
        parent::setSize(0.5, 290);
        $this->form = new BootstrapFormBuilder('form_LancamentosTransf');

        // create the form fields
        $ctOrigem = new TDBCombo('ctOrigem',_DATABASE_, 'Finplanoconta', 'id', 'nome_fmt','ordem');
        $ctDestino = new TDBCombo('ctDestino',_DATABASE_, 'Finplanoconta', 'id', 'nome_fmt','ordem');
        $dv1 = new TDate('dv1');
        $dv2 = new TDate('dv2');

        // custom        
        $ctOrigem->enableSearch();
        $ctOrigem->addValidation('Conta Origem', new TRequiredValidator);
        $ctOrigem->setSize('100%');
        $ctDestino->addValidation('Conta Destino', new TRequiredValidator);
        $ctDestino->enableSearch();
        $ctDestino->setSize('100%');
        $dv1->setSize('40%'); 
        $dv1->setMask('dd/mm/yyyy'); 
        $dv1->setDatabaseMask('yyyy-mm-dd');
        $dv2->setSize('40%'); 
        $dv2->setMask('dd/mm/yyyy'); 
        $dv2->setDatabaseMask('yyyy-mm-dd');

        // add the fields
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        $this->form->addFields([new TLabel('Trocar conta')], [$ctOrigem]);
        $this->form->addFields([new TLabel('pela conta')], [$ctDestino]);
        $this->form->addFields([new TLabel('Período')], [$dv1, new TLabel('até'), $dv2]);

        // create the form actions
        $this->form->addAction('Confirmar', new TAction([$this, 'onSave']));

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
            if($data->ctOrigem == $data->ctDestino)
                throw new Exception("Contas inválidas !<br>As contas devem ser diferentes");

            $pc = new Finplanoconta($data->ctOrigem);
            if(strlen($pc->ordem) < 6)
                throw new Exception("Conta de origem inválida !");

            $pc = new Finplanoconta($data->ctDestino);
            if(strlen($pc->ordem) < 6)
                throw new Exception("Conta de destino inválida !");

            $criteria = new TCriteria; 
            $criteria->add(new TFilter('finplanoconta_id', '=', $data->ctOrigem)); 
            $dtf = TBLFuncoes::makeFilterIntervalDate('dtlanc', $data->dv1, $data->dv2);
            if($dtf) {
                $criteria->add($dtf);
            }
            
            //echo $criteria->dump();
            $repository = new TRepository('Finlanca'); 
            $lancamentos = $repository->load($criteria); 
            $nrlanc = 0;
            if($lancamentos) 
            {
                //var_dump($lancamentos);
                foreach ($lancamentos as $key=>$lancamento) { 
                    $lancamento = new Finlanca($lancamento->id);
                    $lancamento->finplanoconta_id = $data->ctDestino;
                    $lancamento->store();
                    $nrlanc++; 
                }                
                new TMessage('info','Foram ajustados '.$nrlanc.' lançamentos');
            }
            else{
                new TMessage('info','Não existem lançamentos da conta de<br>origem no período informado');
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData());            
            TTransaction::rollback();
        }
    }    

    public function onShow() {}

    public static function onClose() {
        AdiantiCoreApplication::loadPage('LancamentosList','onReload');
    }    
}
