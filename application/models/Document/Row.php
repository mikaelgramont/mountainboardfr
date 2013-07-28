<?php
abstract class Document_Row extends Data_Row implements Data_Row_DocumentInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'document';

	protected $_onePassSubmit = false;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Document_Form';

    /**
     * Name of the class of form used to submit the title of this object
     * the very first time the object is going to be saved
     *
     * @var string
     */
    protected $_subForm1Class = 'Document_Form_SubForm1';

    /**
     * Name of the class of form used to edit the rest of the attributes
     * of this object before it is activated
     *
     * @var string
     */
    protected $_subForm2Class = 'Document_Form_SubForm2';

    /**
     * Whether or not we should create an album when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createAlbumOnSave = true;

    /**
     * Documents have simple albums
     *
     * @var string
     */
    protected $_albumType = Media_Album::TYPE_SIMPLE;

    /**
     * Whether or not we should create a folder when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createFolderOnSave = true;

    /**
     * Determine whether an author was designated for this article
     *
     * @return boolean
     */
    public function hasAuthor()
    {
        $return = !empty($this->author);
        return $return;
    }

    /**
     * Return the author of current item
     *
     */
    public function getAuthor()
    {
        return $this->findParentRow('User', 'Author');
    }

    /**
     * Return the name of the author of current item
     *
     */
    public function getAuthorName()
    {
        if(empty($this->author)){
            return null;
        }

        $authorInfo = $this->getAuthorNameAndLink();
        return ucfirst($authorInfo['name']);
    }

    /**
     * Return an array of parameters needed to build a link to the author of
     * the current item.
     * If the author is not registered on the website, only their name will be returned.
     *
     * @return array
     */
    public function getAuthorNameAndLink()
    {
        $return = array(
            'name' => null,
            'link' => null,
        );

        if(empty($this->author)){
            return $return;
        }

        if(strpos($this->author, NOREALDATA_MARK) !== false){
            // username is stored directly in DB
            $return['name'] = ucfirst(str_replace(NOREALDATA_MARK, '', $this->author));
            return $return;
        }

        // userId is stored in DB
        $author = $this->findParentRow('User', 'Author');
        if(!empty($author)){
            $return['name'] = ucfirst($author->{User::COLUMN_USERNAME});
            $return['link'] = $author->getLink();
        }
        return $return;
    }

    /**
     * Instantiates the form to edit this document.
     * If the id is empty, we are editing a brand new document.
     * If the id is not empty, but the date is, that means we just submitted
     * the document and it was never activated.
     * If the id is not empty and the date either, then we are editing a
     * document that was activated before.
     *
     * @param User_Row $user
     * @param Lib_Acl $acl
     * @param array $options
     * @return Data_Form
     */
    public function getForm(User_Row $user, Lib_Acl $acl, $options = null)
    {
        $editType = $this->getEditType();
        switch($editType){
        	case Data::EDITTYPE_FIRST_SAVE:
        		$formClass = $this->_subForm1Class;
        		break;
        	case Data::EDITTYPE_NEXT_SAVE:
        		$formClass = $this->_subForm2Class;
        		break;
        	case Data::EDITTYPE_EDIT:
        	default:
        		$formClass = $this->_formClass;
        		break;
        }
    	$form = new $formClass($this, $user, $acl, $options);
        $form->setName($this->getItemType().'Form');
        return $form;
    }

	/**
	 * Increments the view counter (when necessary) and
	 * optionally save to database
	 *
	 * @param User_Row $viewer
	 * @param boolean $andSave
	 */
	public function viewBy(User_Row $viewer, Zend_Controller_Request_Http $request)
	{
		if($this->author == $viewer->{User::COLUMN_USERID}){
			// Do not increment view counter if the viewer is the author
			return;
		}

		$prefetchHeader = $request->getHeader('X-moz');
		if($prefetchHeader == 'prefetch'){
			// Do not increment view counter in case of prefetch request
			return;
		}

		parent::viewBy($viewer, $request);
	}

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = ucfirst($this->getTitle());
		$return .= ' '.$this->getDescription();
		$return .= ' '.$this->getContent();
		return $return;
	}
}