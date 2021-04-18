<?php
/**
 * Finfilial Active Record
 * @author  <your-name-here>
 */
class Finfilial extends TRecord
{
    const TABLENAME = 'finfilial';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }
}
