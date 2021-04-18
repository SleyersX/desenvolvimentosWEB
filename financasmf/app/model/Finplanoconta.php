<?php
/**
 * Finplanoconta Active Record
 * @author  <your-name-here>
 */
class Finplanoconta extends TRecord
{
    const TABLENAME = 'finplanoconta';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ordem');
        parent::addAttribute('nome');
        parent::addAttribute('nomecurto');
        parent::addAttribute('tipolanc');
        parent::addAttribute('ativa');
    }

    public function get_nome_fmt()
    {
        $ordem = $this->ordem;
        if(strlen($ordem) < strlen(_NIVEISPC_))
            $ordem = '';
        else
            $ordem.=' - ';
        return $ordem.$this->nome;
    }

    public static function getBalancete($dtini, $dtfinal, $zeradas, $categoria, $filial)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $conn = TTransaction::get();     
            
            $sql = "SELECT cc.ordem, cc.nome, sum(credito) AS creditos, sum(debito) AS debitos FROM finplanoconta cc ";
            $sql.= $zeradas == 'S' ? ' left ':'';

            $db = parse_ini_file("app/config/"._DATABASE_.".ini");
            if( $db['type'] == 'sqlite'){
                $sql.= "JOIN view_finlanca1 m ON m.ordem LIKE cc.ordem || '%'";
            }
            elseif($db['type'] == 'mysql'){
                $sql.= "JOIN view_finlanca1 m ON m.ordem LIKE concat(cc.ordem,'%')";   // mysql
                //$sql.= "JOIN view_finlanca1 m ON m.ordem LIKE cc.ordem||'%'";            // sqlite
            }
            
            if($dtini OR $dtfinal OR $categoria) {
                $sql.=' where ';
            }
            if($filial) {
                $sql.= 'm.filial_id = '.$filial;
            }
            if($categoria){
                $sql.= $filial ? ' and ' : ' ';
                $sql.= 'm.ccusto_id = '.$categoria;
            }
            if($dtini OR $dtfinal) 
            {
                $sql.= ($categoria OR $filial) ? ' and ':'';
                if($dtini && $dtfinal)
                    $sql.= 'm.dtlanc between "'.$dtini.'" and "'.$dtfinal.'"';
                elseif($dtini)
                    $sql.= 'm.dtlanc >= "'.$dtini.'"';
                else
                    $sql.= 'm.dtlanc <= "'.$dtfinal.'"';
            }
            
            $sql.= ' GROUP BY cc.ordem, cc.nome ORDER BY cc.ordem ASC;';

            $sth = $conn->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();
            TTransaction::close();
            return $result;
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    } 

    public static function getResumo($ini, $fim)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $conn = TTransaction::get();     

            //$sql  = 'call zeraPlanoContas();';
            $sql = 'call resumoMov(1);';
            //$sql .= 'call totalizaPlanoContas();';
            $sth = $conn->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();

            TTransaction::close();
            return $result;
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    } 
}
