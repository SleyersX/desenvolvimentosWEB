<?php
/**
 * DefaultFormView
 * @author  <your name here>
 */
class DefaultFormView extends BootstrapFormBuilder
{
    public function __construct($fieldSigla = NULL, $ftam = 10, $fUpper = NULL)
    {
        parent::__construct();
        
        TSession::setValue('setWidthWCadAux', .5);
        TSession::setValue('setHeigthWCadAux', 225);

        $id    = new TEntry('id');
        $descricao = new TEntry('descricao');
        if($fieldSigla)
            $sigla = new TEntry('sigla');
        
        $id->setSize('60');
        $id->setEditable(FALSE);
        $descricao->setMaxLength(40);
        $descricao->setSize('100%');
        $descricao->autofocus = 'autofocus';
        $descricao->addValidation('descrição', new TRequiredValidator);
        if($fieldSigla) 
        {
            $sigla->setSize('100%');
            $sigla->setMaxLength($ftam);
            if($fUpper) 
                $sigla->forceUpperCase();
        }

        if($fieldSigla) 
        {
            $row = parent::addFields(
                    [$lb0=new TLabel('ID'),$id],
                    [new TLabel('Descrição'), $descricao],
                    [new TLabel('Sigla'), $sigla]);
            $lb0->setSize('100%');
            $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-4'];
        }
        else
        {
            $row = parent::addFields(
                    [$lb0=new TLabel('ID'),$id],
                    [new TLabel('Descrição'), $descricao]);
            $lb0->setSize('100%');
            $row->layout = ['col-sm-2', 'col-sm-10'];
        }
    }
}
