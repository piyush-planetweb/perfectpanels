<?php

namespace Pws\Panel\Model\ResourceModel;

class Panel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Store model
	 *
	 * @var \Magento\Store\Model\Store
	 */
	protected $_store = null;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $_date;

	/**
	 * Store manager
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Framework\Stdlib\Datetime
	 */
	protected $dateTime;

	/**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime $dateTime,
		$connectionName = null
		) {
		parent::__construct($context, $connectionName);
		$this->_date = $date;
		$this->_storeManager = $storeManager;
		$this->dateTime = $dateTime;
	}

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
    	$this->_init('pws_panel','panel_id');
    }

    /**
     *  Check whether panel url key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPanelUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Page $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['pws_panel_store' => $this->getTable('pws_panel_store')],
                $this->getMainTable() . '.panel_id = pws_panel_store.panel_id',
                []
                )->where(
                'status = ?',
                1
                )->where(
                'pws_panel_store.store_id IN (?)',
                $storeIds
                )->order(
                'pws_panel_store.store_id ASC'
                )->limit(
                1
                );
            }

            return $select;
        }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
            )->join(
            ['cps' => $this->getTable('pws_panel_store')],
            'cp.panel_id = cps.panel_id',
            []
            )->where(
            'cp.identifier = ?',
            $identifier
            )->where(
            'cps.store_id IN (?)',
            $store
            );

            if (!is_null($isActive)) {
                $select->where('cp.status = ?', $isActive);
            }

            return $select;
        }

    /**
     * Check if panel url key exist for specific store
     * return panel id if panel exists
     *
     * @param string $url_key
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($url_key, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($url_key, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('cp.panel_id')->order('cps.store_id ASC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Process panel data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['panel_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('pws_panel_store'), $condition);

        $condition = ['panel_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('pws_panel_product'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process panel data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $result = $this->checkUrlExits($object); 

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign panel to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('pws_panel_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['panel_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['panel_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }


        if(null !== ($object->getData('products'))){
            $table = $this->getTable('pws_panel_product');
            $where = ['panel_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);

            if($quetionProducts = $object->getData('products')){
                $where = ['panel_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                foreach ($quetionProducts as $k => $_post) {
                    $data[] = [
                    'panel_id' => (int)$object->getId(),
                    'product_id' => $k,
                    'position' => $_post['product_position']
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        return parent::_afterSave($object);
    }

    
    public function deletePanelsByProduct($product_id = 0) {
        if($product_id) {
            $condition = ['product_id = ?' => (int)$product_id];
            $this->getConnection()->delete($this->getTable('pws_panel_product'), $condition);
            return true;
        }
        return false;
    }

    public function getPanelIdByName($panel_name = '') {
        if($panel_name) {
            $panel_id = null;
            $table = $this->getTable('pws_panel');

            $select = $this->getConnection()->select()->from(
            ['cp' => $table]
            )->where(
            'cp.name = ?',
            $panel_name
            )->limit(1);

            $row_panel = $this->getConnection()->fetchAll($select);
            if($row_panel) { // check if have panel record

                $panel_id = isset($row_panel[0]['panel_id'])?(int)$row_panel[0]['panel_id']:null;
            }
            return $panel_id;
        }
        return null;
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        if ($id = $object->getId()) {
                $connection = $this->getConnection();
                $select = $connection->select()
                ->from($this->getTable('pws_panel_product'))
                ->where(
                    'panel_id = '.(int)$id
                    );
                $products = $connection->fetchAll($select);
                $object->setData('products', $products);
            } 

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $panelId
     * @return array
     */
    public function lookupStoreIds($panelId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('pws_panel_store'),
            'store_id'
            )
        ->where(
            'panel_id = ?',
            (int)$panelId
            );
        return $connection->fetchCol($select);
    }

    public function checkUrlExits(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = $object->getStores();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('pws_panel'),
            'panel_id'
            )
        ->where(
            'url_key = ?',
            $object->getUrlKey()
            )
        ->where(
            'panel_id != ?',
            $object->getId()
            );

        $panelIds = $connection->fetchCol($select);
        if(count($panelIds)>0 && is_array($stores)){

            $stores[] = '0';
            $select = $connection->select()->from(
                $this->getTable('pws_panel_store'),
                'panel_id'
                )
            ->where(
                'panel_id IN (?)',
                $panelIds
                )
            ->where(
                'store_id IN (?)',
                $stores
                );
            $result = $connection->fetchCol($select);

        }
        return $this;
    }
}