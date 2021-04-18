<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Util\TImage;

/**
 * Help Widget
 *
 * @version    5.0
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class THelp extends TImage
{
    public function __construct($help, $source = 'fa:info-circle blue')
    {
        parent::__construct($source);
        $this->title = $help;
    }
}
