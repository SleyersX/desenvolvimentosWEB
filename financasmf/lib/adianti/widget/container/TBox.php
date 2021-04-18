<?php

namespace Adianti\Widget\Container;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TStyle;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Util\TImage;
use Adianti\Control\TAction;

use Exception;

/**
 * Box - The box component
 *
 * @version    5.6
 * @package    widget
 * @subpackage container
 * @author     MarcoARCampos
 * @copyright  Copyright (c) 2018
 * @license    Free
 */
class TBox extends TElement
{
    private $id;
    private $boxStyle;
    private $width;
    private $height;
    private $borderTop;
    private $borderBox;
    
    private $hearderStyle;
    private $title;
    private $titleImg;
    private $titleStyle;
    private $button;
    private $tools;
    private $actions;

    private $body;
    private $bodyStyle;
    private $footer;
    private $ftColor;
    private $loading;   

    
    /**
     * Class Constructor
     */
    public function __construct( $title = '' )
    {
        parent::__construct('div');
        $this->{'class'} = 'talert alert alert-dismissible alert-white';
        $this->{'role'}  = 'alert';
        
        $this->id = 'tbox_'.mt_rand(1000000000, 1999999999);
        
        // creates the box
        $this->boxStyle  = null;
        $this->borderTop = null;
        $this->borderBox = 'border: 1px solid #3C8DBC;';
        $this->width     = 'width:100%;';
        $this->height    = 'height:100%;';
        
        $this->hearderStyle = null;
        $this->title        = $title;
        $this->titleImg     = null;
        $this->titleStyle   = 'font-size: 16px; font-weight: bold;';
        $this->button       = null;
        $this->tools        = null;
        $this->actions      = array();

        $this->body      = array();
        $this->bodyStyle = null;
        $this->footer    = null;
        $this->ftColor   = null;
        $this->loading   = false;       
    }

    
// Div1 Box --------------------------------------------------------------------
//------------------------------------------------------------------------------
    
    /**
     * Returns the element ID
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set the box's size
     * @param $width Panel width
     * @param $height Panel height
     */
    public function setSize( $width, $height )
    {
        if ( $width )
        {
            $this->width = ( strstr( $width, '%' ) !== FALSE ) ? "width:{$width};" : "width:{$width}px;";
        }
        
        if ( $height )
        {
            $this->height = ( strstr( $height, '%' ) !== FALSE ) ? "height:{$height};" : "height:{$height}px;";
        }
    }
    
    
    /**
     * Returns the box size
     * @return array( width, height )
     */
    public function getSize()
    {
        return array( $this->width, $this->height );
    }


    // STYLE's -----------------------------------------------------------------
    

    /**
     * Define style of box
     * @param $style
	 * Ex: 'background-color: green' 
     */
    public function setBoxStyle( $style = null )
    {
        $this->boxStyle = $style;
    }


    /**
     * Define style hearder of box
     * $param $color
     * Obs: bg-color type - create Application.css in your theme
     * Ex: bg-color:  .bg-primary {
     *                    background-color: #3C8DBC !important;
     *                    color: #fff  !important;
     *                }   
     *
     */
    public function setHeaderStyle( $color = null )
    {
        $this->hearderStyle = ( strstr( $color, 'bg' ) ? trim( $color ) : null );
    }


    /**
     * Set the style type of box
     * @param $color
     *
     * Ex: 'orange;'
     */
    public function setBorderTopColor( $color = null )
    {
        $this->borderTop = (( $color ) ? 'border-top: 3px solid '.$color.'!important;' : null );
    }


    /**
     * Define style border of box
     * @param $color
     * Ex: 'orange' or #FFA500
     */
    public function setBorderBoxColor( $color = null )
    {
        $this->borderBox = (( $color ) ? 'border: 1px solid '.$color.'!important;' : null );
    }




// div2 ------------------------------------------------------------------------
//------------------------------------------------------------------------------
        
    /**
     * Set the title of box
     * @param $title
     */
    public function setTitle( $title = '' )
    {
        $this->title = $title;
    }


    /**
     * Set an image that goes in front of the title
     * @param $titleimg
     * Ex: 'app/images/date.png'
     *     'fa:save fa-lg'   
     *     'bs:save red'
     */
    public function setTitleImg( $titleimg = null )
    {
        if ( $titleimg )
        {
            $image = new TElement('span');
            $image->{'style'} = 'padding-right:4px';
            
            if ( substr( $titleimg, 0, 3 ) == 'bs:' )
            {
                $image = new TElement('i');
                $image->{'style'} = 'padding-right:4px';
                $image->{'class'} = 'glyphicon glyphicon-'.substr( $titleimg, 3 );
            }
            else if ( substr( $titleimg, 0, 3 ) == 'fa:' )
            {
                $fa_class = substr( $titleimg, 3 );
                
                if ( strstr( $titleimg, '#' ) !== FALSE )
                {
                    $pieces = explode( '#', $fa_class );
                    $fa_class = $pieces[0];
                    $fa_color = $pieces[1];
                }
                
                $image = new TElement('i');
                $image->{'style'} = 'padding-right:4px';
                $image->{'class'} = 'fa fa-'.$fa_class;
                
                if ( isset( $fa_color ))
                {
                    $image->{'style'} .= "; color: #{$fa_color}";
                }
            }
            else if ( file_exists( 'app/images/'.$titleimg ))
            {
                $image = new TImage( 'app/images/'.$titleimg );
                $image->{'style'} = 'padding-right:4px';
            }
            else if ( file_exists( 'lib/adianti/images/'.$titleimg ))
            {
                $image = new TImage( 'lib/adianti/images/'.$titleimg );
                $image->{'style'} = 'padding-right:4px';
            }
            
            $this->titleImg = $image;
        }
    }
    
    
    /**
     * Set a style for the title
     * @param $titlestyle
     * Ex: 'font-size: 16px; font-weight: bold;'
     */
    public function setTitleStyle( $titlestyle = '' )
    {
        $this->titleStyle = $titlestyle;
    }


    /**
     * Add style for the title
     * @param $titlestyle
     * Ex: 'font-size: 16px; font-weight: bold;'
     */
    public function addTitleStyle( $titlestyle = '' )
    {
        $this->titleStyle = $this->titleStyle.$titlestyle;
    }

    /**
     * Set the button's of box
     * @param $type - ( 'all', 'collapse', 'remove', null )
     */
    public function setButton( $button = null )
    {
        $this->button = $button;
    }
    
    /**
     * Set anything go right side header
     * @param $tools ex: '<span class="label label-primary">Label</span>'
     * Buttons, labels, and many other things can be placed here!
     */
    public function setTools( $tools = null )
    {
        $this->tools = $tools;
    }


// div3 ------------------------------------------------------------------------
//------------------------------------------------------------------------------
    
    /**
     * Reset body
     */
    public function setResetBody()
    {
        return $this->body = array();
    }


    /**
     * Add a Box Body
     * @param $body Any object that implements the show() method
     */
    public function addBody( $body )
    {
        $this->body[] = $body; 
        return $body;
    }


    /**
     * Define style body
     * @param $style
     */
    public function setBodyStyle( $style = null )
    {
        $this->bodyStyle = $style;
    }


// div4 ------------------------------------------------------------------------
//------------------------------------------------------------------------------
    
    /**
     * Set anything go right side header
     * @param $tools ex: '<span class="label label-primary">Label</span>'
     */
    public function addFooter( $footer )
    {
        $this->footer[] = $footer;
        return $footer;
    }     


    /**
     * Set background color the footer
     * @param $color
     */
    public function setFooterColor( $color = null )
    {
        $this->ftColor = (( $color ) ? 'background:'.$color.'!important;' : null );
    }    


    /**
     * Return form actions
     */
    public function getActions()
    {
        return $this->actions;
    }


    /**
     * Return form actions reseted
     */
    public function setResetActions()
    {
        return $this->actions = array();
    }


    /**
     * Add a form Button
     * @param $label  - Button label
     *        $action - JS Button action
     *        $icon   - Button icon
     *        $title  - Button title (Tooltip)
     *        $class  - Button Class
     *        $pull   - 'left' or 'right'
     */
    public function addButton( $label, $action, $icon = 'fa:save', $title = null, $class = 'btn btn-default', $pull = 'left' )    
    {
        // Label
        $label_info = ( $label instanceof TLabel ) ? $label->getValue() : $label;
        $name       = 'btn_'.mt_rand(1000000000, 1999999999);
        $class      = ( $class ) ? $class : 'btn btn-default';
        $pull       = ( $pull ) ? $pull : 'left';    

        $button = new TElement( $name );
        $button->class = $class.' pull-'.$pull;
        $button->style = 'margin-right:4px';

        // Add Icon
        if ( $icon )
        {
            $image = new TImage( $icon );
			$image->{'style'} = 'padding-right:4px';
	
			$button->add( $image );
        }
        
        // Add Tooltip
        if ( $title )
        {
            $button->{'title'} = $title;
        }
		
		// Add Label
        $button->add( $label_info );
       
        // Define the button action
        if ( $action instanceof TAction )
        { 
            $button->{'onclick'} = "__adianti_load_page('{$action->serialize()}');";
        }
        else if ( is_string( $action ))
        {
            $button->{'onclick'} = $action;
        }

        $this->actions[] = $button;
        
        return $button;
    }

    
    // DropDown Button    
    public function addDropDown( $DropDown, $pull = 'left' ) 
    {
        $pull = ( $pull ) ? $pull : 'left';
            
        $DropDown->class .= ' pull-'.$pull;
        
        $this->actions[] = $DropDown;

        return $DropDown;
    }   



// div5 ------------------------------------------------------------------------
//------------------------------------------------------------------------------
    
    /**
     * Loading States
     * @param $loading = false
     * To simulate a loading state, simply place this code before the .box closing tag.
     */
    public function setLoading( $loading = false )
    {
        $this->loading = $loading;
    }




// Show ------------------------------------------------------------------------
//------------------------------------------------------------------------------
    
    /**
     * Show the widget
     */
    public function show()
    {
        // Box -----------------------------------------------------------------
        $div1 = new TElement('div');
        $div1->class = "box ";
        $div1->style = $this->width;
        
        if ( $this->boxStyle ){
            $div1->style .= $this->boxStyle;
        }

        if ( $this->borderBox ){
            $div1->style .= $this->borderBox;
        }

        if ( $this->borderTop ){
            $div1->style .= $this->borderTop;
        }


        // header title --------------------------------------------------------
        $div21 = new TElement('h3');
        $div21->class = "box-title ";
        $div21->style = $this->titleStyle;
        $div21->add( $this->titleImg ); 
        $div21->add( $this->title );      


        //  header tools -------------------------------------------------------
        $div22 = new TElement('div');
        $div22->class = "box-tools pull-right";


        // Add Tools        
        if ( $this->tools ){
            $div22->add( $this->tools );                                
        }


        // Add Buttons
        if ( $this->button ){
            
            // Button collapse
            if (( $this->button == 'all' ) or ( $this->button == 'collapse' ))
            {
                $btn1 = new TElement('button');
                $btn1->class           = "btn btn-box-tool";
                $btn1->{'data-widget'} = "collapse";
                $btn1->{'data-toggle'} = "tooltip";
                $btn1->title           = "Recolhe/Expande";
                $btn1->add('<i class = "fa fa-minus"></i>');
                
                $div22->add( $btn1 );
            }    
            
            // Button remove
            if (( $this->button == 'all' ) or ( $this->button == 'remove' ))
            {
                /*
                $btn2 = new TElement('button');
                $btn2->class           = "btn btn-box-tool";
                $btn2->{'data-widget'} = "remove";
                $btn2->{'data-toggle'} = "tooltip";
                $btn2->title           = "Recolhe/Expande";
                $btn2->add('<i class = "fa fa-times"></i>');
                $div22->add( $btn2 );
                */

                $btn2 = new TElement('button');
                //$btn2->{'type'}  = 'button';
                //$btn2->{'class'} = 'remove';                
                $btn2->{'data-dismiss'} = 'alert';
                $btn2->{'data-widget'}  = "remove";
                $btn2->{'aria-label'}   = 'Close';
                
                $span = new TElement('span');
                $span->{'aria-hidden'} = 'true';
                $span->add('&times;');
                $btn2->add($span);

                $div22->add( $btn2 );
            }    
        }


        // Add header ----------------------------------------------------------
        $div2 = new TElement('div');
        $div2->class = "box-header with-border ".$this->hearderStyle;
        $div2->add( $div21 );
        $div2->add( $div22 );
        
        
        // Add header in Box ---------------------------------------------------
        $div1->add( $div2 ); 


        // body ----------------------------------------------------------------
        if ( $this->body )
        {
            $div3 = new TElement('div');
            $div3->class = "box-body";
            $div3->style = $this->height;
            
            if ( $this->bodyStyle ){
                $div3->style .= $this->bodyStyle; 
            }

            foreach ( $this->body as $body )
            {
                $div3->add( $body );
            }
            
            // Add body in Box -------------------------------------------------
            $div1->add( $div3 ); 
        }


        // Actions -------------------------------------------------------------
        if ( $this->actions )
        {
            foreach ( $this->actions as $action_button )
            {
                $this->footer[] = $action_button;
            }
        }    


        // footer --------------------------------------------------------------
        if ( $this->footer )
        {
            $div4 = new TElement('div');
            $div4->class = "box-footer";
            
            // background-color footer
            if ( $this->ftColor ){
                $div4->style .= $this->ftColor; 
            }

            // footer[]
            foreach ( $this->footer as $footer )
            {
                $div4->add( $footer );
            }

            // Add footer in Box -----------------------------------------------
            $div1->add( $div4 ); 
        }


        // loading -------------------------------------------------------------
        if ( $this->loading ) 
        {
            $div5 = new TElement('div');
            $div5->class = 'overlay';
            $div5->add('<i class = "fa fa-refresh fa-spin"></i>' );
    
            // Add loading in Box ----------------------------------------------
            $div1->add( $div5 );
        }    
        
        parent::add( $div1 );
        parent::show();
    }
}
