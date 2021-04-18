<?php 

class TBLFuncoes
{
    /**
     * Função fmtNumber() - formata numeros fracionados formato pt_BR
     *                      (retorna o fracionado corretamente)
     * @access public
     * @param $value = valor a ser formatado (double, float, currency)
     * @param $dec  = numero de casas decimais
     * @param $curr = incluir simbolo da moeda
     * @return TFilter com intervado de datas
     */
    public static function fmtNumber($value, $dec = 2, $curr=NULL)
    {
        $numberFormatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL );
        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $dec);
        return $curr.' '.$numberFormatter->format($value);
    }

    // recebe (string) 11,52 devolve (float) 11.52
    public static function numberUS($value, $dec = 2)
    {        
        $e1=str_replace('.','',$value);
        return (float) str_replace(',','.',$e1);
    }

    /**
     * Função makeFilterIntervalDate() - formato US
     * @access public
     * @param $field = campo p/ montagem do filtro
     * @param $dataIni, $dataFim - variável que tem valor de datas no formato
     * @return TFilter com intervado de datas
     */
    public static function makeFilterIntervalDate($field, $dataIni, $dataFim)    
    {
        if (empty($dataIni) AND empty($dataFim)){
                $filter = NULL;
        }        
        elseif (!empty($dataIni) AND !empty($dataFim)) {
            if($dataIni > $dataFim){
                $filter = new TFilter($field, 'BETWEEN', $dataFim.' 00:00:00', $dataIni.' 23:59:59');
            }
            elseif($dataIni == $dataFim) 
                $filter = new TFilter($field, 'like', $dataIni.'%');
            else
                $filter = new TFilter($field, 'BETWEEN', $dataIni.' 00:00:00', $dataFim.' 23:59:59');
        }
        else {
            if (!empty($dataIni) AND empty($dataFim))
                $filter = new TFilter($field, '>=', $dataIni.' 00:00:00');
            else
                $filter = new TFilter($field, '<=', $dataFim.' 23:59:59');
        }  
        return $filter;
    }


    // makeFilterIntervalValue
    public static function makeFilterIntervalValue($field, $valIni, $valFim)
    {
        $valIni = (float) $valIni * 1;
        $valFim = (float) $valFim * 1;

        if (empty($valIni) AND empty($valFim) OR $valIni == 0 AND $valFim == 0){
            $filter = NULL;
        }
        elseif (!empty($valIni) AND !empty($valFim)) 
        {
            if($valIni > $valFim){
                $filter = new TFilter($field, 'BETWEEN', $valFim, $valIni);
            }
            elseif($valIni == $valFim) 
                $filter = new TFilter($field, '=', $valIni);
            else
                $filter = new TFilter($field, 'BETWEEN', $valIni, $valFim);
        }
        else {
            if (!empty($valIni) AND empty($valFim))
                $filter = new TFilter($field, '>=', $valIni);
            else
                $filter = new TFilter($field, '<=', $valFim);
        }  
        return $filter;
    }

    // retorna msg de erro de banco mais amigaveis
    public static function msgErrors($e)
    {
        $msg = $e->getMessage();
        if( get_class($e) == 'PDOException')
        {
            $dberrors = [
                [1451, 'Este registro não pode ser excluído. Outras tabelas que dependem dele !'],
                [1062, 'Número do ID duplicado']
            ]; 
            foreach($dberrors as $key=>$error) {
                if ($error[0] == $e->errorInfo[1]) {
                    $msg = $error[1] . '<br>[Code: '.$e->errorInfo[1].']';
                    break;
                }
            }
        }
        return $msg;
    }   

    // transforma um vetor em StdClass
    public static function fromArray($data)
    {        
        $obj = new StdClass;
        if(is_array($data)) {
            foreach ($data as $key => $value) {
                $obj->$key = $value;
            }            
        }
        return $obj;
    }
}
