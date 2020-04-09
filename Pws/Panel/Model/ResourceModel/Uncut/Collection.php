<?php

namespace Pws\Panel\Model\ResourceModel\Uncut;

use \Pws\Panel\Model\ResourceModel\AbstractCollection;
/**
 * Panel collection
 */
class Collection extends AbstractCollection
{

	/**
     * @var string
     */
	protected $_idFieldName = 'uncut_id';

	/**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_init('Pws\Panel\Model\Uncut', 'Pws\Panel\Model\ResourceModel\Uncut');
		$this->_map['fields']['uncut_id'] = 'main_table.uncut_id';
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