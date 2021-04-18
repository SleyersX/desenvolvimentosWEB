<?php
/**
 * FinSaldoContasReport Report
 * @author  Adalberto Lima Vitorino (blsistemas50@gmail.com)
 */
class FinSaldoContasReport extends TPage
{
    protected $form;
    private $headerDate;
    private $dtsdInic;
    private $vlsdInic;

    function __construct()
    {
        parent::__construct();
        $this->form = new ParamsReportView(__CLASS__, 'Relação de Saldos de Contas', [$this,'onPrint']);

        $dtInicial = new TDate('dtInicial');
        $dtFinal   = new TDate('dtFinal');

        $dtInicial->setMask('dd/mm/yyyy');
        $dtFinal->setMask('dd/mm/yyyy');

        $dtInicial->setSize(150);
        $dtFinal->setSize(150);

        $dtInicial->addValidation('Data inicial', new TRequiredValidator);
        $dtFinal->addValidation('Data final', new TRequiredValidator);

        $this->form->addFields([new TLabel('Período: de ','red')], [$dtInicial, new TLabel('&nbsp;a&nbsp;'), $dtFinal]);

        //parent::add(TBreadCrumb::create(['Relatórios','Finanças']));
        parent::add($this->form);
    }
    
    function onPrint($param)
    {
        try
        {
            $fdata = $this->form->getData();
            $this->form->validate();
            $this->headerDate = 'Período: '.$fdata->dtInicial.' a '.$fdata->dtFinal;
            $dtInicial = TDate::date2us($fdata->dtInicial);
            $dtFinal = TDate::date2us($fdata->dtFinal);
            if(!empty($dtInicial) && !empty($dtFinal) && $dtInicial > $dtFinal)
                throw new Exception("Período inválido !");

            // instancia o relatorio
            $relCad = new TBLReport(__CLASS__);
            $relCad->setTitle('SALDOS DE CONTAS FINANCEIRAS');
            $relCad->setBody();
            $relCad->Header();

            $relCad->printLine('Período: '.$fdata->dtInicial.' a '.$fdata->dtFinal,'center','titulo1');
            $relCad->printRow();
            $relCad->setCols([20,140,90,90,90,90,20]);
            $relCad->addCell('','left','headersec');
            $relCad->addCell('Conta','left','headersec');
            $relCad->addCell('Saldo Anterior','right','headersec');
            $relCad->addCell('Créditos','center','headersec');
            $relCad->addCell('Débitos','center','headersec');
            $relCad->addCell('Saldo','center','headersec');
            $relCad->addCell('','left','headersec'); 
            $relCad->printRow();

            // Soma
            $estilo = 'balanc2';
            $res = [0.0, 0.0, 0.0, 0.0];
            $saldos = Finconta::getSaldosCC(NULL, $dtInicial, $dtFinal);
            if( !$saldos )
                throw new Exception("Sem registros para imprimir");                

            foreach ($saldos as $key => $value) 
            {
                $saldoinicial = (float) $value['sdini'] + (float) $value['credINI'] + (float) $value['debINI'];
                
                $res[0] += $saldoinicial;
                $res[1] += (float) $value['creditos'];
                $res[2] += (float) $value['debitos'];
                $res[3]  = $res[0] + $res[1] + $res[2];

                $relCad->addCell('','left',$estilo);
                $relCad->addCell($value['descricao'],'left',$estilo);
                $relCad->addCell(TBLFuncoes::fmtNumber($saldoinicial),'right',$estilo);
                $relCad->addCell(TBLFuncoes::fmtNumber($value['creditos']),'right',$estilo);
                $relCad->addCell(TBLFuncoes::fmtNumber($value['debitos']),'right',$estilo);
                $relCad->addCell(TBLFuncoes::fmtNumber($saldoinicial +
                                     (float) $value['creditos'] +
                                     (float) $value['debitos'] ),'right',$estilo);
                $relCad->addCell('','left',$estilo);
                $relCad->printRow();
            }

            $estilo = 'titulo2';
            $relCad->addCell('','left',$estilo);
            $relCad->addCell('Saldo','right',$estilo);
            $relCad->addCell(TBLFuncoes::fmtNumber($res[0]),'right',$estilo);
            $relCad->addCell(TBLFuncoes::fmtNumber($res[1]),'right',$estilo);
            $relCad->addCell(TBLFuncoes::fmtNumber($res[2]),'right',$estilo);
            $relCad->addCell(TBLFuncoes::fmtNumber($res[3]),'right',$estilo);
            $relCad->addCell('','left',$estilo);
            $relCad->printRow();

            $relCad->output();
            $this->form->setData($fdata);
        }
        catch (Exception $e) {
            new TMessage('error',$e->getMessage());
        }
    }

    public function onShow() {}
}
