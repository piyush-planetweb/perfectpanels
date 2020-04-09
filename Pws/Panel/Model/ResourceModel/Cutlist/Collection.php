<?php

namespace Pws\Panel\Model\ResourceModel\Cutlist;

use \Pws\Panel\Model\ResourceModel\AbstractCollection;
/**
 * Panel collection
 */
class Collection extends AbstractCollection
{

	/**
     * @var string
     */
	protected $_idFieldName = 'cutline_id';

	/**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_init('Pws\Panel\Model\Cutlist', 'Pws\Panel\Model\ResourceModel\Cutlist');
		$this->_map['fields']['cutline_id'] = 'main_table.cutline_id';
	}

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}