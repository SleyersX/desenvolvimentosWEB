<?php
/**
 * Form generico de parametros para impressão
 * @author  Adalberto Lima Vitorino - alivitor@gmail.com
 */

class ParamsReportView extends BootstrapFormBuilder
{
    private $teste;
    private $SizeWin;

    public function __construct($name = 'param', $titulo = 'Relatório', $btPrint = NULL, $btClear = NULL, $btBack = NULL, $btConfig = NULL, $sizewin = '70%', $param = NULL, $btExport = NULL)
    {
        parent::__construct('form_'.$name);
        parent::setFormTitle($titulo);

        /*
        $id2= new TEntry('id2');
        parent::addFields([new TLabel('ID')],[$id2]);
        colocar botao na barra de titulo p/ setar:
        imprimir linha de filtro / orientação / nome do arquivo / 
        enviar relatorio por email / etc / cor da quebra da linha
        colocar na classe TBLReports
        $this->form->addHeaderAction('config',new TAction(['','onConfig']),'fa:cog blue');
        */
        if($btConfig)
            parent::addHeaderAction('Config',new TAction([$this,'onConfig']),'fa:cog blue');
        
        parent::addAction(_t('Print'), new TAction($btPrint),'fa:print');
        if(!empty($btClear))
            parent::addAction(_t('Clear'), new TAction($btClear),'fa:eraser blue');

        if(!empty($btExport))
            parent::addAction(_t('Export'), new TAction($btExport),'fa:file-excel-o green');

        /*
        if(empty($btBack)){
            $referer = strpos($_SERVER['HTTP_REFERER'],'=');
            $referer = substr($_SERVER['HTTP_REFERER'], $referer+1);            
        }
        else
        */
        if(!empty($btBack))
        {
            $referer =  $btBack;
            parent::addAction(_t('Back'), new TAction([$referer,'onShow']),'fa:arrow-circle-o-left blue');
        }

        // vertical box container
        $container = new TVBox;
        $container->style = "width: {$this->SizeWin}";
        parent::add($container);
    }

    public function setSizeWin($value)
    {
        $this->SizeWin = $value;
    }
}
