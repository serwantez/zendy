<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

/**
 * Pomocnik CSS
 *
 * @author Piotr Zając
 */
class Css {
    /**
     * Rozmieszczenie kontenerów względem swoich kontenerów
     */

    const ALIGN_CLIENT = 'ui-align-client';
    const ALIGN_TOP = 'ui-align-top';
    const ALIGN_RIGHT = 'ui-align-right';
    const ALIGN_BOTTOM = 'ui-align-bottom';
    const ALIGN_LEFT = 'ui-align-left';
    const ALIGN_CENTER = 'ui-align-center';
    const ALIGN_HCENTER = 'ui-align-hcenter';
    const ALIGN_VCENTER = 'ui-align-vcenter';

    /**
     * Style dotyczące przycisków
     */
    const BUTTON = 'ui-button';
    const BUTTON_TEXT = 'ui-button-text';
    const BUTTON_TEXT_ONLY = 'ui-button-text-only';

    /**
     * Style dotyczące kalendarza
     */
    const CALENDAR = 'ui-calendar';
    const CALENDAR_BODY = 'ui-calendar-body';
    const CALENDAR_NAVI = 'ui-calendar-navi';
    const CALENDAR_NAVI_LEFT = 'ui-calendar-navi-left';
    const CALENDAR_NAVI_RIGHT = 'ui-calendar-navi-right';
    const CALENDAR_RANGE = 'ui-calendar-range';
    const CALENDAR_DAYNAMES = 'ui-calendar-daynames';

    /**
     * Style dotyczące listy kombo
     */
    const COMBOBOX = 'ui-combobox';

    /**
     * Style dotyczące menu podręcznego
     */
    const CONTEXTMENU = 'ui-contextmenu';

    /**
     * Style dotyczące zaokrągleń kontrolek
     */
    const CORNER_ALL = 'ui-corner-all';
    const CORNER_BOTTOM = 'ui-corner-bottom';
    const CORNER_LEFT = 'ui-corner-left';
    const CORNER_RIGHT = 'ui-corner-right';
    const CORNER_TOP = 'ui-corner-top';
    const CORNER_TL = 'ui-corner-tl';
    const CORNER_TR = 'ui-corner-tr';
    const CORNER_BL = 'ui-corner-bl';
    const CORNER_BR = 'ui-corner-br';

    /**
     * Style dotyczące okna dialogowego
     */
    const DIALOG = 'ui-dialog';
    const DIALOG_TITLE = 'ui-dialog-title';
    const DIALOG_TITLEBAR = 'ui-dialog-titlebar';
    const DIALOG_CONTENT = 'ui-dialog-content';
    const DIALOG_BUTTONPANE = 'ui-dialog-buttonpane';
    const DIALOG_BUTTONS = 'ui-dialog-buttons';
    const DIALOG_LOADING = 'ui-dialog-loading';

    /**
     * Style dotyczace dokumentów
     */
    const DOCUMENT = 'ui-document';
    const DOCUMENT_TITLE = 'ui-document-title';

    /**
     * Style dotyczące kontrolki tekstowej
     */
    const EDIT = 'ui-edit';
    const FORM = 'ui-form';
    const FRONT = 'ui-front';


    /**
     * Style dotyczące grida
     */
    const GRID = 'ui-grid';
    const GRID_BODY = 'ui-grid-body';
    const GRID_FIRSTROW = 'ui-grid-firstrow';
    const GRID_LABEL = 'ui-grid-label';
    const GRID_WITHPAGER = 'ui-grid-withpager';
    const GRID_PAGER = 'ui-grid-pager';
    const GRID_PAGER_NAVI = 'ui-grid-pager-navi';
    const GRID_PAGER_BUTTON = 'ui-grid-pager-button';
    const GRID_PAGER_POSITION = 'ui-grid-pager-position';
    const GRID_HEADER = 'ui-grid-header';
    const GRID_HEADER_CONTAINER = 'ui-grid-header-container';
    const GRID_HEADER_LABEL = 'ui-grid-header-label';
    const GRID_HEADERBODY = 'ui-grid-headerbody';

    /**
     * Style dotyczące pomocników
     */
    const HELPER_CLEARFIX = 'ui-helper-clearfix';
    const HELPER_HIDDEN = 'ui-helper-hidden';

    /**
     * Style dotyczące ikon
     */
    const ICON = 'ui-icon';

    /**
     * Klasy ikon
     */
    const ICON_CARAT1N = 'ui-icon-carat-1-n';
    const ICON_CARAT1NE = 'ui-icon-carat-1-ne';
    const ICON_CARAT1E = 'ui-icon-carat-1-e';
    const ICON_CARAT1SE = 'ui-icon-carat-1-se';
    const ICON_CARAT1S = 'ui-icon-carat-1-s';
    const ICON_CARAT1SW = 'ui-icon-carat-1-sw';
    const ICON_CARAT1W = 'ui-icon-carat-1-w';
    const ICON_CARAT1NW = 'ui-icon-carat-1-nw';
    const ICON_CARAT2NS = 'ui-icon-carat-2-n-s';
    const ICON_CARAT2EW = 'ui-icon-carat-2-e-w';
    const ICON_TRIANGLE1N = 'ui-icon-triangle-1-n';
    const ICON_TRIANGLE1NE = 'ui-icon-triangle-1-ne';
    const ICON_TRIANGLE1E = 'ui-icon-triangle-1-e';
    const ICON_TRIANGLE1SE = 'ui-icon-triangle-1-se';
    const ICON_TRIANGLE1S = 'ui-icon-triangle-1-s';
    const ICON_TRIANGLE1SW = 'ui-icon-triangle-1-sw';
    const ICON_TRIANGLE1W = 'ui-icon-triangle-1-w';
    const ICON_TRIANGLE1NW = 'ui-icon-triangle-1-nw';
    const ICON_TRIANGLE2NS = 'ui-icon-triangle-2-n-s';
    const ICON_TRIANGLE2EW = 'ui-icon-triangle-2-e-w';
    const ICON_ARROW1N = 'ui-icon-arrow-1-n';
    const ICON_ARROW1NE = 'ui-icon-arrow-1-ne';
    const ICON_ARROW1E = 'ui-icon-arrow-1-e';
    const ICON_ARROW1SE = 'ui-icon-arrow-1-se';
    const ICON_ARROW1S = 'ui-icon-arrow-1-s';
    const ICON_ARROW1SW = 'ui-icon-arrow-1-sw';
    const ICON_ARROW1W = 'ui-icon-arrow-1-w';
    const ICON_ARROW1NW = 'ui-icon-arrow-1-nw';
    const ICON_ARROW2NS = 'ui-icon-arrow-2-n-s';
    const ICON_ARROW2NESW = 'ui-icon-arrow-2-ne-sw';
    const ICON_ARROW2EW = 'ui-icon-arrow-2-e-w';
    const ICON_ARROW2SENW = 'ui-icon-arrow-2-se-nw';
    const ICON_ARROWSTOP1N = 'ui-icon-arrowstop-1-n';
    const ICON_ARROWSTOP1E = 'ui-icon-arrowstop-1-e';
    const ICON_ARROWSTOP1S = 'ui-icon-arrowstop-1-s';
    const ICON_ARROWSTOP1W = 'ui-icon-arrowstop-1-w';
    const ICON_ARROWTHICK1N = 'ui-icon-arrowthick-1-n';
    const ICON_ARROWTHICK1NE = 'ui-icon-arrowthick-1-ne';
    const ICON_ARROWTHICK1E = 'ui-icon-arrowthick-1-e';
    const ICON_ARROWTHICK1SE = 'ui-icon-arrowthick-1-se';
    const ICON_ARROWTHICK1S = 'ui-icon-arrowthick-1-s';
    const ICON_ARROWTHICK1SW = 'ui-icon-arrowthick-1-sw';
    const ICON_ARROWTHICK1W = 'ui-icon-arrowthick-1-w';
    const ICON_ARROWTHICK1NW = 'ui-icon-arrowthick-1-nw';
    const ICON_ARROWTHICK2NS = 'ui-icon-arrowthick-2-n-s';
    const ICON_ARROWTHICK2NESW = 'ui-icon-arrowthick-2-ne-sw';
    const ICON_ARROWTHICK2EW = 'ui-icon-arrowthick-2-e-w';
    const ICON_ARROWTHICK2SENW = 'ui-icon-arrowthick-2-se-nw';
    const ICON_ARROWTHICKSTOP1N = 'ui-icon-arrowthickstop-1-n';
    const ICON_ARROWTHICKSTOP1E = 'ui-icon-arrowthickstop-1-e';
    const ICON_ARROWTHICKSTOP1S = 'ui-icon-arrowthickstop-1-s';
    const ICON_ARROWTHICKSTOP1W = 'ui-icon-arrowthickstop-1-w';
    const ICON_ARROWRETURNTHICK1W = 'ui-icon-arrowreturnthick-1-w';
    const ICON_ARROWRETURNTHICK1N = 'ui-icon-arrowreturnthick-1-n';
    const ICON_ARROWRETURNTHICK1E = 'ui-icon-arrowreturnthick-1-e';
    const ICON_ARROWRETURNTHICK1S = 'ui-icon-arrowreturnthick-1-s';
    const ICON_ARROWRETURN1W = 'ui-icon-arrowreturn-1-w';
    const ICON_ARROWRETURN1N = 'ui-icon-arrowreturn-1-n';
    const ICON_ARROWRETURN1E = 'ui-icon-arrowreturn-1-e';
    const ICON_ARROWRETURN1S = 'ui-icon-arrowreturn-1-s';
    const ICON_ARROWREFRESH1W = 'ui-icon-arrowrefresh-1-w';
    const ICON_ARROWREFRESH1N = 'ui-icon-arrowrefresh-1-n';
    const ICON_ARROWREFRESH1E = 'ui-icon-arrowrefresh-1-e';
    const ICON_ARROWREFRESH1S = 'ui-icon-arrowrefresh-1-s';
    const ICON_ARROW4 = 'ui-icon-arrow-4';
    const ICON_ARROW4DIAG = 'ui-icon-arrow-4-diag';
    const ICON_EXTLINK = 'ui-icon-extlink';
    const ICON_NEWWIN = 'ui-icon-newwin';
    const ICON_REFRESH = 'ui-icon-refresh';
    const ICON_SHUFFLE = 'ui-icon-shuffle';
    const ICON_TRANSFEREW = 'ui-icon-transfer-e-w';
    const ICON_TRANSFERTHICKEW = 'ui-icon-transferthick-e-w';
    const ICON_FOLDERCOLLAPSED = 'ui-icon-folder-collapsed';
    const ICON_FOLDEROPEN = 'ui-icon-folder-open';
    const ICON_DOCUMENT = 'ui-icon-document';
    const ICON_DOCUMENTB = 'ui-icon-document-b';
    const ICON_NOTE = 'ui-icon-note';
    const ICON_MAILCLOSED = 'ui-icon-mail-closed';
    const ICON_MAILOPEN = 'ui-icon-mail-open';
    const ICON_SUITCASE = 'ui-icon-suitcase';
    const ICON_COMMENT = 'ui-icon-comment';
    const ICON_PERSON = 'ui-icon-person';
    const ICON_PRINT = 'ui-icon-print';
    const ICON_TRASH = 'ui-icon-trash';
    const ICON_LOCKED = 'ui-icon-locked';
    const ICON_UNLOCKED = 'ui-icon-unlocked';
    const ICON_BOOKMARK = 'ui-icon-bookmark';
    const ICON_TAG = 'ui-icon-tag';
    const ICON_HOME = 'ui-icon-home';
    const ICON_FLAG = 'ui-icon-flag';
    const ICON_CALCULATOR = 'ui-icon-calculator';
    const ICON_CART = 'ui-icon-cart';
    const ICON_PENCIL = 'ui-icon-pencil';
    const ICON_CLOCK = 'ui-icon-clock';
    const ICON_DISK = 'ui-icon-disk';
    const ICON_CALENDAR = 'ui-icon-calendar';
    const ICON_ZOOMIN = 'ui-icon-zoomin';
    const ICON_ZOOMOUT = 'ui-icon-zoomout';
    const ICON_SEARCH = 'ui-icon-search';
    const ICON_WRENCH = 'ui-icon-wrench';
    const ICON_GEAR = 'ui-icon-gear';
    const ICON_HEART = 'ui-icon-heart';
    const ICON_STAR = 'ui-icon-star';
    const ICON_LINK = 'ui-icon-link';
    const ICON_CANCEL = 'ui-icon-cancel';
    const ICON_PLUS = 'ui-icon-plus';
    const ICON_PLUSTHICK = 'ui-icon-plusthick';
    const ICON_MINUS = 'ui-icon-minus';
    const ICON_MINUSTHICK = 'ui-icon-minusthick';
    const ICON_CLOSE = 'ui-icon-close';
    const ICON_CLOSETHICK = 'ui-icon-closethick';
    const ICON_KEY = 'ui-icon-key';
    const ICON_LIGHTBULB = 'ui-icon-lightbulb';
    const ICON_SCISSORS = 'ui-icon-scissors';
    const ICON_CLIPBOARD = 'ui-icon-clipboard';
    const ICON_COPY = 'ui-icon-copy';
    const ICON_CONTACT = 'ui-icon-contact';
    const ICON_IMAGE = 'ui-icon-image';
    const ICON_VIDEO = 'ui-icon-video';
    const ICON_ALERT = 'ui-icon-alert';
    const ICON_INFO = 'ui-icon-info';
    const ICON_NOTICE = 'ui-icon-notice';
    const ICON_HELP = 'ui-icon-help';
    const ICON_CHECK = 'ui-icon-check';
    const ICON_BULLET = 'ui-icon-bullet';
    const ICON_RADIOOFF = 'ui-icon-radio-off';
    const ICON_RADIOON = 'ui-icon-radio-on';
    const ICON_PLAY = 'ui-icon-play';
    const ICON_PAUSE = 'ui-icon-pause';
    const ICON_SEEKNEXT = 'ui-icon-seek-next';
    const ICON_SEEKPREV = 'ui-icon-seek-prev';
    const ICON_SEEKEND = 'ui-icon-seek-end';
    const ICON_SEEKFIRST = 'ui-icon-seek-first';
    const ICON_STOP = 'ui-icon-stop';
    const ICON_EJECT = 'ui-icon-eject';
    const ICON_VOLUMEOFF = 'ui-icon-volume-off';
    const ICON_VOLUMEON = 'ui-icon-volume-on';
    const ICON_POWER = 'ui-icon-power';
    const ICON_SIGNALDIAG = 'ui-icon-signal-diag';
    const ICON_SIGNAL = 'ui-icon-signal';
    const ICON_BATTERY0 = 'ui-icon-battery-0';
    const ICON_BATTERY1 = 'ui-icon-battery-1';
    const ICON_BATTERY2 = 'ui-icon-battery-2';
    const ICON_BATTERY3 = 'ui-icon-battery-3';
    const ICON_CIRCLEPLUS = 'ui-icon-circle-plus';
    const ICON_CIRCLEMINUS = 'ui-icon-circle-minus';
    const ICON_CIRCLECLOSE = 'ui-icon-circle-close';
    const ICON_CIRCLETRIANGLEE = 'ui-icon-circle-triangle-e';
    const ICON_CIRCLETRIANGLES = 'ui-icon-circle-triangle-s';
    const ICON_CIRCLETRIANGLEW = 'ui-icon-circle-triangle-w';
    const ICON_CIRCLETRIANGLEN = 'ui-icon-circle-triangle-n';
    const ICON_CIRCLEARROWE = 'ui-icon-circle-arrow-e';
    const ICON_CIRCLEARROWS = 'ui-icon-circle-arrow-s';
    const ICON_CIRCLEARROWW = 'ui-icon-circle-arrow-w';
    const ICON_CIRCLEARROWN = 'ui-icon-circle-arrow-n';
    const ICON_CIRCLEZOOMIN = 'ui-icon-circle-zoomin';
    const ICON_CIRCLEZOOMOUT = 'ui-icon-circle-zoomout';
    const ICON_CIRCLECHECK = 'ui-icon-circle-check';
    const ICON_CIRCLESMALLPLUS = 'ui-icon-circlesmall-plus';
    const ICON_CIRCLESMALLMINUS = 'ui-icon-circlesmall-minus';
    const ICON_CIRCLESMALLCLOSE = 'ui-icon-circlesmall-close';
    const ICON_SQUARESMALLPLUS = 'ui-icon-squaresmall-plus';
    const ICON_SQUARESMALLMINUS = 'ui-icon-squaresmall-minus';
    const ICON_SQUARESMALLCLOSE = 'ui-icon-squaresmall-close';
    const ICON_GRIPDOTTEDVERTICAL = 'ui-icon-grip-dotted-vertical';
    const ICON_GRIPDOTTEDHORIZONTAL = 'ui-icon-grip-dotted-horizontal';
    const ICON_GRIPSOLIDVERTICAL = 'ui-icon-grip-solid-vertical';
    const ICON_GRIPSOLIDHORIZONTAL = 'ui-icon-grip-solid-horizontal';
    const ICON_GRIPSMALLDIAGONALSE = 'ui-icon-gripsmall-diagonal-se';
    const ICON_GRIPDIAGONALSE = 'ui-icon-grip-diagonal-se';

    /**
     * Style dotyczące kontrolki tekstowej z ikoną
     */
    const ICONEDIT = 'ui-iconedit';
    const ICONEDIT_BUTTON = 'ui-iconedit-button';
    const ICONEDIT_RIGHT_BUTTON = 'ui-iconedit-right-button';

    /**
     * Style dotyczące kontrolki obrazu
     */
    const IMAGE = 'ui-image';
    const IMAGE_FIT = 'ui-image-fit';
    const IMAGE_SCROLLED = 'ui-image-scrolled';
    const IMAGE_UPLOAD = 'ui-image-upload';

    /**
     * Style dotyczące widoczności
     */
    const INVISIBLE = 'ui-invisible';

    /**
     * Style dotyczące linku
     */
    const LINK = 'ui-link';

    /**
     * Style dotyczące listy rozwiniętej
     */
    const LISTBOX = 'ui-listbox';

    /**
     * Style dotyczące kontrolki długiego tekstu do odczytu
     */
    const LONGTEXT = 'ui-longtext';

    /**
     * Style dotyczące map google
     */
    const MAP = 'ui-map';
    const MAP_CANVAS = 'ui-map-canvas';
    const MAP_HEADER = 'ui-map-header';

    /**
     * Style dotyczące menu
     */
    const MENU = 'ui-menu';
    const MENU_ICONS = 'ui-menu-icons';
    const MENU_ITEM = 'ui-menu-item';
    /**
     * Style dotyczące odstępów wewnętrznych
     */
    const PADDING_ALL = 'ui-padding-all';

    /**
     * Style dotyczące kontrolki opcji radio
     */
    const RADIO = 'ui-radio';

    /**
     * Style dotyczące raportów
     */
    const REPORT = 'ui-report';
    const REPORT_TITLE = 'ui-report-title';
    const REPORT_SUBTITLE = 'ui-report-subtitle';

    /**
     * Wyświetlanie paska przewijania
     */
    const SCROLL_AUTO = 'ui-scroll-auto';
    const SCROLL_ENABLE = 'ui-scroll-enable';
    const SCROLL_DISABLE = 'ui-scroll-disable';

    /**
     * Style dotyczące listy sortowalnej
     */
    const SORTABLE_LISTBOX = 'ui-sortable-listbox';

    /**
     * Style dotyczące stanów kontrolek
     */
    const STATE_DEFAULT = 'ui-state-default';
    const STATE_ACTIVE = 'ui-state-active';
    const STATE_HIGHLIGHT = 'ui-state-highlight';
    const STATE_HOVER = 'ui-state-hover';
    const STATE_ERROR = 'ui-state-error';

    /**
     * Style dotyczące przycisków submit
     */
    const SUBMIT = 'ui-submit';
    const SUBMIT_ICON_PRIMARY = 'ui-submit-icon-primary';
    const SUBMIT_ICON_SECONDARY = 'ui-submit-icon-secondary';

    /**
     * Style dotyczące zakładek
     */
    const TABS_NAV = 'ui-tabs-nav';
    const TABS_NAV_ITEM = 'ui-tabs-nav-item';
    const TABS_PANEL = 'ui-tabs-panel';

    /**
     * Style dotyczące kontrolki tekstowej do odczytu
     */
    const TEXT = 'ui-text';

    /**
     * Rozmieszczenie tektu w kontenerze/kontrolce
     */
    const TEXT_ALIGN_HORIZONTAL_LEFT = 'ui-text-left';
    const TEXT_ALIGN_HORIZONTAL_RIGHT = 'ui-text-right';
    const TEXT_ALIGN_HORIZONTAL_CENTER = 'ui-text-center';
    const TEXT_ALIGN_HORIZONTAL_JUSTIFY = 'ui-text-justify';

    /**
     * Style dotyczące wieloliniowej kontrolki tekstowej
     */
    const TEXTAREA = 'ui-textarea';

    /**
     * Style dotyczące kontrolki TextFileView
     */
    const TEXTFILEVIEW = 'ui-textfileview';
    const TEXTFILEVIEW_CONTAINER = 'ui-textfileview-container';
    const TEXTFILEVIEW_ROWNUMBERS = 'ui-textfileview-rownumbers';
    const TEXTFILEVIEW_CONTENT = 'ui-textfileview-content';

    /**
     * Style dotyczące kontrolki drzewa
     */
    const TREEVIEW = 'ui-treeview';
    const TREE = 'ui-tree';
    const TREE_LEAF = 'ui-tree-leaf';
    const TREE_NODE = 'ui-tree-node';
    const TREE_NODE_ICON = 'ui-tree-node-icon';
    const TREE_NODE_HANDLE = 'ui-tree-node-handle';

    /**
     * Style dotyczące budowy kontrolek
     */
    const WIDGET = 'ui-widget';
    const WIDGET_HEADER = 'ui-widget-header';
    const WIDGET_CONTENT = 'ui-widget-content';

    /**
     * Tablica sposobów pozycjonowania kontenerów
     * 
     * @var array
     */
    static public $aligns = array(
        self::ALIGN_CLIENT,
        self::ALIGN_TOP,
        self::ALIGN_RIGHT,
        self::ALIGN_BOTTOM,
        self::ALIGN_LEFT
    );

    /**
     * Domyślny rozmiar ikon stosowanych w bibliotece
     * 
     * @var int
     */
    static public $iconSize = 16;

}