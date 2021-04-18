<?php
/**
 * FinExtratoReport Report
 * @author  Adalberto Lima Vitorino (blsistemas50@gmail.com)
 */
class FinExtratoReport extends TPage
{
    protected $form;
    private $headerDate;
    private $dtsdInic;
    private $vlsdInic;

    function __construct()
    {
        parent::__construct();
        $this->form = new ParamsReportView(__CLASS__, 'Extrato de Movimentação', [$this,'onPrint'], [$this, 'onClear']);

        $conta = new TDBCombo('conta', _DATABASE_, 'Finconta', 'id', 'descricao', 'descricao');
        $finplanoconta_id = new TDBCombo('finplanoconta_id', _DATABASE_, 'Finplanoconta', 'id', 'nome_fmt', 'ordem');
        $finccusto_id = new TDBCombo('finccusto_id', _DATABASE_, 'Finccusto', 'id', 'descricao');
        $finfilial_id = new TDBCombo('finfilial_id', _DATABASE_, 'Finfilial', 'id', 'descricao');
        $dtInicial = new TDate('dtInicial');
        $dtFinal   = new TDate('dtFinal');
        $pessoa_id = new TDBCombo('pessoa_id',_DATABASE_,'Pessoa','id','{nome}  ({apelido})', 'nome');
        $pessoa_id->enableSearch();
        $finplanoconta_id->enableSearch();
        $dtInicial->setMask('dd/mm/yyyy');
        $dtFinal->setMask('dd/mm/yyyy');
        $dtInicial->setSize('45%');
        $dtFinal->setSize('45%');

        $this->form->addFields([new TLabel('Conta Financeira','blue')], [$conta],
                               [new TLabel('Conta Resumo','blue')], [$finplanoconta_id] );
        $this->form->addFields([new TLabel('Período: de ')], [$dtInicial, new TLabel('&nbsp;a&nbsp;'), $dtFinal], [new TLabel('Filial')], [$finfilial_id]);
        $this->form->addFields([new TLabel('Centro de Custos')], [$finccusto_id], [new TLabel('Pessoa')], [$pessoa_id]);
        
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
            $fdata = $this->form->getData();
            $this->form->validate();

            if(empty($fdata->conta) && empty($fdata->finplanoconta_id))
                throw new Exception("Informe uma Conta Financeira ou uma Conta Resumo");

            $dtInicial = TDate::date2us($fdata->dtInicial);
            $dtFinal = TDate::date2us($fdata->dtFinal);
            if(!empty($dtInicial) && !empty($dtFinal) && $dtInicial > $dtFinal)
                throw new Exception("Período inválido !");

            // monta o filtro
            TTransaction::open(_DATABASE_);
            $criteria = new TCriteria;
            $this->headerDate = 'Período: ';
            $this->vlsdInic = 0;
            
            if(!empty($fdata->conta)){
                $criteria->add(new TFilter('finconta_id','=',"{$fdata->conta}"));
            }            

            if(!empty($fdata->finplanoconta_id))
            {                
                $cres = [];
                $res = new Finplanoconta($fdata->finplanoconta_id);
                $grupo = Finplanoconta::where('ordem', 'LIKE', "{$res->ordem}%")->load();
                foreach ($grupo as $key=>$contares) {
                    $cres[] = $contares->id; 
                }
                $criteria->add(new TFilter('finplanoconta_id', 'IN', $cres));
            }
            
            $filialname = '';
            if($fdata->finfilial_id) 
            {
                $criteria->add(new TFilter('finfilial_id','=',"{$fdata->finfilial_id}"));
                $filial = new Finfilial($fdata->finfilial_id);
                $filialname = $filial->descricao;
            }
            
            if(!empty($fdata->finccusto_id)){
                $criteria->add(new TFilter('finccusto_id','=',"{$fdata->finccusto_id}"));
            }

            if(!empty($fdata->pessoa_id)){
                $criteria->add(new TFilter('pessoa_id','=',"{$fdata->pessoa_id}"));
            }            
            
            $cta = new Finconta($fdata->conta);
            if(!empty($fdata->dtInicial) && empty($fdata->dtFinal))
            {
                $criteria->add(new TFilter('dtlanc','>=',"{$dtInicial}"));
                $this->dtsdInic = new DateTime($dtInicial);
                $this->dtsdInic->sub(new DateInterval('P1D'));
                $this->vlsdInic = $cta->getSaldoCC($this->dtsdInic->format('Y-m-d'));
                $this->headerDate .= 'a partir de '.$fdata->dtInicial;
            }
            elseif(empty($fdata->dtInicial) && !empty($fdata->dtFinal))
            {
                $criteria->add(new TFilter('dtlanc','<=',"{$dtFinal}"));
                $this->dtsdInic = new DateTime($cta->dtsdini);
                $this->vlsdInic = (float) $cta->vlsdini;
                $this->headerDate .= 'até '.$fdata->dtFinal;
            }
            elseif(!empty($fdata->dtInicial) && !empty($fdata->dtFinal))
            {
                $criteria->add(new TFilter('dtlanc','BETWEEN',"{$dtInicial}","{$dtFinal}"));
                $this->dtsdInic = new DateTime($dtInicial);
                $this->dtsdInic->sub(new DateInterval('P1D'));
                $this->vlsdInic = $cta->getSaldoCC($this->dtsdInic->format('Y-m-d'));
                $this->headerDate .= $fdata->dtInicial.' a '.$fdata->dtFinal;
            }
            else{ 
                $this->dtsdInic = new DateTime($cta->dtini);
                $this->vlsdInic = (float) $cta->sdini;
            }
            $criteria->setProperty('order','dtlanc');

            // instancia o relatorio
            $sTit = '';
            if( $filialname ) {
                $sTit = $filialname. '   -   '.$sTit;                
            }

            $relCad = new TBLReport(__CLASS__);
            $relCad->setTitle('Extrato de Movimentação');
            if(!empty($fdata->conta))
                $sTit.= $cta->descricao;

            if(!empty($fdata->finplanoconta_id)) {
                $sTit.=(!empty($sTit) ? '  /  ' : '').'('.$res->ordem.') '.$res->nome;
            }
            TTransaction::close();
            $relCad->setSubTitle($sTit);

            $relCad->setMainDataSource(_DATABASE_, 'Finlanca', $criteria);

            if(!empty($fdata->conta))
                $relCad->setBreakBody('finconta->sigla', [$this,'onSaldoAnt']);

            // Monta as colunas
            $relCad->setMainHeader( );
            $relCad->addMainHeader(0, 50, 'Data','C/L');
            $relCad->addMainHeader(0, 30, 'ID','C/R');
            $relCad->addMainHeader(0, 40, 'C/C');
            $relCad->addMainHeader(0, 45, 'C.Resumo','C/C');
            $relCad->addMainHeader(0,195, 'Histórico');
            $relCad->addMainHeader(0, 50, 'Nº Doc');
            $relCad->addMainHeader(0, 50, 'Valor','C/R');
            $relCad->addMainHeader(0, 60, 'Saldo','C/R');
            $relCad->addMainHeader(0, 20, 'Orig','C/R');

            $relCad->addMainField(0, 'dtlanc', NULL, [$this, 'formatDate']);
            $relCad->addMainField(0, 'id');
            $relCad->addMainField(0, 'finconta->sigla');
            $relCad->addMainField(0, 'finplanoconta->ordem');
            $relCad->addMainField(0, 'descricao');
            $relCad->addMainField(0, 'nrdoc');
            $relCad->addMainField(0, 'valor', NULL, [$this, 'formatValue']);
            $relCad->addMainField(0, 'saldo', NULL, [$this, 'calcSaldo']);
            $relCad->addMainField(0, 'origem', NULL, [$this, 'lbOrigem']);

            $relCad->setSubHeader([$this,'onTituloAux']);

            $relCad->printRep();
            $this->form->setData($fdata);
        }
        catch (Exception $e) {
            new TMessage('error',$e->getMessage());
        }
    }

    public function onTituloAux($rep)
    {
        if($this->headerDate <> 'Período: ')
            $rep->printLine($this->headerDate,'left','titulo2');
    }

    public function onSaldoAnt($rep) 
    {
        $rep->applyStyle('datai');
        $rep->pdf->Cell( 50,  12, $this->dtsdInic->format('d/m/Y'));
        $rep->pdf->Cell(115,  12, '');
        $rep->pdf->Cell(195,  12, 'Saldo Anterior');
        $rep->pdf->Cell(120,  12, '');
        $rep->pdf->Cell( 40,  12, TBLFuncoes::fmtNumber($this->vlsdInic),0,0,'R');
        $rep->pdf->Cell( 20,  12, '');
        $rep->pdf->ln();
    }

    public function formatDate($content) {
        $dtFormat = '';
        if($content <> $this->dtsdInic){
            $this->dtsdInic = $content;
            $dtFormat = TDate::date2br($content);
        }
        return $dtFormat;
    }

    public function formatValue($content) {
        $this->vlsdInic += (float) $content;
        return TBLFuncoes::fmtNumber($content);
    }

    public function lbOrigem($content) {
        $ret = ['','CX','TF','PR'];
        return $ret[is_null($content) ? 0 : $content];
    }

    public function calcSaldo($content) {
        return TBLFuncoes::fmtNumber($this->vlsdInic);
    }

    public function onShow() {}
}
