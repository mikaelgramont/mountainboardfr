<?php
class Media_Album_Aggregate_User extends Media_Album_Aggregate
{
	protected $_rowClass = 'Media_Album_Aggregate_User_Row';

    /**
     * Fetches one row in an object of type Zend_Db_Table_Row_Abstract,
     * or returns null if no row matches the specified criteria.
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Row_Abstract|null The row results per the
     *     Zend_Db_Adapter fetch mode, or null if no row found.
     */
    public function fetchRowMod($where = null, $order = null, User_Row $user, $page = 1)
    {
        if (!($where instanceof Zend_Db_Table_Select)) {
            $select = $this->select();

            if ($where !== null) {
                $this->_where($select, $where);
            }

            if ($order !== null) {
                $this->_order($select, $order);
            }

            $select->limit(1);

        } else {
            $select = $where->limit(1);
        }

        $rows = $this->_fetch($select);

        if (count($rows) == 0) {
            return null;
        }

        $data = array(
            'table'   => $this,
            'data'     => $rows[0],
            'readOnly' => $select->isReadOnly(),
            'stored'  => true,
        	'user' => $user,
        	'page' => $page
        );

        if (!class_exists($this->_rowClass)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($this->_rowClass);
        }
        return new $this->_rowClass($data);
    }
}