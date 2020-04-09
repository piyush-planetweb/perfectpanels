<?php

namespace Pws\Panel\Model\Source;

class Panelgrouplist implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Pws\Panel\Model\Group
     */
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
        $groups = $this->_group->getCollection()
        ->addFieldToFilter('status', '1');
        $groupList = array();
        foreach ($groups as $group) {
            $groupList[] = array('label' => $group->getName(),
                'value' => $group->getId());
        }
        return $groupList;
    }
}
