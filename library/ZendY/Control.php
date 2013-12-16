<?php

/**
 * ZendY
 *
 * @copyright E-FISH sp. z o.o. (http://www.efish.pl/)
 */

namespace ZendY;

/**
 * Klasa bazowa dla wszystkich kontrolek.
 * Kontrolki są komponentami wizualnymi.
 *
 * @author Piotr Zając
 */
abstract class Control extends Component {

    use JQueryTrait;
}
