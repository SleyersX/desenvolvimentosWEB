<?php
/**
 * DefaultForm Registration
 * @author  <your name here>
 */
class DefaultForm extends TWindow
{
    private $form;
    
    function __construct()
    {
        parent::__construct();
        parent::setTitle(TSession::getValue('TITULO'));
        parent::setCloseAction(new TAction([$this,'onClose']));
        parent::setProperty('class', 'window_modal');

        $sfom = TSession::getValue('FORMULARIO');
        $this->form = new $sfom(TSession::getValue('SIGLA'));
        parent::setSize(TSession::getValue('setWidthWCadAux'), TSession::getValue('setHeigthWCadAux'));
        //parent::setSize(0.5, 245);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'),  new TAction([$this, 'onClear']), 'fa:eraser red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);        
        parent::add($container);
    }

    public function onClear() {
        $this->form->clear();
    }

    public static function onClose() {
        AdiantiCoreApplication::loadPage(TSession::getValue('RETORNO'), 'onReload');
    }

    public function onEdit($param) 
    {
        try
        {
            TTransaction::open(_DATABASE_);      
            $obj = TSession::getValue('TABELA');
            $object = new $obj($param['id']);
            $this->form->setData($object);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            $this->onClear();
        }
    }

    public function onSave($param)
    {
        try
        {
            TTransaction::open(_DATABASE_);            
            $data = $this->form->getData();
            $this->form->validate();

            $obj = TSession::getValue('TABELA');
            $object = new $obj;
            $object->fromArray((array) $data);
            $object->store();
            TTransaction::close();
            
            //$action = new TAction([$this, 'onClose']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            $this->onClear();
        }
        catch (Exception $e)
        {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
