<?php
namespace Pws\Panel\Controller\Panel;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Add extends \Pws\Panel\Controller\Index
{
                
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}