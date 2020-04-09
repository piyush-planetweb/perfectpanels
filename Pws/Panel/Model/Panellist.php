<?php

namespace Pws\Panel\Model;

class Panellist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $_panel;
    
    /**
     * 
     * @param \Pws\Panel\Model\Panel $panel
     */
    public function __construct(
        \Pws\Panel\Model\Panel $panel
        ) {
        $this->_panel = $panel;
    }
    
    
    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $panels = $this->_panel->getCollection()
        ->addFieldToFilter('status', '1');
        $listPanel = array();
        foreach ($panels as $panel) {
            $listPanel[] = array('label' => $panel->getName(),
                'value' => $panel->getId());
        }
        // echo 'test';
        // die();
        return $listPanel;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        }
        return $options;
    }
}