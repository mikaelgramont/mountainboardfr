<?php
class DataController extends Lib_Controller_Action
{
    protected $_aclActionRules = array(
        'editcomment' => array('resource' => Lib_Acl::REGISTERED_RESOURCE ),
        'fileBrowser' => array('resource' => Lib_Acl::EDITOR_RESOURCE)
    );

    /**
     * List of fields in a form that will never match
     * a field in the data DB table.
     * Example: 'submit'
     *
     * @var array
     */
    protected $_disregardUpdates = array(
        'tags',
        'submit',
        'skipAutoFields',
        'longitude',
        'latitude',
        'zoom',
        'yaw',
        'pitch',
        'mapType',
        'token',
    	'locationFlag',
    );

    /**
     * Initialisations
     */
    public function init()
    {
        parent::init();
        Zend_Registry::set('Category', Category::COMMUNITY);
    }

    /**************************************************************************
     * DATA ITEM CRUD ACTIONS
     *************************************************************************/
    /**
     * List of data elements
     *
     */
    public function listAction()
    {
        $this->_useAdditionalContent = true;
    	
    	$dataType = $this->_request->getParam('dataType');
        if($dataType == 'data'){
        	// Abstract route
        	throw new Lib_Exception("dataType 'data' not allowed for listAction");
        }
        $page = $this->_getParam('page', 1);
        $result = Data_Utils::getList($this->_user, $this->_acl, $dataType, $page);
        $items = $this->_paginateData($result['select'], $page, $result['itemsPerPage']);

        if(count($items)){
        	$item = $items->getIterator()->current();
        } else {
        	$table = ucfirst($dataType);
        	$table = new $table();
        	$item = $table->fetchNew();
        }
        Zend_Registry::set('Category', $item->getCategory());
        Zend_Registry::set('SubCategory', $item->getSubCategory());

        $this->_helper->layout->setLayout($item->getLayout(Data::ACTION_LIST));

        $this->view->items = $items;
        $this->view->dataType = $dataType;
        $this->view->separateFirstContentCardHeader = true;
    }

    /**
     * Displays a data
     *
     */
    public function displayAction()
    {
        $dataType = $this->_request->getParam('dataType');
        if($dataType == 'data'){
        	// Abstract route
        	throw new Lib_Exception("dataType 'data' not allowed for displayAction");
        }
    	$dataId = $this->_request->getParam(2);
    	if($dataId === null){
    		$dataId = $this->_request->getParam('id');
    	}
        $data = Data::factory($dataId, $dataType);

        if(empty($data)){
            throw new Lib_Exception_NotFound("Data not found");
        }

        Zend_Registry::set('Category', $data->getCategory());
        Zend_Registry::set('SubCategory', $data->getSubCategory());

        if((!$data->isReadableBy($this->_user, $this->_acl))){
            $this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
        }

        $layout = $data->getLayout(Data::ACTION_DISPLAY);
        $this->_helper->layout->setLayout($layout);
		
        $additionalData = $this->_getAdditionalDataForDisplay($data, $dataType);
        $data->viewBy($this->_user, $this->getRequest());

        $this->view->data = $data;
        $this->view->additionalData = $additionalData;

        
        $reflection = new ReflectionClass ($data);
        if($reflection->implementsInterface('Data_Row_LocationInterface')){
            $this->view->hasMap = $data->hasLocation();
        } else {
            $this->view->hasMap = false;
        }

        if($reflection->implementsInterface('Data_Row_AlbumInterface')){
            $this->view->album = $data->getAlbum();
        } else {
            $this->view->album = null;
        }
        
        $this->view->richTextContent = false;

        if($identity = Zend_Auth::getInstance()->getIdentity()){
        	$this->view->lastLogin = $identity->lastLogin;
        } else {
        	$this->view->lastLogin = null;
        }
        
        $this->render($data->getDisplayView());
    }

    /**
     * Edit or create a data
     */
    public function editAction()
    {
    	$dataType = $this->view->dataType = $this->_request->getParam('dataType');
        if($dataType == 'data'){
        	// Abstract route
        	throw new Lib_Exception("dataType 'data' not allowed for editAction");
        }
    	$dataId = $this->_request->getParam(2);

        $dataRow = $this->view->data = Data::factory($dataId, $dataType);
        $this->_helper->layout->setLayout($dataRow->getLayout(Data::ACTION_EDIT));

        $editType = $dataRow->getEditType();

        if($editType == Data::EDITTYPE_EDIT){
        	Zend_Registry::set('Category', $dataRow->getCategory());
        	Zend_Registry::set('SubCategory', $dataRow->getSubCategory());

        	if((!$dataRow->isEditableBy($this->_user, $this->_acl))){
                $this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
            }
        } else {
        	Zend_Registry::set('Category', $dataRow->getCategory('creation'));
        	Zend_Registry::set('SubCategory', $dataRow->getSubCategory('creation'));

        	if((!$dataRow->isCreatableBy($this->_user, $this->_acl))){
                $this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
            }
        }

        $this->view->editStatus = $editStatus = $this->_request->getParam('editStatus', null);
        if($editStatus){
            // A status was given, this means an update was attempted before, we just want
            // to display a feedback message
            return;
        }

        $postData = $this->_request->getPost();
        $dbData = $dataRow->toArray();
        $reflection = new ReflectionClass($dataRow);
        if($reflection->implementsInterface('Data_Row_LocationInterface') && $dataRow->hasLocation()){
       		$locationData = $this->_helper->populateLocationFormElements($dataRow, $postData);
           	$dbData = array_merge($dbData, $locationData);
        }

        $form = $dataRow->getForm($this->_user, $this->_acl);
        $form->populateFromDatabaseData($dbData,false);

        if(empty($postData)){
            // In case of a new data, we may want to preset dpt
            if($editType != Data::EDITTYPE_EDIT && ($dptId = $this->_request->getQuery('dpt', null))){
                $this->_presetBounds($form, $dptId);
            }

            // Display empty form
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($postData)) {
            // Display form with errors
            $this->view->form = $form;
            return;
        }

        try{
            $data = $form->getFormattedValuesForDatabase();
			if(empty($data['status'])){
				$data['status']= Data::VALID;
			}
            
        	$this->_helper->dataSaver()->save($dataRow, $form, $data, $this->_user, $this->_acl, $this->_disregardUpdates);
            $status = Constants::SUCCESS;
        } catch (Exception $e) {
            $msg = "Failed to save data: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();
        	Globals::getLogger()->error($msg, Zend_Log::ERR);
            $status = Constants::FAILURE;
        }

        /**
         * We may want to redirect to this same page, in the
         * case where we just submitted a document's first subform
         */
        if(!$dataRow->onePassSubmit() && $editType == Data::EDITTYPE_FIRST_SAVE){
        	$url = $dataRow->getEditLink();
            $this->_helper->redirector->gotoUrlAndExit($url);
        }

        /**
         * If the current Data_Row is a metadata, we want to redirect to
         * its parent Data_Row's page
         */
        if($reflection->implementsInterface('Data_Row_MetaDataInterface')){
            $parentItem = $dataRow->getParentItemFromDatabase();
            $params = array(
                'dataType' => $parentItem->getItemType(),
                'name' => $parentItem->getCleanTitle(),
                'id' => $parentItem->id,
            );
            $this->_helper->redirectToRoute('displaydata', $params);
        } else {
            $params = array(
                'dataType' => $dataRow->getItemType(),
                'name' => $dataRow->getCleanTitle(),
                'id' => $dataRow->id,
                'status' => $status,
            );
            $this->_helper->redirectToRoute('editdatadone', $params);
        }
    }

    /**
     * Redirection page after a successful or a failed
     * data submission or update
     */
    public function editdoneAction()
    {
    	$this->view->dataType = $dataType = $this->_request->getParam(1);
        $this->view->dataId = $dataId = $this->_request->getParam(3);
        $this->view->status = $this->_request->getParam(4, Constants::ERROR);

        if($dataId && $dataType){
            $data = Data::factory($dataId, $dataType);
        }

        if(!empty($data)){
            $this->view->data = $data;
        	Zend_Registry::set('Category', $data->getCategory());
    		Zend_Registry::set('SubCategory', $data->getSubCategory());
        } else {
            $this->view->data = null;
        }
    }

    /**
     * Delete page
     */
    public function deleteAction()
    {
        $dataType = $this->_request->getParam('dataType');
        if($dataType == 'data'){
        	// Abstract route
        	throw new Lib_Exception("dataType 'data' not allowed for deleteAction");
        }
    	$dataId = $this->_request->getParam(2);
    	if(empty($dataId)){
    		$dataId = $this->_request->getParam(1);
    		if(empty($dataId)){
    			throw new Lib_Exception("No id found for deleteAction");
    		}
    	}

        $dataRow = $this->view->data = Data::factory($dataId, $dataType);

		if((!$dataRow->isDeletableBy($this->_user, $this->_acl))){
        	$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
		}

		$this->_helper->layout->setLayout($dataRow->getLayout(Data::ACTION_DELETE));

		$form = new Data_Form_Delete($dataRow);
        $postData = $this->_request->getPost();
        if(empty($postData) || !$form->isValid($postData)){
			// Display empty form or form with errors
        	$this->view->form = $form;
        	$this->view->data = $dataRow;
        	return;
        }

        try{
        	$dataRow->deleteAndRedirect(array(), $this->_user);
        } catch(Exception $e){
        	$message = "An error occured while trying to delete item '$dataId' of type '$dataType': " . $e->getMessage().PHP_EOL.$e->getTraceAsString();
        	Globals::getLogger()->deletes($message, Zend_Log::ERR);
            $this->_helper->redirectToRoute('othererror', array('error'=>'deleteError'), true);
        }
    }

    /**
     * Delete confirmation message
     */
    public function deletedoneAction()
    {
    	$redirectUrl = $this->_request->getParam(1);
    	if(empty($redirectUrl)){
    		$redirectUrl = Globals::getRouter()->assemble(array(), 'defaults', true);
    	}
    	$this->view->url = $redirectUrl;
    }

    /**
     * Validates/invalidates an item
     *
     */
    public function validateAction()
    {
		$dataType = $this->_request->getParam(1);
		$dataId = $this->_request->getParam(2);
		$targetStatus = $this->_request->getParam('targetStatus');

		$dataRow = $this->view->data = Data::factory($dataId, $dataType);
        if((!$dataRow->isEditableBy($this->_user, $this->_acl))){
               $this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
        }

        if(!in_array($targetStatus, array(Data::VALID, Data::INVALID))){
        	$targetStatus = Data::INVALID;
        }

        $dataRow->status = $targetStatus;
        $dataRow->save(false); // Do not update dates

        if(isset($_SERVER['HTTP_REFERER'])){
			$this->_helper->redirector->gotoUrlAndExit($_SERVER['HTTP_REFERER']);
        }
        $this->_helper->redirectToRoute('defaults', $params);
    }

    /**************************************************************************
     * UPLOADIFY
     *************************************************************************/
    public function uploadAction()
    {
    	$filename = 'no filename found when error occured';
		$post = $this->_request->getPost();

    	if(DEBUG){
    		Globals::getLogger()->upload("-------------------------------------------------", Zend_Log::INFO);
    		Globals::getLogger()->upload(Lib_Debug::dump($_FILES, 'files', false), Zend_Log::INFO);
    		Globals::getLogger()->upload(Lib_Debug::dump($post, 'post', false), Zend_Log::INFO);
    		Globals::getLogger()->upload(Lib_Debug::dump($_GET, 'get', false), Zend_Log::INFO);
    	}

		try{
	    	$operation = $this->_request->getParam('operation');
	    	$type = $this->_request->getParam('type');

        	$sessionUploadsNamespace = new Zend_Session_Namespace(UPLOADS_NAMESPACE);
        	if(!isset($sessionUploadsNamespace->folder)){
        		Globals::getLogger()->error("Trying to upload data, but no folder was found in session namespace", Zend_Log::INFO);
        		die(-1);
        	}

			$folderPath = $sessionUploadsNamespace->folder;
    		$folder = new Folder($folderPath);
    		if(!$folder->isWritableBy($this->_user)){
    			$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
    		}

	    	$path = $folder->getPath();
	    	Globals::getLogger()->upload("Upload operation: '$operation', type: '$type', folder path: '".$path."'", Zend_Log::INFO);
	    	if(!$folder->isWritableBy($this->_user)){
    			$this->_helper->redirectToRoute('usererror', array('errorCode' => User::RESOURCE_ACCESS_DENIED));
	    	}
    		if($operation == 'upload' && (empty($_FILES['Filedata']['tmp_name']) || empty($_FILES['Filedata']['name']))){
				throw new Lib_Exception("Missing information for file upload");
			}

			// Clear the APC stat cache in order to be sure of the result of file_exists()
			clearstatcache();

			if($operation == 'check'){
				// Check all files for a previous version:
				$fileArray = array();
				foreach ($post as $key => $value) {
					if ($key != 'folder') {
						$exists = 'false';
						if (file_exists($path . '/' . $value)) {
							$fileArray[$key] = $value;
							$exists = 'true';
						}
						Globals::getLogger()->upload("Upload - Checking for previous versions of '" . $path . '/' . $value."': ". $exists, Zend_Log::INFO);
					}
				}
				die(json_encode($fileArray));
			}

			$filename = strtolower(str_replace(' ', '', ($_FILES['Filedata']['name'])));
			$extensionWhitelist = Globals::getFileExtensionUploadWhiteList();
			$info = pathinfo($filename);
			$extension = isset($info['extension']) ? $info['extension'] : '';
			if($operation == 'upload' & !in_array($extension, $extensionWhitelist)){
				throw new Lib_Exception("Extension not allowed for upload: '$extension'");
			}
			$destination = $path . '/'. $filename;
			$tmpFile = new File_Uploaded($_FILES['Filedata']['tmp_name']);
			$file = $tmpFile->moveTo($destination);
			Globals::getLogger()->upload("'$filename' successfully uploaded", Zend_Log::INFO);
			die($file->getFullPath());

		} catch(Lib_Exception $e) {
			Globals::getLogger()->error("Upload error '$filename' - ".$e->getMessage(), Zend_Log::ERR);
			$this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
			$this->getResponse()->sendResponse();
			die();
		}
    }

    /**************************************************************************
     * FILE BROWSER
     *************************************************************************/
    public function fileBrowserAction()
    {
        $sessionUploadsNamespace = new Zend_Session_Namespace(UPLOADS_NAMESPACE);

        if(!isset($sessionUploadsNamespace->folder)){
       		Globals::getLogger()->error("Trying to browse uploads, but no folder was found in session namespace", Zend_Log::INFO);
       		die(-1);
        }

    	$type = $this->_request->getParam(1);
    	$path = $sessionUploadsNamespace->folder; //$this->_request->getParam(2);

    	if($this->_user->getRoleId() == User::STATUS_ADMIN){
    		$root = APP_ROOT;
    	} else {
    		$root = $this->_user->getUploadFolder();
    	}


    	$fileBrowser = new File_Browser($root, $path);
    	$currentFolder = $fileBrowser->stripAppRootFromCurrentPath();
    	$currentFolder = str_replace(DIRECTORY_SEPARATOR, '/', $currentFolder);
    	if(substr($currentFolder, 0,1) == '/'){
    		$currentFolder = substr($currentFolder, 1);
    	}
    	if(substr($currentFolder, 0,-1) != '/'){
    		$currentFolder = $currentFolder.'/';
    	}
    	$url = Globals::getRouter()->assemble(array(1 => $type, 2 => $currentFolder), 'filebrowser', true, false);
    	if($currentFolder != $path && ('/' . $currentFolder) != $path){
    		/**
    		 * User either attempted to go up in the folder structure, or
    		 * something unexpected happened with the parameters. We must
    		 * recreate the correct, clean url.
    		 */
    		header("Location: $url");
    		exit();
    	}

    	$this->view->url = $url;
    	$this->view->files = $fileBrowser->getFilesInCurrentPath($type);
    	$this->view->currentFolder = $currentFolder;
    	$this->view->type = $type;
    	$this->_helper->layout->setLayout('file-browser');
    }

	/**************************************************************************
     * COMMENTS
     *************************************************************************/
    public function editcommentAction()
    {
        $param1 = $this->_request->getParam(1);
        $param2 = $this->_request->getParam(2);
        $postData = $this->_request->getPost();

        $commentTable = new Comment();

        if(is_numeric($param1)){
            // Comment editing

            $commentRow = $commentTable->find($param1)->current();
            if(empty($commentRow)){
                throw new Lib_Exception_NotFound("Comment $param1 could not be found");
            }
            if((!$commentRow->isEditableBy($this->_user, $this->_acl))){
                $redirector = new Lib_Controller_Helper_RedirectToRoute();
                $redirector->direct('usererror', array('errorCode'=>User::RESOURCE_ACCESS_DENIED), true);
            }

            $dataRow = $commentRow->getParentItemfromDatabase();

            // End comment editing
        } else {
            // New comment
            $dataType = $param1;
            $dataId = $param2;

            $dataRow = Data::factory($dataId, $dataType);
            Zend_Registry::set('Category', $dataRow->getCategory());

            $commentRow = $commentTable->fetchNew();
            $commentRow->parentItem = $dataRow;

            if((!$commentRow->isCreatableBy($this->_user, $this->_acl))){
                $redirector = new Lib_Controller_Helper_RedirectToRoute();
                $redirector->direct('usererror', array('errorCode'=>User::RESOURCE_ACCESS_DENIED), true);
            }

            // End new comment
        }

        $dbData = $commentRow->toArray();
        $commentForm = $commentRow->getForm($this->_user, $this->_acl);
        $commentForm->populateFromDatabaseData($dbData);

        if (empty($postData)) {
            // Display edit form
            $this->view->commentForm = $commentForm;
            return;
        }

        // Overwrite parentId and parentType
        $postData['parentId'] = $dataRow->id;
        $postData['parentType'] = $dataRow->getItemType();
        if (!$commentForm->isValid($postData)) {
            // Display form with errors
            $this->view->commentForm = $commentForm;
            return;
        }

        // Saving comment
        try{
            $commentData = $commentForm->getFormattedValuesForDatabase();
        	$this->_helper->dataSaver()->save($commentRow, $commentForm, $commentData, $this->_user, $this->_acl, $this->_disregardUpdates);
        } catch (Exception $e) {
            Globals::getLogger()->error("Failed to save comment for dataType '$dataType', id '$dataId': ".$e->getMessage(), Zend_Log::ERR);
            $redirector = new Lib_Controller_Helper_RedirectToRoute();
            $redirector->direct('othererror', array('error'=>'commentSavingError'), true);
        }

        $this->_response->setRedirect($dataRow->getLink())
                        ->sendResponse();
        exit();
    }

    /**************************************************************************
     * PROTECTED METHODS
     *************************************************************************/

    /**
     * Returns other information required to display $data correctly
     *
     * @param Data_Row $data
     * @return array
     */
    protected function _getAdditionalDataForDisplay(Data_Row $data, $dataType)
    {
    	$additionalData = array();
        $dataType = Data::mapDataType($dataType);
		
        
        switch(strtolower($dataType)){
            case Dpt::ITEM_TYPE:
                $additionalData['items'] = Item::getItemsInBounds($data->getBounds());
            	if($data->id <= Dpt::LAST_FRENCH_DPT_ID){
                	$additionalData['spots'] = Spot::getFrenchDptSpotsWithoutLocation($data->id, $additionalData['items']);
            	} else {
            		$additionalData['spots'] = array();
            	}
                $addComments = false;
                $this->_useAdditionalContent = false;
                break;
            case Country::ITEM_TYPE:
            	$additionalData['items'] = Item::getItemsInBounds($data->getBounds());
            	if($data->id == Country::FRANCE_ID){
            		$additionalData['spots'] = Spot::getFrenchSpotsWithoutLocation($additionalData['items']);
            	} else {
                	$additionalData['spots'] = array();
            	}
            	$addComments = false;
            	$this->_useAdditionalContent = false;
                break;
			default:
    			$addComments = true;
    			$this->_useAdditionalContent = true;
            	break;
        }
        
        if($addComments){
        	$comments = $data->getComments($this->_user, $this->_acl);
			$additionalData['comments'] = $comments;
		}
        
        
        return $additionalData;
    }

    /**
     * Takes care of pagination
     *
     * @param Zend_Db_Select $select
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param int $pageRange
     * @param string $view
     * @return Zend_Paginator
     */
    protected function _paginateData(Zend_Db_Select $select, $currentPage = 1, $itemsPerPage = null, $pageRange = null, $view = null)
    {
        if(empty($view)){
            $view = 'commonviews/pagination.phtml';
        }
        if(empty($itemsPerPage)){
            $itemsPerPage = DEFAULT_ITEMS_PER_PAGE;
        }
        if(empty($pageRange)){
            $pageRange = DEFAULT_PAGERANGE;
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setPageRange(5); // Number of links displayed for browsing
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($itemsPerPage);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial($view);

        return $paginator;
    }

    /**
     * Presets the value of the dpt element if:
     *  - it exists
     *  - the value given in the URL matches an existing dpt
     *
     * @param Data_Row $dataRow
     * @param Lib_Form $form
     */
    protected function _presetBounds(Lib_Form $form, $dptId)
    {
        $dptId = $this->_request->getQuery('dpt', null);
        if(empty($dptId)){
            return;
        }
		$table = new Dpt();
		$result = $table->find($dptId);
		if(empty($result)){
			return;
		} 
		
		$dpt = $result->current();
		
		$form->setBounds($dpt->getBounds());
    }
}