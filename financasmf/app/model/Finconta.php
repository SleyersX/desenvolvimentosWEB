<?php
/**
 * Finconta Active Record
 * @author  <your-name-here>
 */
class Finconta extends TRecord
{
    const TABLENAME = 'finconta';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('sigla');
        parent::addAttribute('descricao');
        parent::addAttribute('ativa');
        parent::addAttribute('dtini');
        parent::addAttribute('sdini');
    }

    // Retorna o saldo de uma C/C ate data especifica
    public function getSaldoCC($dtLimite)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $conn = TTransaction::get();       
            $sth = $conn->prepare('select sum(valor) as lancs from finlanca where finconta_id = ? and dtlanc <= ?');
            $sth->execute([$this->id, $dtLimite]);            
            $result = $sth->fetchAll();
            TTransaction::close();
            return (float) $this->sdini + (float) $result[0]['lancs'];
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    } 

    // sqLite
    public static function SaldoCC($cc, $dtLimite, $sdini = NULL)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            if(is_null($sdini)) {
                $contaCorrente = new Finconta($cc);
                $saldoinicial = $contaCorrente->sdini;
            }
            else {
                $saldoinicial = $sdini;
            }

            $conn = TTransaction::get();       
            $sth = $conn->prepare('select sum(valor) as lancs from finlanca where finconta_id = ? and dtlanc <= ?');
            $sth->execute([$cc, $dtLimite]);            
            $result = $sth->fetchAll();
            TTransaction::close();
            return (float) $saldoinicial + (float) $result[0]['lancs'];
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }    

    // sqLite
    public static function debcredCC($dc, $cc, $dtini, $dtfim)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $conn = TTransaction::get();       
            $sth = $conn->prepare('select sum(valor) as soma from finlanca where finconta_id = ? and valor '.($dc == 'C' ? '>=' : '<').' 0 and dtlanc between ? and ?');
            $sth->execute([$cc, $dtini, $dtfim]);
            $result = $sth->fetchAll();
            TTransaction::close();
            return (float) $result[0]['soma'];
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }    

    // format date: us
    public static function getSaldosCC($idCC=NULL, $dIni, $dFim=NULL, $balanco=NULL)
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $conn = TTransaction::get();            
            $sql = 'select a.id, a.sigla, a.descricao, a.ativa, a.dtini, a.sdini, ';
            // Levanta sd da dt inic da cta até a dt inic do relat -1 dia            
            // Calcular dIni -1 dia
            $dcIni = new DateTime($dIni);
            $dcIni->sub(new DateInterval('P1D'));
            $dcfIni = $dcIni->format('Y-m-d');

            $dcFim = new DateTime($dFim);
            $dcfFim = $dcFim->format('Y-m-d');

            // SIMPLIFICAR ESTA SQL
            // creditos e debitos
            $sql.= "(select sum(valor) from finlanca b where b.finconta_id = a.id and dtlanc between a.dtini and '{$dcfIni}' and debcred = 'C' group by b.finconta_id) as credINI, 
                    (select sum(valor) from finlanca b where b.finconta_id = a.id and dtlanc
                between a.dtini and '{$dcfIni}' and debcred = 'D' group by b.finconta_id) 
                as debINI, ";

            $sql.= "(a.sdini + 
                (select sum(valor) from finlanca b where b.finconta_id = a.id and dtlanc between a.dtini and '{$dcfIni}' and debcred = 'C' group by b.finconta_id) + 
                (select sum(valor) from finlanca b where b.finconta_id = a.id and dtlanc between a.dtini and '{$dcfIni}' and debcred = 'D' group by b.finconta_id) 
                ) as saldoINI, ";

            // Levanta creditos e debitos da data inicial até a data final
            $dcIni = new DateTime($dIni);
            $dcfIni = $dcIni->format('Y-m-d');

            $sql.= "(select sum(valor) from finlanca b where b.finconta_id = a.id and dtlanc between '{$dcfIni}' and '{$dcfFim}' and debcred = 'C' group by b.finconta_id) as creditos, 
                    (select sum(valor) from finlanca b where b.finconta_id = a.id and dtlanc 
                between '{$dcfIni}' and '{$dcfFim}' and debcred = 'D' group by b.finconta_id) as debitos ";

            // gerar a variavel de saldo aqui tb

            // data do ultimo movimento dentro do limite de datas
            // $sql.= "(select max(c.dtlanc) from finlanca c where c.conta_id = a.id and dtlanc <= '{$dcfFim}' order by dtlanc) as dtult ";

            $sql.= " from finconta a ";
            if($idCC) {
                $sql.= "where a.id = {$idCC} ";
            }
            if($balanco) {
                //$sql.= ($idCC ? " and ":" where ")."a.fcustos = 'S' ";
            }
            $sql.= " order by a.descricao ;";

            //echo $sql.'<br>';

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
