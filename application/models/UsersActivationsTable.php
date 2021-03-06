<?php
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class UsersActivationsTable extends Omeka_Db_Table
{
    
    public function findByUrl($url)
    {
        return $this->fetchObject($this->getSelect()->where('url = ?', $url)->limit(1));
    }
}
