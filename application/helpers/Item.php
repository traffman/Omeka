<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OmekaThemes
 * @subpackage Omeka_View_Helper
 */

/**
 * Helper used to retrieve metadata for an item.
 *
 * @see item()
 * @package OmekaThemes
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 */
class Omeka_View_Helper_Item extends Omeka_View_Helper_ElementText
{    
    public function item(Item $item, 
                         $elementSetName, 
                         $elementName = null, 
                         $options     = array())
    {
        return $this->_get($item, $elementSetName, $elementName, $options);
    }
    
    /**
     * Retrieve a special value of an item that does not correspond to an 
     * Element record. Examples include the database ID of the item, the
     * name of the item type, the name of the collection, etc.
     * 
     * @param string
     * @return mixed
     **/
    protected function _getRecordMetadata($specialValue)
    {
        $item = $this->_record;
        switch (strtolower($specialValue)) {
            case 'id':
                return $item->id;
                break;
            case 'item type name':
                return $item->Type->name;
                break;
            case 'date added':
                return $item->added;
                break;
            case 'collection name':
                return $item->Collection->name;
                break;
            case 'featured':
                return $item->featured;
                break;
            case 'public':
                return $item->public;
                break;
            default:
                throw new Exception("'$specialValue' is an invalid special value.");
                break;
        }
    }
}
