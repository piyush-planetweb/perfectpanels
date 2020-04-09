<?php

namespace Pws\Panel\Model\Source;
use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
	/**
	 * @var \Pws\Panel\Model\Panel
	 */
	protected $panelModel;

	/**
     * Constructor
     *
     * @param \Pws\Panel\Model\Panel $panelModel
     */
	public function __construct(\Pws\Panel\Model\Panel $panelModel)
	{
		$this->panelModel = $panelModel;
	}

	/**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->panelModel->getAvailableStatuses();

        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
 
        return $options;
    }
}