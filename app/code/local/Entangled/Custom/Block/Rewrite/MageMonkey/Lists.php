<?php

class Entangled_Custom_Block_Rewrite_MageMonkey_Lists extends Ebizmarts_MageMonkey_Block_Customer_Account_Lists {

    /**
     * Get default list data from MC
     *
     * @return array
     */
    public function getGeneralList()
    {
        $list = $this->helper('monkey')->config('list');

        if ($list) {
            if (empty($this->_generalList)) {

                $api = $this->getApi();
                $listData = $api->lists(array('list_id' => $list));

                if (empty($this->_myLists)) {
                    $this->_myLists = $api->listsForEmail($this->_getEmail());
                }

                if ($listData['total'] > 0) {
                    $showRealName = $this->helper('monkey')->config('showreallistname');
                    if ($showRealName) {
                        $listName = $listData['data'][0]['name'];
                    } else {
                        $listName = $this->__('Entangled New Releases');
                    }
                    $ig = $api->listInterestGroupings($listData['data'][0]['id']);
                    $this->_generalList = array(
                        'id' => $listData['data'][0]['id'],
                        'name' => $listName,
                        'interest_groupings' => $this->helper('monkey')->filterShowGroupings($ig),
                    );
                }
            }
        }

        return $this->_generalList;
    }

    public function getAuthorsList(){
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        $authorIds = $customer->getAuthorIds();
        $authorIds = explode(")(",substr($authorIds,1,-1));
        $authorAttribute = Mage::getModel("eav/config")->getAttribute("catalog_product", "publish_author");
        $authors = $authorAttribute->getSource()->getAllOptions(true, true);
        $names = array();
        $authorList = array();

        foreach($authors as $author){
            $names[$author["value"]] = $author["label"];
        }
        foreach($authorIds as $authorId){
            if($authorId){
                $authorList[$authorId] = $names[$authorId];
            }
        }

        return $authorList;
    }

}