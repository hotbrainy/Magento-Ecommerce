<?php

class Entangled_Custom_Model_System_Config_Source_Authors {

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var SteveB27_Publish_Model_Resource_Author_Collection $authors */
        $authors = Mage::getModel('publish/author')->getCollection();
        $authors->addAttributeToSelect("name")
                ->addAttributeToSort("name");

        $options = array();
        foreach($authors as $author){
            $options[] = array("value"=>$author->getId(),"label"=>$author->getName());
        }

        return $options;
    }
}