<?php

class Magestore_Magenotification_Adminhtml_FeedbackController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('system/magestore_extension')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Extension Feedbacks'), Mage::helper('adminhtml')->__('Extension Feedbacks'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->getResponse()->setRedirect('http://support.magestore.com/');
		//$this->_initAction()
		//	->renderLayout();
	}
	
	public function resendAction()
	{
		$id = $this->getRequest()->getParam('id');
		$feedback = Mage::getModel('magenotification/feedback')->load($id);		
		if($feedback->getId()){
			if($feedback->getIsSent() == '1'){
				Mage::getSingleton('core/session')->addNotice(Mage::helper('magenotification')->__('This feedback is already sent to Magestore.com'));
				return $this->_redirect('*/*/edit', array('id' => $feedback->getId()));
			}
			$feedback->setMessage($feedback->getLatestMessage());
			try{
				Mage::helper('magenotification/feedback')->postFeedback($feedback);
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('magenotification')->__('This feedback had been sent to Magestore.com'));
				$feedback->setIsSent(1)
							->save();
				return $this->_redirect('*/*/edit', array('id' => $feedback->getId()));
			} catch(Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				return $this->_redirect('*/*/edit', array('id' => $feedback->getId()));
			}
		}
		
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magenotification')->__('Item does not exist'));
		$this->_redirect('*/*');
	}
	
	public function resendmessageAction()
	{
		$id = $this->getRequest()->getParam('id');
		$feedback_id = $this->getRequest()->getParam('feedback_id');
		$message = Mage::getModel('magenotification/feedbackmessage')->load($id);		
		if($message->getId()){
			if((int)$message->getIsSent() == 1){
				Mage::getSingleton('core/session')->addNotice(Mage::helper('magenotification')->__('This message is already sent'));
				return $this->_redirect('*/*/edit', array('id' => $feedback_id));
			}
			try{
				Mage::helper('magenotification/feedback')->postMessage($message);
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('magenotification')->__('This message had been sent'));
				$message->setIsSent(1)
							->save();
				return $this->_redirect('*/*/edit', array('id' => $feedback_id));
			} catch(Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				return $this->_redirect('*/*/edit', array('id' => $feedback_id));
			}
		}
		
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magenotification')->__('Item does not exist'));
		$this->_redirect('*/*');
	}	

	public function editAction(){
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('magenotification/feedback')->load($id);
		if(Mage::helper('magenotification/feedback')->needUpdate($model)){
			Mage::helper('magenotification/feedback')->updateFeedback($model);
		}

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('feedback_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('system/magestore_extension');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feedbacks Manager'), Mage::helper('adminhtml')->__('Feedbacks Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('magenotification/adminhtml_feedback_edit'))
				->_addLeft($this->getLayout()->createBlock('magenotification/adminhtml_feedback_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magenotification')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction(){
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('magenotification/feedback');		
			$model->load($this->getRequest()->getParam('id'));
			$model->addData($data);
			$helper = Mage::helper('magenotification');
				//upload files
			$attachedfiles = array();
			if(count($_FILES)){
				$path = Mage::getBaseDir('media').DS.'feedback';
				foreach($_FILES as $fileId=>$file){
					if(!$file['name'])
						continue;
					$uploader = new Varien_File_Uploader($fileId);
					$uploader->setAllowRenameFiles(false);			
					$uploader->setFilesDispersion(true);	
					$uploader->save($path,$file['name']);
					$attachedfiles[] = $uploader->getDispretionPath($file['name']).DS.$file['name'];				
				}
			}
			if(count($attachedfiles)){
				$attachedfiles = implode(',',$attachedfiles);
				$attachedfiles = str_replace(DS,'/',$attachedfiles);
				$data['file'] = $attachedfiles;
			}
				//save message
			$message = Mage::getModel('magenotification/feedbackmessage');
			if($model->getId() && isset($data['message']) && $data['message']){
				$message->setData($data)
						->setFeedbackId($model->getId())
						->setFeedbackCode($model->getCode())
						->setPostedTime(now())
						->setIsCustomer(1)
						->setIsSent(2)
						->setUser(Mage::getSingleton('admin/session')->getUser()->getUsername())
						;
				unset($data['file']);
			}
				
			$model->addData($data);
			$model->setExtensionVersion($helper->getExtensionVersion($model->getExtension()));
			if(!$model->getId()){
				$code = strtoupper($helper->getDomain(Mage::getBaseUrl())).time();
				$code = str_replace('WWW.','',$code);
				$model->setCode($code);
			}
			if ($model->getCreated() == NULL || $model->getUpdated() == NULL) {
				$model->setCreated(now());
			} 
				// post feedback
			if(!$model->getId() || ($model->getId() && $model->getMessage())){
				try{	
					Mage::helper('magenotification/feedback')->postFeedback($model);
					$model->setIsSent(1);
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$model->setIsSent(2);
				}
			}
				// post message
			if($message->getData()){
				try{	
					Mage::helper('magenotification/feedback')->postMessage($message);
					$message->setIsSent(1);
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$message->setIsSent(2);
				}				
			}
			try{	
				$model->save();
					//save message
				if($message->getData()){
					$message->save();
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magenotification')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magenotification')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('magenotification/feedback');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $feedbackIds = $this->getRequest()->getParam('feedback');
        if(!is_array($feedbackIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($feedbackIds as $feedbackId) {
                    $feedback = Mage::getModel('magenotification/feedback')->load($feedbackId);
                    $feedback->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($feedbackIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $feedbackIds = $this->getRequest()->getParam('feedback');
        if(!is_array($feedbackIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($feedbackIds as $feedbackId) {
                    $feedback = Mage::getSingleton('magenotification/feedback')
                        ->load($feedbackId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($feedbackIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'feedbacks.csv';
        $content    = $this->getLayout()->createBlock('magenotification/adminhtml_feedback_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'feedbacks.xml';
        $content    = $this->getLayout()->createBlock('magenotification/adminhtml_feedback_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}