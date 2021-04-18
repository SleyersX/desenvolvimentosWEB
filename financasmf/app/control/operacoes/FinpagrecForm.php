<?php
/**
 * FinpagrecForm Registration
 * @author  <your name here>
 */
class FinpagrecForm extends TPage
{
    protected $form;    
    use Adianti\Base\AdiantiStandardFormTrait;
    
    function __construct()
    {
        parent::__construct();        
        $this->setDatabase(_DATABASE_); 
        $this->setActiveRecord('Finpagrec');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Finpagrec');
        $this->form->setFormTitle('Contas a Pagar/Receber');
        
        // create the form fields
        $id = new TEntry('id');
        $manter = new TCheckGroup('manter');
        $tipo = new TRadioGroup('tipo');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', _DATABASE_, 'Pessoa', 'id', 'nome');
        $dtemiss = new TDate('dtemiss');
        $dtvenc = new TDate('dtvenc');
        $nrdoc = new TEntry('nrdoc');
        $valor = new TEntry('valor');
        $multa = new TEntry('multa');
        $jurosdia = new TEntry('jurosdia');
        $nrparc = new TEntry('nrparc');
        $totparc = new TEntry('totparc');
        $referencia = new TEntry('referencia');
        $finfilial_id = new TDBCombo('finfilial_id', _DATABASE_, 'Finfilial', 'id', 'descricao');
        $finccusto_id = new TDBCombo('finccusto_id', _DATABASE_, 'Finccusto', 'id', 'descricao');
        $finplanoconta_id = new TDBCombo('finplanoconta_id', _DATABASE_, 'Finplanoconta', 'id', 'nome_fmt','ordem');
        $dtbaixa = new TDate('dtbaixa');
        $finlanca_id = new TEntry('finlanca_id');
        $obs = new TText('obs');

        // size
        $id->setSize('50%');
        $manter->addItems(['S'=>'Manter dados']);
        $manter->setLayout('horizontal');
        $tipo->setLayout('horizontal');
        $finccusto_id->setSize('100%');
        $finfilial_id->setSize('100%');
        $pessoa_id->setSize('100%');
        $referencia->setSize('100%');
        $nrdoc->setSize('100%');
        $dtemiss->setSize('100%');        
        $dtvenc->setSize('100%');
        $valor->setSize('100%');
        $multa->setSize('100%');
        $jurosdia->setSize('100%');
        $nrparc->setSize('45%');
        $totparc->setSize('40%');
        $finplanoconta_id->setSize('100%');
        $dtbaixa->setSize('100%');
        $finlanca_id->setSize('100%');
        $obs->setSize('100%','60');

        // custom
        $id->setEditable(FALSE);
        $manter->addItems(['S'=>'Manter dados']);
        $manter->setLayout('horizontal');
        $tipo->addItems(['P'=>'Pagar', 'R'=>'Receber']);
        $pessoa_id->setMinLength(1);
        $dtemiss->setMask('dd/mm/yyyy'); 
        $dtemiss->setDatabaseMask('yyyy-mm-dd');
        $dtvenc->setMask('dd/mm/yyyy'); 
        $dtvenc->setDatabaseMask('yyyy-mm-dd');
        $nrdoc->setMaxLength(25);
        $valor->setNumericMask(2,',','.', TRUE);
        $multa->setNumericMask(2,',','.', TRUE);
        $jurosdia->setNumericMask(2,',','.', TRUE);
        $nrparc->setMask('999');
        $totparc->setMask('999');
        $referencia->setMaxLength('60');
        $finplanoconta_id->enableSearch();
        $dtbaixa->setEditable(FALSE);
        $dtbaixa->setMask('dd/mm/yyyy'); 
        $dtbaixa->setDatabaseMask('yyyy-mm-dd');
        $finlanca_id->setEditable(FALSE);
        $finlanca_id->setMask('999999');

        // Validation
        $tipo->addValidation('Tipo de Conta', new TRequiredValidator);
        $pessoa_id->addValidation('Credor/Devedor', new TRequiredValidator);
        $dtvenc->addValidation('Data de Vencimento', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);
        $finccusto_id->addValidation('Centro de Custos', new TRequiredValidator);
        $finfilial_id->addValidation('Filial', new TRequiredValidator);
        $referencia->addValidation('Referência', new TRequiredValidator);

        $row = $this->form->addFields(
                [$lb0=new TLabel('ID'), $id, $manter],
                [$lb1=new TLabel('Tipo de Conta','red'), $tipo],
                [$lb3=new TLabel('Centro de Custos','red'), $finccusto_id],
                [$lb4=new TLabel('Filial','red'), $finfilial_id]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb3->setSize('100%');
        $lb4->setSize('100%');
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $row = $this->form->addFields(
                [$lb0=new TLabel('Credor/Devedor','red'), $pessoa_id],
                [$lb1=new TLabel('Referência','red'), $referencia],
                [$lb2=new TLabel('Nº Documento'), $nrdoc]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-4', 'col-sm-5', 'col-sm-3']; //, 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields(
                [$lb0=new TLabel('Data Emissão'), $dtemiss],
                [$lb1=new TLabel('Vencimento','red'), $dtvenc],
                [$lb2=new TLabel('Parcela(s)'), $nrparc,'/',$totparc],
                [$lb3=new TLabel('Valor','red'), $valor],
                [$lb4=new TLabel('Multa %'), $multa],
                [$lb5=new TLabel('Juro Diário ($)'), $jurosdia]);
        $lb0->setSize('100%');
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $lb3->setSize('100%');
        $lb4->setSize('100%');
        $lb5->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields(
                [$lb1=new TLabel('Conta Resumo'), $finplanoconta_id],
                [$lb2=new TLabel('Data da Baixa','blue'), $dtbaixa]);
                //[$lb3=new TLabel('Nº Lançamento','blue'), $finlanca_id]);
        $lb1->setSize('100%');
        $lb2->setSize('100%');
        $row->layout = ['col-sm-5', 'col-sm-2', 'col-sm-2'];

        //$this->form->appendPage('Observações');
        $this->form->addFields([new TLabel('Observações')]);
        $this->form->addFields([$obs]);

        // create the form actions
        if(is_null(TSession::getValue('titbaixado')))
        {
            $btn = $this->form->addAction(_t('Save & New'), new TAction([$this,'onSaveNew']),'bs:floppy-saved blue');        
            $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSaveConsist']), 'fa:floppy-o');
            $btn->class = 'btn btn-sm btn-primary';
            $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        }
        else
        {
            $id->setEditable(FALSE);
            $tipo->setEditable(FALSE);
            $manter->setEditable(FALSE);
            $finccusto_id->setEditable(FALSE);
            $pessoa_id->setEditable(FALSE);
            $referencia->setEditable(FALSE);
            $nrdoc->setEditable(FALSE);
            $dtemiss->setEditable(FALSE);        
            $dtvenc->setEditable(FALSE);
            $valor->setEditable(FALSE);
            $multa->setEditable(FALSE);
            $jurosdia->setEditable(FALSE);
            $nrparc->setEditable(FALSE);
            $totparc->setEditable(FALSE);
            $finplanoconta_id->setEditable(FALSE);
            $dtbaixa->setEditable(FALSE);
            $finlanca_id->setEditable(FALSE);
            $obs->setEditable(FALSE);
        }
        $this->form->addAction( _t('Back'), new TAction(['FinpagrecList', 'onReload']), 'fa:arrow-circle-o-left blue');  
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'FinpagrecList'));
        $container->add($this->form);
        parent::add($container);
    }

    public function onSaveNew( $param ) 
    {
        $manter = isset($param['manter']);
        if($this->onSaveConsist($param) && !$manter)
            $this->onClear([]);
        else{
            $dados = new stdClass;
            $dados->id = '';
            TForm::sendData('form_Finpagrec', $dados);
        }
    }

    public function onSaveConsist( $param ) 
    {
        if($param['dtemiss'] && $param['dtvenc'] && 
            TDate::date2us($param['dtemiss']) > TDate::date2us($param['dtvenc'])){
            new TMessage('error','Data de emissão maior que vencimento !');
            $this->form->setData($this->form->getData($this->activeRecord));
        }
        else
            $this->onSave($param);
    }
}
