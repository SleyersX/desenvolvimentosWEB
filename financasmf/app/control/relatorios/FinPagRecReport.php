<?php
/**
 * FinPagRecReport Report
 * @author  Adalberto Lima Vitorino (alivitor@gmail.com)
 */
class FinPagRecReport extends TPage
{
    protected $form;
    private $headerDate;
    private $dtsdInic;
    private $vlsdInic;

    function __construct()
    {
        parent::__construct();
        $this->form = new ParamsReportView(__CLASS__, 'Relação de Contas a Pagar/Receber', [$this,'onPrint'], [$this,'onClear']);

        // create the form fields
        $tipo = new TRadioGroup('tipo');
        $situ = new TRadioGroup('situ');
        $dv1 = new TDate('dv1');
        $dv2 = new TDate('dv2');
        $db1 = new TDate('db1');
        $db2 = new TDate('db2');
        $pessoa_id = new TDBCombo('pessoa_id', _DATABASE_, 'Pessoa', 'id', 'nconhecido', 'nconhecido');
        $ccusto_id = new TDBCombo('ccusto_id',_DATABASE_,'Finccusto','id','descricao','descricao');
        $finfilial_id = new TDBCombo('finfilial_id',_DATABASE_,'Finfilial','id','descricao','descricao');
        $finplanoconta_id = new TDBCombo('finplanoconta_id',_DATABASE_,'Finplanoconta','id','nome_fmt','ordem');

        // ajusts
        $tipo->addItems(['P'=>'&nbsp;&nbsp;Pagar&nbsp;&nbsp;&nbsp;', 
                         'R'=>'&nbsp;&nbsp;Receber&nbsp;&nbsp;&nbsp;']);
        $tipo->setLayout('horizontal');
        $tipo->setValue('P');
        $situ->addItems(['A'=>'&nbsp;&nbsp;Em Aberto&nbsp;&nbsp;', 
                         'B'=>'&nbsp;&nbsp;Baixados&nbsp;&nbsp;',
                         'T'=>'&nbsp;&nbsp;Todas']);
        $situ->setLayout('horizontal');
        $situ->setValue('T');

        $dv1->setSize('40%'); 
        $dv1->setMask('dd/mm/yyyy'); 
        $dv1->setDatabaseMask('yyyy-mm-dd');

        $dv2->setSize('40%'); 
        $dv2->setMask('dd/mm/yyyy'); 
        $dv2->setDatabaseMask('yyyy-mm-dd');
        
        $db1->setSize('40%'); 
        $db1->setMask('dd/mm/yyyy'); 
        $db1->setDatabaseMask('yyyy-mm-dd');
        
        $db2->setSize('40%'); 
        $db2->setMask('dd/mm/yyyy'); 
        $db2->setDatabaseMask('yyyy-mm-dd');
        
        $pessoa_id->enableSearch();
        $finplanoconta_id->enableSearch();
 
        // add the fields
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-6']);
        $this->form->addFields([new TLabel('Tipo')], [$tipo]);
        $this->form->addFields([new TLabel('Situação')], [$situ]);
        $this->form->addFields([new TLabel('Período de Vencto')], [$dv1, $lb1=new TLabel('até'), $dv2]);
        $this->form->addFields([new TLabel('Período de Baixas')], [$db1, $lb2=new TLabel('até'), $db2]);
        $this->form->addFields([new TLabel('Filial')], [$finfilial_id]);
        $this->form->addFields([new TLabel('Centro de Custos')], [$ccusto_id]);
        $this->form->addFields([new TLabel('Credor/Devedor')], [$pessoa_id]);

        //parent::add(TBreadCrumb::create(['Relatórios','Finanças']));
        parent::add($this->form);
    }

    function onClear($param) {
        $this->form->clear(TRUE);
    }
    
    function onPrint($param)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->validate();

            TTransaction::open(_DATABASE_);

            $criteria = new TCriteria;
            $criteria->add(new TFilter('tipo','=',$data->tipo));
            
            $filialname = '';
            if($data->finfilial_id) {
                $criteria->add(new TFilter('finfilial_id','=',"{$data->finfilial_id}"));
                $filial = new Finfilial($data->finfilial_id);
                $filialname = $filial->descricao;
            }
            if($data->ccusto_id) {
                $criteria->add(new TFilter('ccusto_id','=',"{$data->ccusto_id}"));
            }
            if($data->pessoa_id) {
                $criteria->add(new TFilter('pessoa_id','=',"{$data->pessoa_id}"));
            }
            // venc
            $filter = TBLFuncoes::makeFilterIntervalDate('dtvenc', $data->dv1, $data->dv2);
            if($filter) {
                $criteria->add($filter);
            }

            // baixa
            if($data->situ == 'A')
                $criteria->add(new TFilter('dtbaixa','IS',NULL));
            elseif($data->situ == 'B')
                $criteria->add(new TFilter('dtbaixa','IS NOT',NULL));

            $filter = TBLFuncoes::makeFilterIntervalDate('dtbaixa', $data->db1, $data->db2);
            if($filter)
                $criteria->add($filter);

            $criteria->setProperty('order', 'dtvenc, pessoa_id');
            TTransaction::close();

            $this->situ = $data->situ;

            // instancia o relatorio
            $relCad = new TBLReport(__CLASS__, 'L');
            $relCad->setTitle('Relação de Contas a '.($data->tipo == 'P' ? 'Pagar' : 'Receber'));

            $tit = 'Contas ';
            if( $filialname ) {
                $tit = $filialname. '   -   '.$tit;                
            }

            if($data->situ == 'T') {
                $tit.='em Aberto / Baixadas';
            }
            else{
                $tit .= $data->situ == 'A' ? 'em Aberto' : 'Baixadas';
            }
            $relCad->setSubTitle($tit);

            $relCad->setMainDataSource(_DATABASE_,'Finpagrec',$criteria);

            // inicializar vars
            $this->dtsdInic = NULL;
            $this->somal = [];
            $this->somal['valor'] = 0.0;
            $this->somal['lancamentos->desconto'] = 0.0;
            $this->somal['lancamentos->acresc'] = 0.0;
            $this->somal['lancamentos->valor'] = 0.0;

            $this->somat = [];
            $this->somat['valor'] = 0.0;
            $this->somat['lancamentos->desconto'] = 0.0;
            $this->somat['lancamentos->acresc'] = 0.0;
            $this->somat['lancamentos->valor'] = 0.0;

            // $relCad->setBreakBody('dtvenc', [$this, 'onBeforeSoma'], [$this,'onBodySoma'], [$this,'onAfterSoma']);
            $relCad->setBreakBody('dtvenc', NULL, [$this,'onBodySoma'], [$this,'onAfterSoma']);
            
            // Monta as colunas
            $relCad->setMainHeader( );
            $relCad->addMainHeader(0,  50, 'Vencto','C/L');
            $relCad->addMainHeader(0,  60, 'C Custos');
            $relCad->addMainHeader(0,  30, 'ID','C/R');
            $relCad->addMainHeader(0, 140, ($data->tipo == 'P' ? 'Fornecedor' : 'Cliente'));
            $relCad->addMainHeader(0, 130, 'Referencia');
            $relCad->addMainHeader(0,  50, 'Parcela');
            $relCad->addMainHeader(0,  60, 'Nº Doc');
            $relCad->addMainHeader(0,  60, 'Valor','L/R');
            if($data->situ <> 'A') {
                $relCad->addMainHeader(0, 50, 'Dt Baixa','C/L');
                $relCad->addMainHeader(0, 50, 'Desconto','L/R');
                $relCad->addMainHeader(0, 50, 'Acresc','L/R');
                $relCad->addMainHeader(0, 50, 'Vl Liquido','L/R');
            }
            else {
                $relCad->addMainHeader(0, 198, 'Obs');
            }

            $relCad->addMainField(0, 'dtvenc', NULL, [$this, 'formatDate']);
            $relCad->addMainField(0, 'finccusto->sigla');
            $relCad->addMainField(0, 'id');
            $relCad->addMainField(0, 'pessoa->nconhecido');
            $relCad->addMainField(0, 'referencia');
            $relCad->addMainField(0, 'nrparc');
            $relCad->addMainField(0, 'nrdoc');
            $relCad->addMainField(0, 'valor', NULL, [$this, 'formatValue']);
            if($data->situ <> 'A') {
                $relCad->addMainField(0, 'dtbaixa', NULL, [$this, 'formatDtBxa']);
                $relCad->addMainField(0, 'finlanca->desconto', NULL, [$this, 'formatValue']);
                $relCad->addMainField(0, 'finlanca->acresc', NULL, [$this, 'formatValue']);
                $relCad->addMainField(0, 'finlanca->valor', NULL, [$this, 'formatValue']);
            }
            else {
                $relCad->addMainField(0, 'obs');
            }
            $relCad->printRep();
            $this->form->setData($data);
        }
        catch (Exception $e) {
            new TMessage('error',$e->getMessage());
        }
    }

    public function onBeforeSoma($rep, $value=NULL)
    {
        //var_dump($value);
        $rep->printLine('Quebra de Data do Relatorio (BEFORE)', 'left', 'titulo2');
    }

    public function onBodySoma($rep, $col, $value)
    {
        if(isset($this->somal[$col])) {
            $this->somal[$col]+= $value ? (float) $value : 0.0;
            $this->somat[$col]+= $value ? (float) $value : 0.0;
        }
    }

    public function onAfterSoma($rep, $value=NULL)
    {
        if(!is_null($value))
        {
            $values = $this->somal;
            $tit = 'Sub-total: ';
            $this->somal['qtd'] = 0;
            $this->somal['valor'] = 0.0;
            $this->somal['lancamentos->desconto'] = 0.0;
            $this->somal['lancamentos->acresc'] = 0.0;
            $this->somal['lancamentos->valor'] = 0.0;
            $y = $rep->pdf->GetY();
            $w = $rep->pdf->GetPageWidth()-35;
            $rep->pdf->Line(80, $y, $w, $y);
        }
        else 
        {
            $values = $this->somal;
            $tit = 'Sub-total: ';
            $this->somal['qtd'] = 0;
            $this->somal['valor'] = 0.0;
            $this->somal['lancamentos->desconto'] = 0.0;
            $this->somal['lancamentos->acresc'] = 0.0;
            $this->somal['lancamentos->valor'] = 0.0;
            $y = $rep->pdf->GetY();
            $w = $rep->pdf->GetPageWidth()-35;
            $rep->pdf->Line(80, $y, $w, $y);

            $rep->pdf->SetX(80);
            $rep->pdf->Cell(468,14,$tit,0,0,'R');
            $rep->pdf->SetFont('Arial','B',9);
            $rep->pdf->Cell(60,14,$this->formatValue($values['valor']),0,0,'R');
            if($this->situ <> 'A') 
            {
                $rep->pdf->Cell(50,14,'');
                $rep->pdf->Cell(50,14,$this->formatValue($values['lancamentos->desconto']),0,0,'R');
                $rep->pdf->Cell(50,14,$this->formatValue($values['lancamentos->acresc']),0,0,'R');
                $rep->pdf->Cell(50,14,$this->formatValue($values['lancamentos->valor']),0,0,'R');
            }
            $rep->pdf->ln();

            $rep->pdf->ln();
            $values = $this->somat;
            $tit = utf8_decode('TOTAL DO PERÍODO: ');
        }
        $rep->pdf->SetX(80);
        $rep->pdf->Cell(468,14,$tit,0,0,'R');
        $rep->pdf->SetFont('Arial','B',9);
        $rep->pdf->Cell(60,14,$this->formatValue($values['valor']),0,0,'R');
        if($this->situ <> 'A') 
        {
            $rep->pdf->Cell(50,14,'');
            $rep->pdf->Cell(50,14,$this->formatValue($values['lancamentos->desconto']),0,0,'R');
            $rep->pdf->Cell(50,14,$this->formatValue($values['lancamentos->acresc']),0,0,'R');
            $rep->pdf->Cell(50,14,$this->formatValue($values['lancamentos->valor']),0,0,'R');
        }
        $rep->pdf->ln();
    }

    public function formatDate($content) 
    {
        $dtFormat = '';
        if($content <> $this->dtsdInic)
        {
            $this->dtsdInic = $content;
            $dtFormat = TDate::date2br($content);
        }
        return $dtFormat;
    }

    public function formatDtBxa($content) 
    {
        return TDate::date2br($content);
    }

    public function formatValue($content) {
        if((float) $content < 0.0)
            $content = $content  * -1;
        return number_format($content, 2, ',', '.');
    }

    public function onShow() {}
}
