<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Db\DataSet\App;

use ZendY\Db\DataSet\Sortable;
use ZendY\Db\Filter;

/**
 * Zbiór pozycji słownikowych
 *
 * @author Piotr Zając
 */
class ListItem extends Sortable {
    /**
     * Właściwości komponentu
     */

    const PROPERTY_LIST = 'list';

    /**
     * Domyślne kolumny zbioru
     */
    const COL_LIST_ID = 'list_id';
    const COL_ITEM_ID = 'item_id';
    const COL_FLAG = 'flag';
    const COL_NAME = 'name';
    const COL_PARENT_LIST = 'parent_list';
    const COL_PARENT_ID = 'parent_id';
    const COL_DESCRIPTION = 'description';
    const COL_ICON = 'icon';
    const COL_SORT = 'sort';
    const COL_ACTIVE = 'active';

    /**
     * Domyślna nazwa tabeli
     */
    const TABLE_NAME = 'list_item';

    /**
     * Inicjalizacja obiektu
     * 
     * @return void
     */
    public function init() {
        parent::init();
        $this
                ->setTableName(self::TABLE_NAME)
                ->setPrimary(array(self::COL_LIST_ID, self::COL_ITEM_ID))
                ->setSortField(self::COL_SORT)
        ;
    }

    /**
     * Filtruje zbiór po podanym identyfikatorze słownika
     * 
     * @param int $id
     * @return \ZendY\Db\DataSet\App\Lists
     */
    public function setList($id) {
        $filter = new Filter();
        $filter->addFilter(self::COL_LIST_ID, $id);
        $this->filterAction(array('filter' => $filter));
        return $this;
    }

}
