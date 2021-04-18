<?php
/**
 * FinBalanceteReport Report
 * @author  Adalberto Lima Vitorino (blsistemas50@gmail.com)
 */
class FinBalanceteReport extends TPage
{
    protected $form;
    private $headerDate;
    private $dtsdInic;
    private $vlsdInic;

    function __construct()
    {
        parent::__construct();
        $this->form = new ParamsReportView(__CLASS__, 'Balancete Financeiro', [$this,'onPrint']);

        // Somente variáveis controles do relatorio especifico em questão
        $dtInicial = new TDate('dtInicial');
        $dtFinal   = new TDate('dtFinal');
        $categoria = new TDBCombo('categoria',_DATABASE_,'Finccusto','id','descricao', 'descricao');
        $finfilial_id = new TDBCombo('finfilial_id',_DATABASE_,'Finfilial','id','descricao', 'descricao');

        $dtInicial->setMask('dd/mm/yyyy');
        $dtFinal->setMask('dd/mm/yyyy');

        $dtInicial->setSize(150);
        $dtFinal->setSize(150);
        $categoria->setSize('100%');

        $dtInicial->addValidation('Data inicial', new TRequiredValidator);
        $dtFinal->addValidation('Data final', new TRequiredValidator);

        $this->form->addFields([new TLabel('Período: de ','red')], [$dtInicial, new TLabel('&nbsp;a&nbsp;'), $dtFinal]);
        $this->form->addFields([new TLabel('Filial')], [$finfilial_id],[]);
        $this->form->addFields([new TLabel('Centro de Custos')], [$categoria],[]);

        //parent::add(TBreadCrumb::create(['Relatórios','Finanças']));
        parent::add($this->form);
    }
    
    function onPrint($param)
    {
        try
        {
            $fdata = $this->form->getData();

            $fdata->resumo = 'A';
            $this->form->validate();
            $this->headerDate = 'Período: '.$fdata->dtInicial.' a '.$fdata->dtFinal;
            $dtInicial = TDate::date2us($fdata->dtInicial);
            $dtFinal = TDate::date2us($fdata->dtFinal);

            if(!empty($dtInicial) && !empty($dtFinal) && $dtInicial > $dtFinal){
                throw new Exception("Período inválido !");
            }

            $objects = Finplanoconta::getBalancete($dtInicial, $dtFinal, 'N', $fdata->categoria, $fdata->finfilial_id);

            // instancia o relatorio
            $relCad = new TBLReport(__CLASS__);
            $relCad->setTitle('Balanço Financeiro');
            TTransaction::open(_DATABASE_);
            if( $fdata->finfilial_id ) {
                $filial = new Finfilial($fdata->finfilial_id);
                $relCad->setSubTitle( 'FILIAL --> '.$filial->descricao );
            }

            if( !empty($fdata->categoria) ){
                $cCusto = new Finccusto($fdata->categoria);
                $relCad->setSubTitle('C.Custos --> '.strtoupper($cCusto->descricao), 'left', 'subtitle');
            }
            TTransaction::close();

            $relCad->setBody();
            $relCad->Header();

            $relCad->printLine('Período: '.$fdata->dtInicial.' a '.$fdata->dtFinal,'center','titulo1');
            $relCad->setCols([50,220,90,90,90]);
            $relCad->addCell('Nº Conta','left','header');
            $relCad->addCell('Descrição','left','header');
            $relCad->addCell('Créditos','right','header');
            $relCad->addCell('Débitos','right','header');
            $relCad->addCell('Saldo','center','header');
            $relCad->printRow();

            $fv = function($value) {
                return number_format($value, 2, ',', '.');
            };

            $res = [];
            $res['1'] = ['',0.0,0.0,0.0];
            $res['2'] = ['',0.0,0.0,0.0];
            $res['T'] = ['',0.0,0.0,0.0];

            foreach($objects as $key => $value) 
            {
                $estilo = 'balanc2';
                if(strlen($value['ordem']) < ($fdata->resumo == 'R' ? 4:6)) 
                {
                    $relCad->printRow();
                    $estilo = 'balanc1';
                }

                $relCad->addCell($value['ordem'],'left',$estilo);
                $relCad->addCell($value['nome'],'left',$estilo);
                $relCad->addCell($fv($value['creditos']),'right',$estilo);
                $relCad->addCell($fv($value['debitos']),'right',$estilo);
                $relCad->addCell($this->calcSaldo($value),'right',$estilo);
                $relCad->printRow();

                /**
                if($value['ordem'] == '1' OR $value['ordem'] == '2') {
                    $res[$value['ordem']] = 
                        [ $value['nome'], $value['creditos'], $value['debitos'],
                         (float) $value['creditos'] - (float) $value['debitos']];
                }
                **/
            }
            
            /***************
            // Soma
            $cr = (float) $res['1'][1] + (float) $res['2'][1];
            $db = (float) $res['1'][2] + (float) $res['2'][2];
            $res['T'] = [ '', ($cr), ($db), ($cr-$db) ];

            $relCad->printRow();
            $relCad->printRow();
            $relCad->printLine('RESULTADO DO BALANÇO','center','subtitle');
            $relCad->setCols([30,210,90,90,90,30]);
            $relCad->addCell('','left','headersec');
            $relCad->addCell('Descrição','left','headersec');
            $relCad->addCell('Créditos','center','headersec');
            $relCad->addCell('Débitos','center','headersec');
            $relCad->addCell('Saldo','center','headersec');
            $relCad->addCell('','left','headersec');
            $relCad->printRow();

            $estilo = 'balanc2';
            $relCad->addCell('','left',$estilo);
            //$relCad->addCell($res['1'][0],'left',$estilo);
            $relCad->addCell('R E C E I T A S','left',$estilo);
            $relCad->addCell($fv($res['1'][1]),'right',$estilo);
            $relCad->addCell($fv($res['1'][2]),'right',$estilo);
            $relCad->addCell($fv($res['1'][3]),'right',$estilo);
            $relCad->addCell('','left',$estilo);
            $relCad->printRow();

            $relCad->addCell('','left',$estilo);
            //$relCad->addCell($res['2'][0],'left',$estilo);
            $relCad->addCell('D E S P E S A S','left',$estilo);
            $relCad->addCell($fv($res['2'][1]),'right',$estilo);
            $relCad->addCell($fv($res['2'][2]),'right',$estilo);
            $relCad->addCell($fv($res['2'][3]),'right',$estilo);
            $relCad->addCell('','left',$estilo);
            $relCad->printRow();

            $estilo = 'titulo2';
            $relCad->addCell('','left',$estilo);
            $relCad->addCell($res['T'][0],'right',$estilo);
            $relCad->addCell($fv($res['T'][1]),'right',$estilo);
            $relCad->addCell($fv($res['T'][2]),'right',$estilo);
            $relCad->addCell($fv($res['T'][3]),'right',$estilo);
            $relCad->addCell('','left',$estilo);
            $relCad->printRow();
            ********/

            // $relCad->line();
            $relCad->printRow();
            $relCad->printRow();
            
            $relCad->printLine('POSIÇÃO DE CONTAS CORRENTES','center','subtitle');
            $relCad->setCols([20,140,90,90,90,90,20]);
            $relCad->addCell('','left','headersec');
            $relCad->addCell('Conta','left','headersec');
            $relCad->addCell('Saldo Anterior','right','headersec');
            $relCad->addCell('Créditos','center','headersec');
            $relCad->addCell('Débitos','center','headersec');
            $relCad->addCell('Saldo','center','headersec');
            $relCad->addCell('','left','headersec'); $relCad->printRow();

            // Soma
            $estilo = 'balanc2';
            $res = [0.0, 0.0, 0.0, 0.0];
            $saldos = Finconta::getSaldosCC(NULL, $dtInicial, $dtFinal);

            if(!$saldos) {
                throw new Exception("Ocorreu um erro na geração dos saldos das contas");
            }

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
            $relCad->addCell($fv($res[0]),'right',$estilo);
            $relCad->addCell($fv($res[1]),'right',$estilo);
            $relCad->addCell($fv($res[2]),'right',$estilo);
            $relCad->addCell($fv($res[3]),'right',$estilo);
            $relCad->addCell('','left',$estilo);
            $relCad->printRow();

            $relCad->output();
            $this->form->setData($fdata);
        }
        catch (Exception $e) {
            new TMessage('error',$e->getMessage());
        }
    }

    public function formatDate($content) {
        $dtFormat = '';
        if($content <> $this->dtsdInic)
        {
            $this->dtsdInic = $content;
            $dtFormat = TDate::date2br($content);
        }
        return $dtFormat;
    }

    public function formatValue($content) {
        $this->vlsdInic += (float) $content;
        return number_format($content, 2, ',', '.');
    }

    public function calcSaldo($content) {
        $sd = (float) $content['creditos'] - (float) $content['debitos'];
        return number_format($sd, 2, ',', '.');
    }

    public function onShow() {}
}
