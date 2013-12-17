<?php

namespace Application\Form;

use ZendY\JQuery;
use ZendY\Db\Form;
use ZendY\Db\DataSource;
use ZendY\Db\DataSet\App;
use ZendY\Db\Form\Element;

class Locale extends Form {

    public function init() {
        $this->setAttrib('id', 'localeForm');
        $this->setAjaxValidator(false);
        $this->setAction('');

        $dataSet = new App\Locale('locale');
        $dataSet->sortAction(array('field' => 'name'));
        $dataSource = new DataSource('localeSource');
        $dataSource->setDataSet($dataSet)
        ;

        $locale = \Zend_Registry::get('Zend_Locale');
        $element = new Element\Combobox('localeList');
        $element
                ->setListSource($dataSource)
                ->setKeyField('code')
                ->setListField(array('name'))
                ->setLabel('Language', 60)
                ->setWidth(100)
                ->setStaticRender()
                ->setValue($locale->toString())
                ->setOnEvent(JQuery::EVENT_CHANGE, '$(this).closest("form").trigger("submit");')
        ;

        $this->addElement($element);
    }

}

