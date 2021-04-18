<?php
/**
 * ContatoForm Registration
 * @author  <your name here>
 */
class ContatoForm extends TPage
{
    protected $form;
    protected $categ_list;

    function __construct()
    {
        parent::__construct();        
        $this->form = new ContatoFormView();
        $this->form->setFormTitle('Pessoas & Empresas');
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'),new TAction(['ContatoList','onReload']),'fa:arrow-circle-o-left blue');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'ContatoList'));
        $container->add($this->form);        
        parent::add($container);
    }

    public function onSave()
    {
        try
        {
            TTransaction::open(_DATABASE_);
            $object = $this->form->getData('Pessoa');
            $this->form->validate();
            $object->store();

            $this->form->setData($object);
            TTransaction::close();
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            return $object;
        }
        catch (Exception $e)
        {
            $object = $this->form->getData();
            $this->form->setData($object);
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                TTransaction::open(_DATABASE_);
                $object = new Pessoa($param['key']);
                $this->form->setData($object);
                TTransaction::close();  
                return $object;
            }
            else {
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onNome($param) 
    {
        $obj = new stdClass;
        $obj->nconhecido = $param['nome'];
        TForm::sendData('formContatoFormView', $obj);
    }

}

class ContatoFormView extends BootstrapFormBuilder
{
    public function __construct()
    {
        parent::__construct('formContatoFormView');

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $nconhecido = new TEntry('nconhecido');
        $telefone = new TEntry('telefone');
        $celular = new TEntry('celular');
        $email = new TEntry('email');
        $contato = new TEntry('contato');
        $endereco = new TEntry('endereco');
        $bairro = new TEntry('bairro');
        $cep = new TEntry('cep');
        $cidade = new TEntry('cidade');
        $nrdocs = new TEntry('nrdocs');
        $obs = new TText('obs');

        // set sizes
        $id->setSize('70%');
        $nome->setSize('100%');
        $nconhecido->setSize('100%');
        $telefone->setSize('100%');
        $celular->setSize('100%');
        $email->setSize('100%');
        $contato->setSize('100%');
        $endereco->setSize('100%');
        $bairro->setSize('100%');
        $cep->setSize('100%');
        $cidade->setSize('100%');
        $nrdocs->setSize('100%');
        $obs->setSize('100%',80);

        // custom
        $id->setEditable(FALSE);
        $nome->setMaxLength(50);
        $nome->setExitAction(new TAction(['ContatoForm', 'onNome']));
        $nconhecido->setMaxLength(50);
        $telefone->setMask('(99)9999-9999');
        $contato->setMaxLength(30);
        $celular->setMask('(99)99999-9999');
        $email->setMaxLength(80);
        $nrdocs->setMaxLength(50);
        $cep->setMask('99999-999');
        $endereco->setMaxLength(50);
        $bairro->setMaxLength(30);
        $cidade->setMaxLength(40);

        // validations
        $nome->addValidation('Nome', new TRequiredValidator);
        $email->addValidation('Email', new TEmailValidator);

        // add the fields
        $row = parent::addFields( 
            [ $lb = new TLabel('ID'), $id ],
            [ new TLabel('Nome','red'), $nome ],
            [ new TLabel('Nome conhecido (apelido)'), $nconhecido ],
            [ new TLabel('Telefone'), $telefone ]);
        $lb->setSize('100%');
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-3', 'col-sm-3'];

        $row = parent::addFields( 
            [ new TLabel('Contato (se Empresa)','blue',10,'i'), $contato ],
            [ new TLabel('Celular'), $celular ],
            [ new TLabel('Email'), $email ],
            [ new TLabel('Nº Docs'), $nrdocs ]);
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];

        $row = parent::addFields( 
            [ $lb = new TLabel('Cep'), $cep],
            [ new TLabel('Endereco'), $endereco ],
            [ new TLabel('Bairro'), $bairro ],
            [ new TLabel('Cidade/Estado'), $cidade ] );
        $lb->setSize('100%');
        $row->layout = ['col-sm-2','col-sm-4','col-sm-2','col-sm-4'];
        
        parent::addFields( [new TLabel('Observações','blue')] );
        parent::addFields( [ $obs ] );
    }
}
