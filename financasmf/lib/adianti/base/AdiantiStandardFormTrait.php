<?php
namespace Adianti\Base;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;
use Adianti\Database\TRecord;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TAlert;
use Exception;
use StdClass;

/**
 * Standard Form Trait
 *
 * @version    5.5
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait AdiantiStandardFormTrait
{
    use AdiantiStandardControlTrait;
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        {
            if (empty($this->database))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (empty($this->activeRecord))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            // open a transaction with database
            TTransaction::open($this->database);
            
            // get the form data
            $object = $this->form->getData($this->activeRecord);

            // validate data
            $this->form->validate();
            
            // stores the object
            $object->store();

            // fill the form with the active record data
            $this->form->setData($object);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            if( isset($this->alertBox) )
                $this->onMsgBox('success', AdiantiCoreTranslator::translate('Record saved'));
            else
                new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));

            return $object;
        }
        catch (Exception $e) // in case of exception
        {
            // get the form data
            $object = $this->form->getData();
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            // shows the exception error message
            if( isset($this->alertBox) )
                $this->onMsgBox('danger', $e->getMessage());
            else
                new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Clear form
     */
    public function onClear($param)
    {
        // true original
        $this->form->clear( );
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     * @param  $param An array containing the GET ($_GET) parameters
     */
    public function onEdit($param)
    {
        try
        {
            if (empty($this->database))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (empty($this->activeRecord))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database
                TTransaction::open($this->database);
                
                $class = $this->activeRecord;
                
                // instantiates object
                $object = new $class($key);
                
                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
                
                return $object;
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    // ALV
    public function onMsgBox($tipo, $msg) 
    {
        $alert = new TAlert($tipo, $msg);
        $alert->id = 'msgBox';
        if($tipo <> 'danger') {
            $time = $tipo == 'danger' ? 5000 : 1500;
            TScript::create( "$('#msgBox').fadeOut(".$time.");");            
        }
        $this->alertBox->add($alert);
        TScript::create("window.scrollTo(0, 0);");
    }
}
