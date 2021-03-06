<?php

/**
 * Zo2 (http://www.zo2framework.org)
 * A powerful Joomla template framework
 *
 * @link        http://www.zo2framework.org
 * @link        http://github.com/aploss/zo2
 * @author      ZooTemplate <http://zootemplate.com>
 * @copyright   Copyright (c) 2013 APL Solutions (http://apl.vn)
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class Zo2Component_header_logo
 *
 * This class will prepend the logo, sitename, slogan to the header_logo position
 */
class Zo2Component_footer_copyright extends Zo2Component {

    public $position = Zo2Component::RENDER_AFTER;

    public function render() {
        $zo2 = Zo2Framework::getInstance();
        $logo = $zo2->get('footer_logo');
        $copyright = $zo2->get('footer_copyright');
        $gototop = $zo2->get('footer_gototop');

        $html = '<footer>';
        $html .= '<section class="copyright" style="text-align:center">' . $copyright . '</section>';
        if ($logo == 1) {
            $html .= '<a title="Powered by Zo2Framework" class="footer_zo2_logo" href="http://zo2framework.org" style="display:block;">';
            $html .= '<img src="' . JUri::root() . '/templates/zo2_hallo/assets/zo2/images/zo2logo.svg" />';
            $html .= '</a>';
        }
        if ($gototop) {
            $html .= '<a style="display: inline;" href="#" id="gototop" title="Go to top"><i class="fa fa-chevron-up"></i></a>';

            $script = 'jQuery("#gototop").click(function(){jQuery("body, html").animate({scrollTop: 0}); return false;});var scrollDiv = document.createElement("div");
            jQuery(window).scroll(function () {
                if (jQuery(this).scrollTop() != 0) {
                    jQuery("#gototop").fadeIn();
                } else {
                    jQuery("#gototop").fadeOut();
                }
            });';
            $assets = Zo2Assets::getInstance();
            $assets->addScriptDeclaration($script);
        }
        $html .= '</footer>';

        return $html;
    }

}
