<?php
namespace Pws\Panel\Controller\Cutting;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Edit extends \Pws\Panel\Controller\Index
{
                
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}