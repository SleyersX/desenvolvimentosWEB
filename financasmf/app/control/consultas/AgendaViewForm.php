<?php
/**
 * AgendaViewForm
 */
class AgendaViewForm extends TPage
{
    private $fc;
    
    public function __construct()
    {
        parent::__construct();
        $this->fc = new TFullCalendar(date('Y-m-d'), 'agendaWeek');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setTimeRange('05:00', '23:59');
        $this->fc->setDayClickAction(new TAction(array('AgendaEventForm',    'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('AgendaEventForm',  'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('AgendaEventForm', 'onUpdateEvent')));
        //$this->fc->disableDragging();
        //$this->fc->disableResizing();
        parent::add( $this->fc );
    }
    
    /**
     * Output events as an json
     */
    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open(_DATABASE_);
            
            $events = SysAgenda::where('start_time', '>=', $param['start'])
                                   ->where('end_time',   '<=', $param['end'])
                                   ->where('user_id',   '=', TSession::getValue('userid'))
                                   ->load();
            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['start_time']);
                    $event_array['end']   = str_replace( ' ', 'T', $event_array['end_time']);
                    
                    $popover_content = $event->render("{description}");
                    $popover_title = $event->render("{title}");
                    $event_array['title'] = TFullCalendar::renderPopover($event_array['title'], $popover_title, $popover_content);
                    
                    $return[] = $event_array;
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        //var_dump($param);

        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            //$param['date'] = TDate::date2us($param['date']);
            $this->fc->setCurrentDate($param['date']);
        }
    }
}
