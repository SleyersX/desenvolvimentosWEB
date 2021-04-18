<?php
/**
 * Finlanca Active Record
 * @author  <your-name-here>
 */
class Finlanca extends TRecord
{
    const TABLENAME = 'finlanca';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    //const CACHECONTROL = 'TAPCache';
    
    private $finconta;
    private $finccusto;
    private $finplanoconta;
    private $pessoa;
    private $filial;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('origem');
        parent::addAttribute('dtlanc');
        parent::addAttribute('finconta_id');
        parent::addAttribute('finccusto_id');
        parent::addAttribute('finfilial_id');
        parent::addAttribute('finplanoconta_id');
        parent::addAttribute('finconcilia_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('descricao');
        parent::addAttribute('debcred');
        parent::addAttribute('acresc');
        parent::addAttribute('desconto');
        parent::addAttribute('valor');
        parent::addAttribute('nrdoc');
        parent::addAttribute('obs');
        parent::addAttribute('userinc');
        parent::addAttribute('dtinc');
        parent::addAttribute('idlock');
    }

    public function set_finconta(Finconta $object)
    {
        $this->finconta = $object;
        $this->finconta_id = $object->id;
    }
    
    public function get_finconta()
    {
        if (empty($this->finconta))
            $this->finconta = new Finconta($this->finconta_id);
        return $this->finconta;
    }

    public function set_finccusto(Finccusto $object)
    {
        $this->finccusto = $object;
        $this->finccusto_id = $object->id;
    }
    
    public function get_finccusto()
    {
        if (empty($this->finccusto))
            $this->finccusto = new Finccusto($this->finccusto_id);
        return $this->finccusto;
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
    
    public function set_finplanoconta(Finplanoconta $object)
    {
        $this->finplanoconta = $object;
        $this->finplanoconta_id = $object->id;
    }
    
    public function get_finplanoconta()
    {
        if (empty($this->finplanoconta))
            $this->finplanoconta = new Finplanoconta($this->finplanoconta_id);
        return $this->finplanoconta;
    }

    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }
    
    public function get_pessoa()
    {
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
        return $this->pessoa;
    }
    
    public function onBeforeStore($object)
    {
        if(empty($object->id)) 
        {
            $object->userinc = TSession::getValue('userid');
            $object->dtinc = date('Y-m-d H:i:s');
        }
    }
}
