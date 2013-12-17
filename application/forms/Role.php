<?php

namespace Application\Form;

use ZendY\Db\Form;
use ZendY\Form\Container\Panel;
use ZendY\Db\Form\Container\Navigator;
use ZendY\Css;
use ZendY\Db\DataSource;
use ZendY\Db\Filter;
use ZendY\Db\DataSet;
use ZendY\Db\Form\Element as DbElement;

class Role extends Form {

    public function init() {
        //ustawienie identyfikatora formularza
        $this->setAttrib('id', 'roleForm');
        //wyrównanie do wszystkich krawędzi
        $this->setAlign(Css::ALIGN_CLIENT);
        //ustawienie akcji formularza na stronę bieżącą        
        $this->setAjaxValidator(false);

        //zbiór danych przechowujący role użytkowników
        $roleSet = new DataSet\App\Role('role');
        //źródło danych do połączenia zbioru ról z kontrolkami
        $roleSource = new DataSource('roleSource', $roleSet);

        //kontrolka drzewa ról
        $roleList = new DbElement\Treeview('roleList');
        $roleList
                ->setListSource($roleSource)
                ->setKeyField(DataSet\App\Role::COL_ID)
                ->setListField(DataSet\App\Role::COL_NAME)
                ->setIconField(DataSet\App\Role::COL_CLASS)
                ->setAlign(Css::ALIGN_CLIENT)
        ;

        //panel wyrównany do lewej, będący kontenerem dla drzewa ról
        $panelLeft = new Panel();
        $panelLeft->addElement($roleList)
                ->setAlign(Css::ALIGN_LEFT)
                ->setWidth(300)
        ;
        //menu podręczne drzewa ról
        $contextMenu = new DbElement\ContextMenu('rolemenu');
        $contextMenu
                ->setDataSource($roleSource)
                ->setDelegate('.ui-tree-node-icon')
                ->setDataActions(array(
                    DataSet\NestedTree::ACTION_ADDBEFORE,
                    DataSet\Editable::ACTION_ADD,
                    DataSet\NestedTree::ACTION_ADDUNDER,
                    DataSet\NestedTree::ACTION_CUT,
                    DataSet\NestedTree::ACTION_PASTEUNDER,
                    DataSet\NestedTree::ACTION_PASTEBEFORE,
                    DataSet\NestedTree::ACTION_PASTEAFTER
                ));
        //dodanie menu podręcznego do lewego panelu        
        $panelLeft->addElement($contextMenu);
        //dodanie lewego panelu do formularza
        $this->addContainer($panelLeft);

        //kontrolka nazwy roli
        $elements[0] = new DbElement\Edit('name');
        $elements[0]
                ->setDataSource($roleSource)
                ->setDataField(DataSet\App\Role::COL_NAME)
                ->setLabel('Name');

        //zbiór ikon, jako zbiór stałych klasy ZendY\Css
        $iconSet = new DataSet\ClassConst('iconSet', '\ZendY\Css');
        $iconSet->setPrimary(DataSet\ClassConst::COL_VALUE)
                ->sortAction(array('field' => DataSet\ClassConst::COL_VALUE));
        //filtr danych wybiera tylko te stałe, które posiadają prefix 'ICON_'
        $iconFilter = new Filter();
        $iconFilter->addFilter(DataSet\ClassConst::COL_NAME, 'ICON_', DataSet\Base::OPERATOR_BEGIN);
        $iconSet->filterAction(array('filter' => $iconFilter));
        //źródło danych do połączenia zbioru ikon z kontrolką listy rozwijalnej
        $iconSource = new DataSource('iconSource', $iconSet);

        //kontrolka listy rozwijalnej do wyboru ikony dla roli użytkownika
        $elements[1] = new DbElement\IconCombobox('icon');
        $elements[1]
                ->setDataSource($roleSource)
                ->setDataField(DataSet\App\Role::COL_CLASS)
                ->setListSource($iconSource)
                ->setKeyField(DataSet\ClassConst::COL_VALUE)
                ->setListField(DataSet\ClassConst::COL_VALUE)
                ->setLabel('Icon')
                ->setWidth(150)
                ->setStaticRender()
        ;

        //panel wyrównany do wszystkich krawędzi na pozostałej wolnej powierzchni,
        //będący kontenerem dla kontrolki nazwy roli i listy wyboru ikony
        $panelRight = new Panel();
        $panelRight->addElements($elements)
                ->setAlign(Css::ALIGN_CLIENT)
        ;
        //dodanie panelu do formularza
        $this->addContainer($panelRight);

        //akcje nawigatora
        $actions = array(
            DataSet\Base::ACTION_FIRST,
            DataSet\Base::ACTION_PREVIOUS,
            DataSet\Base::ACTION_NEXT,
            DataSet\Base::ACTION_LAST,
            DataSet\Base::ACTION_REFRESH,
            DataSet\Base::ACTION_EXPORTEXCEL,
            DataSet\Base::ACTION_PRINT,
            array('action' => DataSet\Editable::ACTION_EDIT, 'shortkey' => 'F3'),
            array('action' => DataSet\Editable::ACTION_SAVE, 'shortkey' => 'Ctrl+S'),
            DataSet\Editable::ACTION_DELETE,
            DataSet\Editable::ACTION_CANCEL
        );

        //panel nawigatora rekordów wyrównany do dolnej krawędzi
        $nav = new Navigator();
        $nav
                ->setActions($actions)
                ->setDataSource($roleSource)
                ->setHeight(40)
                ->setAlign(Css::ALIGN_BOTTOM)
        ;

        //dodanie nawigatora do formularza
        $this->addContainer($nav);
    }

}
