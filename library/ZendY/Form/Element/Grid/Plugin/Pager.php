<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY\Form\Element\Grid\Plugin;

use ZendY\Form\Element\Grid\Plugin\Custom as Plugin;
use ZendY\Db\DataSet\Base as DataSet;
use ZendY\Db\Form\Element\Button as DbButton;
use ZendY\Db\Form\Element\Expr as DbExpr;
use ZendY\Css;

/**
 * Wyświetla w dolnej części grida paginator 
 * (interfejs stronicujący dla nawigacji po stronach
 * i wspólnych operacji na rekordach).
 *
 * @author Piotr Zając
 */
class Pager extends Plugin {

    /**
     * Wywoływane zanim grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    public function preRender() {
        
    }

    /**
     * Wywoływane po tym jak grid wyśle kod do przeglądarki
     * 
     * @return void
     */
    public function postRender() {
        $this->_grid->addClass(Css::GRID_WITHPAGER);
        $pagerName = $this->_grid->getId() . '_pager';

        $html[] = sprintf(
                '<div id="%s" class="%s %s %s">'
                , $pagerName
                , Css::STATE_DEFAULT
                , Css::GRID_PAGER
                , Css::CORNER_BOTTOM
        );
        $html[] = sprintf('<div class="%s">', Css::GRID_PAGER_NAVI);
        $dataSource = $this->_grid->getListSource();

        $btn = new DbButton();
        $btn->setDataSource($dataSource)
                ->setDataAction(DataSet::ACTION_FIRSTPAGE)
                ->addClass(Css::GRID_PAGER_BUTTON)
        ;
        $html[] = $btn->render($this->_view);

        $btn = new DbButton();
        $btn->setDataSource($dataSource)
                ->setDataAction(DataSet::ACTION_PREVIOUSPAGE)
                ->addClass(Css::GRID_PAGER_BUTTON)
        ;
        $html[] = $btn->render($this->_view);

        $exprPage = new DbExpr();
        $exprPage->setDataSource($dataSource)
                ->setExpr(DataSet::EXPR_PAGE);

        $exprPageCount = new DbExpr();
        $exprPageCount->setDataSource($dataSource)
                ->setExpr(DataSet::EXPR_PAGECOUNT);

        $html[] = sprintf('<div class="%s">%s %s / %s</div>'
                , Css::GRID_PAGER_POSITION
                , $this->_view->translate('Page')
                , $exprPage->render()
                , $exprPageCount->render()
        );
        $btn = new DbButton();
        $btn->setDataSource($dataSource)
                ->setDataAction(DataSet::ACTION_NEXTPAGE)
                ->addClass(Css::GRID_PAGER_BUTTON)
        ;
        $html[] = $btn->render($this->_view);

        $btn = new DbButton();
        $btn->setDataSource($dataSource)
                ->setDataAction(DataSet::ACTION_LASTPAGE)
                ->addClass(Css::GRID_PAGER_BUTTON)
        ;
        $html[] = $btn->render($this->_view);

        $html[] = '</div>';
        $html[] = '</div>';
        $this->addHtml(implode(PHP_EOL, $html));
    }

    /**
     * Wywoływane zanim grid wyśle odpowiedź
     * 
     * @return void
     */
    public function preResponse() {
        
    }

    /**
     * Wywoływane po tym jak grid wyśle odpowiedź
     * 
     * @return void
     */
    public function postResponse() {
        
    }

}