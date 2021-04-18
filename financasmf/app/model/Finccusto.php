<?php
/**
 * Finccusto Active Record
 * @author  <your-name-here>
 */
class Finccusto extends TRecord
{
    const TABLENAME = 'finccusto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('sigla');
    }
}
