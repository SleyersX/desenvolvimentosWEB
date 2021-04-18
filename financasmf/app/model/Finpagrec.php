<?php
/**
 * Finpagrec Active Record
 * @author  <your-name-here>
 */
class Finpagrec extends TRecord
{
    const TABLENAME = 'finpagrec';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    private $finlanca;
    private $finccusto;
    private $pessoa;
    private $filial;
    private $finplanoconta;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo');
        parent::addAttribute('dtinc');
        parent::addAttribute('userinc');
        parent::addAttribute('finfilial_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('dtemiss');
        parent::addAttribute('dtvenc');
        parent::addAttribute('nrdoc');
        parent::addAttribute('valor');
        parent::addAttribute('multa');
        parent::addAttribute('jurosdia');
        parent::addAttribute('nrparc');
        parent::addAttribute('totparc');
        parent::addAttribute('referencia');
        parent::addAttribute('finccusto_id');
        parent::addAttribute('finplanoconta_id');
        parent::addAttribute('obs');
        parent::addAttribute('dtbaixa');
        parent::addAttribute('finlanca_id');
    }

    public function set_finfilial(Finfilial $object)
    {
        $this->finfilial = $object;
        $this->finfilial_id = $object->id;
    }
    
    public function get_finfilial()
    {
        if (empty($this->finfilial))
            $this->finfilial = new Finfilial($this->finfilial_id);
        return $this->finfilial;
    }

    public function get_parcela()
    {
        if(!empty($this->nrparc) OR !empty($this->totparc)){
            return $this->nrparc.' / '.$this->totparc;
        }
        else{
            return '';        
        }
    }

    public function set_finlanca(Finlanca $object)
    {
        $this->finlanca = $object;
        $this->finlanca_id = $object->id;
    }
    
    public function get_finlanca()
    {
        if (empty($this->finlanca)){
            $this->finlanca = new Finlanca($this->finlanca_id);
        }
        return $this->finlanca;
    }
    
    public function get_vlliquido()
    {
        if (empty($this->dtbaixa))
            return $this->valor;
        else{
            return $this->finlanca->valor;
        }
    }

    public function set_finccusto(Finccusto $object)
    {
        $this->finccusto = $object;
        $this->finccusto_id = $object->id;
    }
    
    public function get_finccusto()
    {
        if (empty($this->finccusto)){
            $this->finccusto = new Finccusto($this->finccusto_id);
        }
        return $this->finccusto;
    }
    
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    public function get_pessoa()
    {
        if (empty($this->pessoa)){
            $this->pessoa = new Pessoa($this->pessoa_id);
        }
        return $this->pessoa;
    }
    
    public function get_contatonome()
    {
        if (empty($this->pessoa)){
            $this->pessoa = new Pessoa($this->pessoa_id);
        }
        return $this->pessoa->nomess;
    }

    public function set_finplanoconta(Finplanoconta $object)
    {
        $this->finplanoconta = $object;
        $this->finplanoconta_id = $object->id;
    }
    
    public function get_finplanoconta()
    {
        if (empty($this->finplanoconta)){
            $this->finplanoconta = new Finplanoconta($this->finplanoconta_id);
        }
        return $this->finplanoconta;
    }

    public function onBeforeStore($obj)
    {
        if( empty($obj->id) )
        {
            $obj->dtinc = date('Y-m-d H:i:s');
            $obj->userinc = TSession::getValue('username');
        }
    }
}
