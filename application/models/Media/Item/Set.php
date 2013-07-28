<?php
class Media_Item_Set extends FilterIterator implements Countable
{
    protected $_items;
    protected $_user;
    protected $_acl;

    public function __construct(array $items, User_Row $user, Lib_Acl $acl)
    {
        $this->_items = $items;
        $this->_user = $user;
        $this->_acl = $acl;

        $iterator = new ArrayIterator($this->_items);
        parent::__construct($iterator);
    }

    /**
     * Determines if the current item should be allowed in an iteration
     *
     * @return boolean
     */
    public function accept()
    {
        $current = $this->current();
        $item = Media_Item_Factory::buildItem($current['id'], $current['mediaType']);
        if(empty($item)){
        	throw new Lib_Exception("Could not build media '{$current['id']}' of type '{$current['mediaType']}'");
        }
    	$status = $item->isReadableBy($this->_user, $this->_acl);
        return $status;
    }

    public function count()
    {
        $count = count($this->_items);
        return $count;
    }

	public function getNextItem(Media_Item_Row $currentItem)
	{
		foreach($this->_items as $index => $item){
			if($item['id'] == $currentItem->id ){
			    if(array_key_exists($index + 1, $this->_items)){
			        $media = Media_Item_Factory::buildItem($this->_items[$index + 1]['id'], $this->_items[$index + 1]['mediaType']);
			    	return $media;
			    } else {
			        return null;
			    }
			}
		}
	}

	public function getPreviousItem(Media_Item_Row $currentItem)
	{
		foreach($this->_items as $index => $item){
			if($item['id'] == $currentItem->id ){
			    if(array_key_exists($index - 1, $this->_items)){
			        $media = Media_Item_Factory::buildItem($this->_items[$index - 1]['id'], $this->_items[$index - 1]['mediaType']);
			    	return $media;
			    } else {
			        return null;
			    }
			}
		}
	}
}