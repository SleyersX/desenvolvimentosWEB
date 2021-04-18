<?php 
/**
 * Write tables in PDF - Relatorios tabulares padronizados
 * @author Adalberto Lima Vitorino
 * Baseado no TTableWriterPDF - Pablo Dall'Oglio
 */
class TBLReport implements ITableWriter
{
    //  Parâmetros do relatorio
    public $pdf;
    private $format;
    private $nameReport;
    private $style;
    private $colour;
    private $styles;
    private $widths;
    private $fontface;
    private $fontsize;
    private $fontstyle;
    private $fontcolor;
    private $fillcolor;
    private $borderCell;
    private $colcounter;

    private $colsTemp;
    private $colcounterTemp;
    private $bodySections;
    private $bodyParams;

    //  Fonte de dados
    private $mainDataSource;
    private $database;
    private $table;
    private $filter;
    private $arrayData;
    private $objLine;
    private $statObjLine; // setar T/F se é para guardar o objeto da linha que esta sendo impressa

    //  Controle de impressão
    private $firstPage;         // controlar salto de linha adic no cabec da pag 1
    private $setHeader;         // seta o cabeçalho que esta ativo (main/detail)
    private $leftMargin;        // margem esquerda
    private $leftMarginDetail;  // margem esquerda detail
    private $widthsTemp;        // tamanho do cabeçalho --> relacionado ao detail
    private $flagFormat;        // formato: T-tabular default / F-body free
    private $printFooter;
    private $printHeader;

    //  Top
    public $scTopLogo;          // T/F - imprime ou não a logo
    public $scTopTitles;        // T/F - imprime ou não titulos
    public $scTopDateTime;      // T/F - imprime ou não data e hora
    public $scTopSubHeader;     // T/F - imprime ou não subtitulo
    // Cabeçalhos (Main / Detail)
    private $colsMain;
    public  $flagPrintHeaderMain;   // imprime o ColumnHeader principal (T/F)
    private $count;                 // contador de linhas impressas - main

    private $colsDetail;
    public  $flagPrintHeaderDetail; // imprime o ColumnHeader detalhe (T/F)
    private $detailFunc;
    private $detailLinkFields;
    private $widthsSec;
    private $countDetail;           // contador de linhas impressas - detail
    // Quebras de impressao
    private $bkBodyField;       // qual campo a comparar para quebra
    private $bkBeforeFunc;      // funcao a ser executada antes da impressão da linha de quebra
    private $bkBodyFunc;        // funcao a ser executada se não tiver quebra (somatorias)
    private $bkAfterFunc;       // funcao a ser executada depois da impressão da linha de quebra
    private $bkBodySep;         // separador apos impressão da quebra
    private $bkSepField;        // campo de quebra
    private $bkSepStyle;        // separar por 0-linha em branco / 0-traco
    //  Parâmetros publicos
    public $title;          // titulo
    public $subtitle;       // subtitulo
    public $borderHeader;   // 0-não 1-sim | impressão da borda celulas cabeçalho
    public $borderData;     // 0-não 1-sim | impressão da borda celulas de dados
    public $printSHOnlyFirstPage;   // imprime Subtitulo scTop somente na 1ª pag
    public $nameLogo;
    public $enableMsgEnd;
    public $rodape;
    public $zebrado;                // padrao zebrado (true) ou não (false)
    public $defaultStyle;
    private $StyleFunc;     // função para mudar o estilo da impressão da linha

    /**
     * Constructor
     * @param $logo (0-default sis / 1-empresa / 2-personalizada /3-sem logo)
     *        $nameLogo (caminho do arquivo, se logo personalizada)
     */
    public function __construct($nameReport='Report', $orientation='P', $lm = 0, $logo = 0, $emp = NULL, $nameLogo = NULL, $autoheader = TRUE)
    {
        $this->nameReport = $nameReport;
        $this->title = 'REPORT NAME';
        $this->subtitle = '';
        $this->format = 'pdf';
        $this->leftMargin = $lm;
        $this->leftMarginDetail = $lm;
        $this->zebrado=TRUE;
        $this->enableMsgEnd=FALSE;
        $this->flagFormat = 'T';
        $this->printHeader = TRUE;
        $this->printFooter = TRUE;
        $this->defaultStyle = TRUE;

        $this->widths    = array();
        $this->styles    = array();
        $this->columns   = array();
        setlocale(LC_ALL, 'POSIX');     // define o locale
                
        // cria o objeto FPDF
        $this->pdf = new FPDF($orientation, 'pt', 'A4');
        $this->pdf->Open();
        $this->pdf->AddPage();

        if($autoheader) {
            $this->pdf->setHeaderCallback(array($this,'Header'));
            $this->pdf->setFooterCallback(array($this,'Footer'));
            $this->pdf->AliasNbPages('{nb}');
        }

        $this->defStyles();
        $this->defLogo($logo, $emp, $nameLogo);

        // Inicializa seções
        $this->scTopLogo = TRUE;
        $this->scTopTitles = TRUE;
        $this->scTopDateTime = TRUE;
        $this->scTopSubHeader = NULL;
        $this->scSHTop = FALSE;
        $this->flagPrintHeaderMain = TRUE;

        // Controle de impressão
        $this->firstPage=FALSE;
        $this->printSHOnlyFirstPage=TRUE;
        $this->bkBodyField = NULL;
        $this->bkBeforeFunc  = NULL;
        $this->bkAfterFunc  = NULL;
        $this->bkSepField = NULL;
        $this->bkSepStyle = NULL;
        $this->StyleFunc = NULL;

        //$this->rodape=(!empty($this->nameReport) ? $this->nameReport.' - ':'').APPLICATION_NAME;
        $this->rodape='['.APPLICATION_NAME.'] '.(!empty($this->nameReport) ? $this->nameReport:'');
        $this->setHeader = 'main';        

        $this->statObjLine = FALSE; // marca para não guardar o obj que esta sendo impresso
    }

    /*
     * inicializa a impressão do relatorio
     */ 
    public function Initialize()
    {
        $this->Header();
        $this->addRow();
        $this->colour=FALSE;     
    }

    /*
     *  SETUP / FPDF
     */
    /*
     * Definição dos estilos
     * ToDO: padronizar isso nos params em tabela de config
     *       #ffffff branco        #000000 preto  
     */
    public function defStyles()
    {
        $this->addStyle('header',    'Arial', '09', 'BI', '#ffffff', '#407B49');
        $this->addStyle('headersec', 'Arial', '08', 'BI', '#000000', '#dbdbdb'); 
        $this->addStyle('datap',     'Arial', '09', '',   '#000000', '#e3ecf4');
        $this->addStyle('datai',     'Arial', '09', '',   '#000000', '#ffffff');
        $this->addStyle('title',     'Times', '14', 'B',  '#000000', '#FFF1B2');
        $this->addStyle('subtitle',  'Times', '11', 'BI', '#000000', '#FFF1B2');
        $this->addStyle('body',      'Arial', '09', '',   '#000000', '#FFFFFF');
        $this->addStyle('footer',    'Arial', '08', 'I',  '#2B2B2B', '#B5FFB4');
        $this->addStyle('blank',     'Times', '10', '',   '#ffffff', '#ffffff');
        $this->addStyle('titulo1',   'Times', '11', 'BIU', '#2B2B2B', '#B5FFB4');
        $this->addStyle('titulo2',   'Times', '11', 'B',   '#2B2B2B', '#B5FFB4');
        $this->addStyle('balanc1',   'Arial', '10', 'BI',  '#2B2B2B', '#eaeaea');
        $this->addStyle('balanc2',   'Arial', '09', '',    '#2B2B2B', '#ffffff');
    }

    public function newPage() {
        $this->pdf->AddPage();
    }

    /**
     * Add a new style
     * @param @stylename style name
     * @param @fontface  font face
     * @param @fontsize  font size
     * @param @fontstyle font style (B=bold, I=italic)
     * @param @fontcolor font color
     * @param @fillcolor fill color
     */
    public function addStyle($stylename, $fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $borderCell = 0)
    {
        $this->styles[$stylename] = array($fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $borderCell);
    }
    
    /**
     * Apply a given style
     * @param $stylename style name
     */
    public function applyStyle($stylename)
    {
        // verifica se o estilo existe
        if (isset($this->styles[$stylename]))
        {
            $style = $this->styles[$stylename];
            // obtém os atributos do estilo
            $this->fontface    = $style[0];
            $this->fontsize    = $style[1];
            $this->fontstyle   = $style[2];
            $this->fontcolor   = $style[3];
            $this->fillcolor   = $style[4];
            $this->borderCell  = $style[5];
            
            // aplica os atributos do estilo
            $this->pdf->SetFont($this->fontface, $this->fontstyle); // fonte
            $this->pdf->SetFontSize($this->fontsize); // estilo
            $colorarray = self::rgb2int255($this->fontcolor);
            // cor do texto
            $this->pdf->SetTextColor($colorarray[0], $colorarray[1], $colorarray[2]);
            $colorarray = self::rgb2int255($this->fillcolor);
            // cor de preenchimento
            $this->pdf->SetFillColor($colorarray[0], $colorarray[1], $colorarray[2]);
        }
    }
    
    /**
     * Convert one RGB color into array of decimals
     * @param $rgb String with a RGB color
     */
    private function rgb2int255($rgb)
    {
        $red   = hexdec(substr($rgb,1,2));
        $green = hexdec(substr($rgb,3,2));
        $blue  = hexdec(substr($rgb,5,2));        
        return array($red, $green, $blue);
    }
  
    /*
     *  FONTES DE DADOS
     */
    public function setMainDataSource($database, $table, $filter = NULL) 
    {
        $this->database = $database;
        $this->table  = $table;
        $this->filter = $filter == NULL ? new TCriteria : $filter;
        $this->mainDataSource = 'D';
    }

    public function setMainDataArray($arrData = []) 
    {
        $this->arrayData = $arrData;
        $this->mainDataSource = 'A';
    }

    /*
     *  COLUNAS TEMPORARIAS
     */
    public function setCols($cols = []) 
    {
        if($this->flagFormat == 'T') 
        {}
        else{
            $this->colsTemp = $cols;
            $this->colcounterTemp = 0;            
        }
    }

    /*
     *  COLUNAS CABEÇALHO PRINCIPAL
     */
    public function setMainHeader($bHeader = 0, $bData = 0) 
    {
        $this->setHeader = 'main';
        $this->borderHeader = $bHeader;
        $this->borderData = $bData;
        $this->colsMain = [];
    }    

    public function addMainHeader($line, $length, $label='', $align='left', $style='', $colspan=1) 
    {
        if(!isset($this->nrLinhamontaCabec) or $this->nrLinhamontaCabec <> $line) {
            $this->nrLinhamontaCabec = $line;
            $this->nrColmontaCabec = -1;
        }
        $this->addCols($line, ++$this->nrColmontaCabec, $length, $label, $align, $style, $colspan);
    } 

    public function addMainField($line, $fieldName, $calc=NULL, $transf=NULL, $sumCol=FALSE) 
    {
        if(!isset($this->nrLinhaMontaField) or $this->nrLinhaMontaField <> $line) {
            $this->nrLinhaMontaField = $line;
            $this->nrColMontaField = -1;
        }
        $obj = $this->colsMain[$line][++$this->nrColMontaField];
        $obj->field = $fieldName;
        $obj->calcField = $calc;
        $obj->transf = $transf;
        $obj->sumCol = $sumCol;
    }

    /*
     *  COLUNAS DE DETALHES
     */
    /*
     * setDetail
     * @param @func   array funcao a ser chamada -> retorna array com dados ou nada
     * @param @fields array com campos deverão ser enviados parâmetros p/ func
     * @param @margin margem p/ impressão do detail
     */
    public function setDetail($func, $fields, $margin = 0)
    {
        $this->setHeader = 'detail';
        $this->detailFunc = $func;
        $this->detailLinkFields = $fields;
        $this->leftMarginDetail = $margin;
        $this->colsDetail = [];

        $this->widthsSec    = array();
        $this->widthsTemp   = NULL;
    }

    public function addDetailHeader($length,$label='',$align='left',$style='',$colspan=1)
    {
        if(!isset($this->nrColmontaCabecDet))
            $this->nrColmontaCabecDet = -1;
        $this->addCols(0, ++$this->nrColmontaCabecDet, $length, $label, $align, $style, $colspan);
    } 

    public function addDetailField($fieldName='', $transf=NULL, $sumCol=FALSE) 
    {
        if(!isset($this->nrColMontaFieldDet))
            $this->nrColMontaFieldDet = -1;
        $obj = $this->colsDetail[0][++$this->nrColMontaFieldDet];
        $obj->field = $fieldName;
        $obj->transf = $transf;
        $obj->sumCol = $sumCol;
    }

    public function printDetail($set)
    {
        if($set == 'on') {
            $this->setHeader = 'detail';
            $this->countDetail = 0;
            $this->widthsTemp = $this->widths;
            $this->widths = $this->widthsSec;
            $this->printHeaderColumns('detail');            
        }
        else {
            // ver se tem linha de totais para imprimir
            $this->setHeader = 'main';
            $this->widths = $this->widthsTemp;            
        }
    }

    public function addCols($line, $nrCol, $length, $label='', $align='left', $style='', $colspan = 1)
    {
        if($this->flagFormat == 'T') 
        {
            $length = $length < strlen($label) ? strlen($label): $length; 
            if($this->setHeader == 'main')
                $this->widths[$line][] = $length;
            else
                $this->widthsSec[$line][] = $length;

            $alignCab='left';
            $alignDat='left';        
            if(!empty($align)) 
            {
                if(substr_count($align, '/') > 0) {
                    $pos = strpos($align, '/');
                    if($pos > 0) {
                        $alignCab=substr($align, 0, $pos);
                        $alignDat=substr($align, $pos+1);   
                    }
                    else
                        $alignDat=substr($align, 1);
                }
                else            
                   $alignCab=$align;
            }

            $styleCab = empty($style) ? ($this->setHeader == 'main' ? 'header':'headersec'):$style;

            $styleDat = $this->zebrado ? 'datap':'datai';
            if(!empty($style)) 
            {
                if(substr_count($style, '/') > 0) {
                    $pos = strpos($style, '/');
                    if($pos > 0) {
                        $styleCab=substr($style, 0, $pos); ;
                        $styleDat=substr($style, $pos+1);   
                    }
                    else
                        $styleDat=substr($style, 1);
                }
                else            
                    $styleCab=$style;
            }

            $colspanCab = 1;
            $colspanDat = 1;
            if(!empty($colspan)) 
            {
                if(substr_count($colspan, '/') > 0) {
                    $pos = strpos($colspan, '/');
                    if($pos > 0) {
                        $colspanCab=(int) substr($colspan, 0, $pos);
                        $colspanDat=(int) substr($colspan, $pos+1);   
                    }
                    else
                        $colspanDat=(int) substr($colspan, 1);
                }
                else            
                   $colspanCab=(int) $colspan;
            }

            $montaCol = new stdClass;
            $montaCol->label = $label;
            $montaCol->length = $length;
            $montaCol->align = $alignCab;
            $montaCol->style = $styleCab;
            $montaCol->colspan = $colspanCab;
            $montaCol->field = '';
            $montaCol->alignDat = $alignDat;
            $montaCol->styleDat = $styleDat;
            $montaCol->colspanDat = $colspanDat;
            $montaCol->transf = NULL;
            $montaCol->sumCol = NULL;

            if($this->setHeader == 'main')
                $this->colsMain[$line][$nrCol] = $montaCol;
            else
                $this->colsDetail[$line][$nrCol] = $montaCol;
        }
        else
        {

        }
    }

    /*
     *  IMPRESSÃO DO CABEÇALHO
     */
    /*
     * Ajuste da Logo
     */
    public function defLogo($logo, $emp, $nameLogo)
    {
        // logo default
        if($logo == 0) { 
            $this->nameLogo = 'app/images/logo.png';
        }
        // logo da empresa
        elseif($logo == 1) {
            // acessar a empresa padrao e pegar a logo pelo cod $emp
            // ToDO:acertar o cad da empresa primeiro
            $this->nameLogo = 'files/empresas/'.$emp.'/'.$nameLogo;
        }
        // logo personalizada
        elseif($logo == 2) {
            $this->nameLogo = $nameLogo;
        }
        // sem logo
        else
            $this->nameLogo = NULL;

        if ($this->nameLogo && !file_exists($this->nameLogo))
            $this->nameLogo = NULL;
    }

    /*
     * ajusta os titulos
     */ 
    public function setTitles($title='ReportName', $subtitle='')
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
    }

    public function setTitle($title='ReportName') {
        $this->title = $title;
    }

    public function setSubTitle($subtitle='') {
        $this->subtitle = $subtitle;
    }

    /*
     * ajusta cabec auxiliar 
     */ 
    public function setSubHeader($funcao) {
        $this->scTopSubHeader = $funcao;
    }

    public function Header($y = 25)
    {
        if($this->scTopLogo && !empty($this->nameLogo)) {
            $this->pdf->Image($this->nameLogo, 25, 25, 100, 30);
        }

        $this->applyStyle('title');
        $this->pdf->Cell(0,$this->fontsize*1.1,utf8_decode($this->title),0,0,'C');

        if($this->scTopDateTime) {
            $this->pdf->SetFont('Arial','I',8);
            $this->pdf->Cell(0,0,utf8_decode(date('d/m/Y')),0,0,'R');
            $this->pdf->Ln(12);
        }
        if(!empty($this->subtitle)){
            $this->applyStyle('subtitle');
            $this->pdf->Cell(0,$this->fontsize * 1.3,utf8_decode($this->subtitle),0,0,'C');
        }
        if($this->scTopDateTime) {
            $this->pdf->SetFont('Arial','I',8);
            $this->pdf->Cell(0,0,utf8_decode(date('H:i:s')),0,0,'R');
        }
        $this->pdf->Ln(20);

        if($this->scTopSubHeader)
        {
            if($this->printSHOnlyFirstPage && $this->firstPage) ;
            else {
                $this->pdf->Ln();  
                call_user_func($this->scTopSubHeader, $this);
            }
        }
        if($this->flagPrintHeaderMain)
            $this->printHeaderColumns('main');
    }

    /**
     * Imprime o cabeçalho de colunas
     * @param @qual valor: main/detail
     */
    public function printHeaderColumns($qual='main')
    {
        if($qual == 'main') {
            $lm = $this->leftMargin;
            $columns = $this->colsMain;
        }
        else {
            $lm = $this->leftMarginDetail;
            $columns = $this->colsDetail;
        }

        $tam = sizeof($columns);
        $i = 0;
        foreach($columns as $linhas=>$colunas) 
        {
            $i++;
            if($lm <> 0 ){
                $this->pdf->setX($lm);
            }
            foreach ($colunas as $col=>$cabec) 
            {                
                $borda = $cabec->style == 'header' ? $this->borderHeader : $this->borderData;
                $this->applyStyle($cabec->style);
                $this->pdf->Cell($cabec->length, 
                            $this->fontsize * 1.3,
                            utf8_decode($cabec->label), $borda, 0, 
                            strtoupper(substr($cabec->align,0,1)),TRUE);
            }
            if($i < $tam) {
                $this->pdf->Ln();
            }
        }
        if($this->firstPage) {
            $this->pdf->Ln(12);
        }
        else{
            $this->firstPage=TRUE;
        }
    } 

    /*
     *  IMPRESSÃO DO CORPO DO RELATÓRIO
     */
    public function printRep() 
    {
        try
        {
            if($this->mainDataSource == 'D') 
            {
                TTransaction::open(_DATABASE_);
                $className = $this->table;
                $instance = new $className();
                $this->arrayData = $instance::getObjects($this->filter);
            }            
            if(empty($this->arrayData)){
                throw new Exception('Sem dados para imprimir');
            }

            $this->Initialize();
   
            // Main Loop
            $varContent = NULL;
            if($this->bkBodyField) {
                $flag = -1;
            }

            foreach ($this->arrayData as $item=>$imprimir)                
            {
                // ALV*
                // var_dump($imprimir);
                if($this->statObjLine)
                    $this->objLine = $imprimir;

                // break Body
                $varTemp = $varContent;
                if($this->bkBodyField && 
                  (gettype($imprimir) == 'array' ? $imprimir[$this->bkBodyField] : $imprimir->{$this->bkBodyField}) <> $varContent) 
                {
                    $varContent = (gettype($imprimir) == 'array' ? $imprimir[$this->bkBodyField] : $imprimir->{$this->bkBodyField});

                    if(++$flag > 0) 
                    {
                        if($this->bkAfterFunc && $varTemp <> $varContent)      
                            call_user_func($this->bkAfterFunc, $this, $varContent);

                        if($this->bkBodySep == 0)
                            $this->addRow();
                        else                
                            $this->line();
                    }
                    if($this->bkBeforeFunc)
                        call_user_func($this->bkBeforeFunc, $this, $varContent);
                }

                // impressão da linha de dados principal
                $this->setHeader = 'main';
                $this->count = 0;
                foreach($this->colsMain as $linha=>$colunas)
                {
                    if($this->leftMargin <> 0)
                        $this->pdf->setX($this->leftMargin);
                    

                    foreach($colunas as $col=>$printDataCol)
                    {
                        $content = '';
                        if($printDataCol->field <> 'spaces')
                        {
                            if($printDataCol->field <> 'calc') 
                            {
                                $content = (gettype($imprimir) == 'array') ? 
                                            $imprimir[$printDataCol->field] :
                                            $imprimir->{$printDataCol->field}; 
                                if($this->bkBodyField && $this->bkBodyFunc) {
                                    call_user_func($this->bkBodyFunc, $this, $printDataCol->field, $content);                                    
                                }      
                                // transformer
                                if($printDataCol->transf) {
                                    $content = call_user_func($printDataCol->transf, $content);
                                }
                                // sumCol
                                if($printDataCol->sumCol) {
                                    // ToDO: acertar detalhes de soma
                                }
                            }
                            elseif($printDataCol->field == 'spaces') {
                                $content='';
                            }
                            else
                            {
                                $content = call_user_func($printDataCol->calcField, $imprimir);
                                if($this->bkBodyField && $this->bkBodyFunc){
                                    call_user_func($this->bkBodyFunc, $this, $printDataCol->field, $content);                                    
                                }
                            }
                        }
                        $this->printColumn($linha, $printDataCol, $content);
                    }
                    $this->printRow();
                }

                $this->count++;

                // detail
                if(isset($this->colsDetail))
                {      
                    $params = []; 
                    if(!empty($this->detailLinkFields)) {
                        foreach ($this->detailLinkFields as $key => $value) {
                            $params[] = gettype($imprimir) == 'object' ? $imprimir->{$value} : $imprimir[$value];
                        }
                    }
                    $arrData = call_user_func($this->detailFunc, $params);
                    // impressão de detalhes
                    if($arrData) 
                    {
                        $this->printDetail('on');
                        foreach ($arrData as $item=>$imprimir) 
                        {                            
                            foreach($this->colsDetail as $linha=>$colunas)
                            {
                                $this->pdf->setX($this->leftMarginDetail);
                                foreach($colunas as $col=>$printDataCol)
                                {
                                    $content = '';
                                    if($printDataCol->field <> 'spaces')
                                    {                                    
                                        $content = $imprimir[$printDataCol->field];
                                        // transformer
                                        if($printDataCol->transf) {
                                            $content = call_user_func($printDataCol->transf, $content);
                                        }
                                        // sumCol
                                        if($printDataCol->sumCol) {
                                            // ToDO: acertar detalhes de soma
                                        }
                                    }
                                    $this->printColumn($linha, $printDataCol, $content);
                                }
                                $this->printRow();
                            }
                            $this->countDetail++;
                        }
                        $this->printDetail('off');
                    }
                }
            }

            if($this->bkBodyField && $this->bkAfterFunc) {
                call_user_func($this->bkAfterFunc, $this, NULL);
            }      

            // ToDO: imprimir total de registros (config)

            if($this->mainDataSource == 'D')  {
                TTransaction::close(); 
            }

            $this->output( );
            
            if($this->enableMsgEnd)
                new TMessage('info','Relatório gerado com sucesso !');                
        }
        catch (Exception $e)
        {
            if($this->mainDataSource == 'D') { 
                TTransaction::rollback(); 
            }
            new TMessage('error',$e->getMessage(),NULL,' ');
        }
    }

    public function setObjLine()
    {
        $this->statObjLine = TRUE;
    }

    public function getObjLine()
    {
        return $this->objLine;
    }

    /*
     *          IMPRESSÃO DE LINHAS / COLUNAS / ETC
     */
    public function setLeftMargin($value) {
        $this->leftMargin = $value;
    }

    public function line($footer=FALSE)
    {
        $tam = 4;
        $this->pdf->Ln(); 
        $x=$this->pdf->getX();
        $y=$this->pdf->getY()-$tam;   // altura da linha 
        $y = $footer ? $y-$tam:$y;
        $this->pdf->Line($x,$y,$this->pdf->getPageWidth()-$x,$y);
    }

    /**
     * Imprime uma linha avulsa
     */
    public function printLine($content, $align='left', $stylename='', $colspan = 1, $lMargin = 0)
    {
        $lmTemp = $this->leftMargin;
        if($lMargin > 0 )
            $this->leftMargin = $lMargin;

        if($this->leftMargin <> 0 )
            $this->pdf->setX($this->leftMargin);
        if(!empty($stylename)) 
            $this->applyStyle($stylename);
        $this->pdf->Cell(0, $this->fontsize * 1.3, utf8_decode($content),0,0,strtoupper(substr($align,0,1))); 
        $this->pdf->Ln();
        
        if($lMargin > 0 )
            $this->leftMargin = $lmTemp;
    }    

    /**
     * Finaliza e imprime a linha do relatorio padrão
     */
    public function printRow()
    {
        $this->addRow();        
        $this->colour = !$this->colour;
    }
    
    /**
     * Add a new row inside the table
     */
    public function addRow()
    {
        $this->pdf->Ln();
        if($this->flagFormat == 'T') {
            $this->colcounter = 0;
        }
        else {
            $this->colcounterTemp = 0;
        }
    }
    
    public function printColumn($line, $coluna, $content='')
    {       
        $this->linePrint = $line;
        if($this->defaultStyle)
        {
            if($this->zebrado)
                $coluna->styleDat = $this->colour ? 'datap' : 'datai';            
        }

        $this->addCell($content, $coluna->alignDat, $coluna->styleDat, $coluna->colspanDat);
    }

    /**
     * Add a new cell inside the current row
     * @param $content   cell content
     * @param $align     cell align
     * @param $stylename style to be used
     * @param $colspan   colspan (merge) 
     */
    public function addCell($content, $align = 'L', $stylename='', $colspan = 1)
    {
        if(!$stylename && $this->zebrado)
            $stylename = $this->colour ? 'datap' : 'datai';

        if (is_null($stylename) OR !isset($this->styles[$stylename])) {
            throw new Exception(TAdiantiCoreTranslator::translate('Style ^1 not found in ^2', $stylename, __METHOD__ ) );
        }
            
        $this->applyStyle($stylename);
        if (utf8_encode(utf8_decode($content)) == $content ) // SE UTF8
        {
            $content = utf8_decode($content);
        }

        if($this->flagFormat == 'T') 
        {
            $width = 0;
            // calcula a largura da célula (incluindo as mescladas)
            for ($n=$this->colcounter; $n<$this->colcounter+$colspan; $n++) {
                $width += $this->widths[$this->linePrint][$n];
            }
            // exibe a célula com o conteúdo passado
            $this->pdf->Cell( $width, $this->fontsize * 1.3, $content, $this->borderData, 0, strtoupper(substr($align,0,1)), true);
            $this->colcounter += $colspan;
        }
        else
        {
            $widthTemp = 0;
            // calcula a largura da célula (incluindo as mescladas)
            for($n=$this->colcounterTemp; $n < $this->colcounterTemp + $colspan; $n++){
                $widthTemp += $this->colsTemp[$n];
            }
            // exibe a célula com o conteúdo passado
            $this->pdf->Cell( $widthTemp, $this->fontsize * 1.2, $content, $this->borderCell, 0, strtoupper(substr($align,0,1)), true);
            $this->colcounterTemp += $colspan;
        }
    }

    /*
     *  IMPRESSÃO DE QUEBRAS
     *  sep = NULL - nada / 1 - linha / 0 - linha me branco
     */
    public function setBreakBody($field, $fBefore=NULL, $fBody=NULL, $fAfter=NULL, $sep=0)
    {
        $this->bkBodyField  = $field;
        $this->bkBeforeFunc = $fBefore;
        $this->bkAfterFunc  = $fAfter;
        $this->bkBodyFunc   = $fBody;
        $this->bkBodySep    = $sep;
    }

    /*
     * Detalhes 
     * @param @field  campo que fara a quebra  
     * @param @style  values: 0: linha em branco / 1: traco
     */
    public function setSeparator($field, $style = 0)
    {
        $this->bkSepField = $field;
        $this->bkSepStyle = $style;
    }

    /*
     *  FOOTER
     */
    public function Footer() 
    { 
        if($this->printFooter) 
        {         
            $marginBottom = -55; // distancia do rodape ref ao final da pag
            $this->pdf->SetY($marginBottom);  
            if($this->borderData == 0)
                $this->line(TRUE);

            $this->applyStyle('footer');
            $this->pdf->Cell(0, 0, utf8_decode($this->rodape),0,0,'L');
            $this->pdf->Cell(0, 0, utf8_decode('Página ').$this->pdf->PageNo().' / {nb}',0,0,'R');
        }
    }    

    /*
     *  SAIDA / GRAVAÇÃO DO RELATÓRIO
     */
    public function output($dest = NULL, $title = '')
    {
        $repName = empty($title) ? $this->title : $title;
        $repName = str_replace(' ','_',$repName);
        $repName = str_replace('/','_',$repName);

        $folder = $dest ? $dest : 'app/output';    
            
        if(!file_exists($folder)) {
            if(!mkdir($folder)) {
                throw new Exception('Erro na criação da pasta: '."{$folder}");
            }
        }        
        $filenamereport = $folder.'/'.$repName.'.'.$this->format; 
        if (!file_exists($filenamereport) OR is_writable($filenamereport)) {
            $this->save($filenamereport);
        }
        else {
            throw new Exception(_t('Permission denied') . ': ' . $filenamereport);
        }
        if(!$dest){
            TPage::openFile($filenamereport);
        }
    }

    /**
     * Save the current file
     * @param $filename file name
     */
    public function save($filename)
    {
        $this->pdf->Output($filename);
        return TRUE;
    }

    /**
     * Impressão de relatorio em formato livre
     */
    public function setBody($header = TRUE, $footer = TRUE)
    {
        $this->flagFormat = 'F';
        $this->flagPrintHeaderMain = FALSE;
        $this->bodySections = [];
        $this->printHeader = $header;
        $this->printFooter = $footer;
    }

    public function addBody($section, $params)
    {
        $this->bodySections[] = $section;
        $this->bodyParams[] = $params;
    }

    public function printBody( )
    {
        $this->Initialize();
        foreach ($this->bodySections as $key => $value) { 
            call_user_func($value, $this, $this->bodyParams[$key]);
            $this->printRow();
        }
    }
}
