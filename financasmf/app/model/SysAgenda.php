<?php
/**
 * SysAgenda Active Record
 * @author  <your-name-here>
 */
class SysAgenda extends TRecord
{
    const TABLENAME = 'sys_agenda';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial';
        
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('start_time');
        parent::addAttribute('end_time');
        parent::addAttribute('color');
        parent::addAttribute('title');
        parent::addAttribute('description');
        parent::addAttribute('user_id');
        parent::addAttribute('profile_id');
    }
}
