<?php

class SteveB27_GoodReads_AjaxController extends Mage_Core_Controller_Front_Action {

    public function reviewsAction(){
        $page = $this->getRequest()->getParam("page",1);
        $isbn = $this->getRequest()->getParam("isbn");
        $id = $this->getRequest()->getParam("id");
        $goodreads = Mage::helper('goodreads')->isbnBookReviews($isbn);
        Mage::register("current_goodreads_review",$goodreads);
        Mage::register("product",Mage::getModel("catalog/product")->load($id));
        $block = $this->getLayout()->createBlock('goodreads/review_product_view_list')->setData("page",$page)->setData("isbn",$isbn);

        $this->getResponse()->setBody($block->toHtml());
    }

}