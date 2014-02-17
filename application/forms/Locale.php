<?php

namespace Application\Form;

use ZendY\JQuery;
use ZendY\Db\Form;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\App;
use ZendY\Db\Form\Element as DbElement;
use ZendY\Css;

class Locale extends Form {

    public function init() {
        $dataSet = new App\Locale('locale');
        $dataSet->sortAction(array('field' => 'name'));
        $dataSource = new DataSource(array(
                    DataSource::PROPERTY_NAME => 'localeSource',
                    DataSource::PROPERTY_DATASET => $dataSet
                ));

        $locale = \Zend_Registry::get('Zend_Locale');
        $element = new DbElement\Combobox(array(
                    DbElement\Combobox::PROPERTY_NAME => 'localeList',
                    DbElement\Combobox::PROPERTY_LABEL => array(
                        'text' => 'Language',
                        'width' => 80
                    ),
                    DbElement\Combobox::PROPERTY_WIDTH => 100,
                    DbElement\Combobox::PROPERTY_LISTSOURCE => $dataSource,
                    DbElement\Combobox::PROPERTY_LISTFIELD => App\Locale::COL_NAME,
                    DbElement\Combobox::PROPERTY_KEYFIELD => App\Locale::COL_CODE,
                    DbElement\Combobox::PROPERTY_STATICRENDER => true,
                    DbElement\Combobox::PROPERTY_VALUE => $locale->toString(),
                ));
        $element
                ->setOnEvent(JQuery::EVENT_CHANGE, '$(this).closest("form").trigger("submit");')
        ;

        $this->addElement($element);
    }

}

