<?php
/**
 * FinPlanoContasReport
 * @author  Adalberto Lima Vitorino - alivitor@gmail.com
 **/
class FinPlanoContasReport extends TPage
{
	private $form;	
	private $ordem;

	function __construct($param)
	{
		parent::__construct();
		$this->form = new ParamsReportView(__CLASS__, 'Impressão do Plano de Contas', [$this,'onPrint'], [$this,'onClear'], 'FinplanocontaList');

		$ordemsaida = new TCombo('ordemsaida');
        $ordemsaida->addItems(['0'=>'Nº de Ordem', '1'=>'ID', '2'=>'Descrição']);
        $ordemsaida->setValue('0');
        $ordemsaida->setTip('Ordem de impressão');
        $this->ordem = ['ordem','id','nome'];

        $this->form->addFields([new TLabel('Ordem')], [$ordemsaida]);
        //parent::add(TBreadCrumb::create(['Cadastros','Finanças','Plano de Contas']));
		parent::add($this->form);
	}

	function onPrint($param)
	{
		$data = $this->form->getData();
	
	    $criteria = new TCriteria;
		$criteria->resetProperties();
		$criteria->setProperty('order',$this->ordem[$data->ordemsaida]);

	    $relCad = new TBLReport(__CLASS__);
	    $relCad->setTitle('Plano de Contas');
	    $relCad->setMainDataSource(_DATABASE_, 'Finplanoconta', $criteria); 

		$relCad->setMainHeader( );
		$relCad->addMainHeader(0, 40,'ID','C/R');
		$relCad->addMainHeader(0, 10,'');
		$relCad->addMainHeader(0, 70,'Nº Ordem');
		$relCad->addMainHeader(0,300,'Descrição');
		$relCad->addMainHeader(0, 60,'Ativa','C/C');
		$relCad->addMainHeader(0, 60,'Lanc Aceito','C/C');

		$func1 = function($value){ return ($value == 'Y' ? 'Sim':'Não'); };
		$func2 = function($value){ return ($value == 'D' ? 'Débitos':($value == 'C' ? 'Créditos':'Deb/Cred')); };
		$relCad->addMainField(0,'id');
		$relCad->addMainField(0,'spaces');
		$relCad->addMainField(0,'ordem' );
		$relCad->addMainField(0,'nome');
		$relCad->addMainField(0,'ativa', NULL, $func1);
		$relCad->addMainField(0,'tipolanc', NULL, $func2);

	    $relCad->printRep();
		$this->form->setData($data);
	}

    public function onClear($param) {
        $this->form->clear(TRUE);
    }

	public function onShow() {}
}
