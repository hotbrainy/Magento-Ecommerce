<?php

class Idev_OneStepCheckout_Helper_Message extends Mage_GiftMessage_Helper_Message
{

    public function getInline ($type, Varien_Object $entity,
    $dontDisplayContainer = false)
    {

        $html = parent::getInline($type, $entity, $dontDisplayContainer);

        if (! empty($html)) {
            $block = Mage::getSingleton('core/layout')->createBlock(
            'giftmessage/message_inline')
                ->setId('giftmessage_form_' . $this->_nextId ++)
                ->setDontDisplayContainer($dontDisplayContainer)
                ->setEntity($entity)
                ->setType($type)
                ->setTemplate('onestepcheckout/gift_message.phtml');

            $html = $block->toHtml();
        }

        return $html;
    }
}
