<?php

namespace Pws\Panel\Model\Source;

class Carousellayout implements \Magento\Framework\Option\ArrayInterface
{
    protected  $_group;
    
    /**
     * 
     * @param \Pws\Panel\Model\Group $group
     */
    public function __construct(
        \Pws\Panel\Model\Group $group
        ) {
        $this->_group = $group;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {   
        $groupList = array();
        $groupList[] = array(
            'label' => __('Owl Carousel'),
            'value' => 'owl_carousel'
            );
        
        $groupList[] = array(
            'label' => __('Bootstrap Carousel'),
            'value' => 'bootstrap_carousel'
            );
        return $groupList;
    }
}
