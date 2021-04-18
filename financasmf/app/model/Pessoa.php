<?php
/**
 * Pessoa Active Record
 * @author  <your-name-here>
 */
class Pessoa extends TRecord
{
    const TABLENAME = 'pessoa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('nconhecido');
        parent::addAttribute('pfj');
        parent::addAttribute('cpfcnpj');
        parent::addAttribute('nrdocs');
        parent::addAttribute('telefone');
        parent::addAttribute('celular');
        parent::addAttribute('email');
        parent::addAttribute('contato');
        parent::addAttribute('endereco');
        parent::addAttribute('bairro');
        parent::addAttribute('cep');
        parent::addAttribute('cidade');
        parent::addAttribute('obs');
    }
}
