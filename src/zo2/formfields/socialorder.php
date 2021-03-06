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
defined('_JEXEC') or die;

class JFormFieldSocialorder extends JFormFieldHidden {

    protected $type = 'Socialorder';

    /**
     * Get the html for input
     *
     * @return string
     */
    public function getInput() {
        $assets = Zo2Assets::getInstance();
        //$document = JFactory::getDocument();
        $assets->addScript('zo2/js/adminsocial.min.js');

        /* Twitter */
        $layout_button['twitter'] = array(
            'none' => 'None',
            'horizontal' => 'Horizontal Count',
            'vertical' => 'Vertical Count'
        );
        /* Facebook */
        $layout_button['facebook'] = array(
            'standard' => 'Standard',
            'button_count' => 'Button Count',
            'box_count' => 'Box Count'
        );
        /* Buffer */
        $layout_button['buffer'] = array(
            'none' => 'None',
            'vertical' => 'Vertical Count',
            'horizontal' => 'Horizontal Count'
        );
        $layout_button['linkedin'] = array(
            'right' => 'Horizontal Count',
            'top' => 'Vertical Count',
            'none' => 'No Count'
        );
        $layout_button['google'] = array(
            'none' => 'None',
            'bubble' => 'Horizontal Bubble ',
            'vertical-bubble' => 'Vertical Bubble',
            'inline' => 'Inline '
        );
        $layout_button['youtube'] = array(
            'default' => 'Default',
            'full' => 'Full'
        );
        $layout_button['pinterest'] = array(
            'beside' => 'Horizontal Count',
            'above' => 'Vertical Count',
            'none' => 'No Count'
        );

        $layout_button['tumblr'] = array(
            '1' => 'Staff on Tumblr',
            '2' => 'Follow on Tumblr',
            '3' => 't'
        );

        // default
        $default = array(
            array(
                'name' => 'twitter',
                'index' => 1,
                'website' => 'Twitter',
                'link' => 'https://twitter.com/',
                'enable' => 1,
                'button_design' => 'vertical'
            ),
            array(
                'name' => 'facebook',
                'index' => 2,
                'website' => 'Facebook',
                'link' => 'https://www.facebook.com/',
                'enable' => 1,
                'button_design' => 'box_count'
            ),
            array(
                'name' => 'buffer',
                'index' => 3,
                'website' => 'Buffer',
                'link' => 'https://twitter.com/',
                'enable' => 1,
                'button_design' => 'vertical'
            ),
            array(
                'name' => 'linkedin',
                'index' => 4,
                'website' => 'Linkedin',
                'link' => 'http://www.linkedin.com',
                'enable' => 1,
                'button_design' => 'top'
            ),
            array(
                'name' => 'google',
                'index' => 5,
                'website' => 'Google',
                'link' => 'https://google.com/',
                'enable' => 1,
                'button_design' => 'standard_bubble',
            ),
            array(
                'name' => 'pinterest',
                'index' => 6,
                'website' => 'Pinterest',
                'link' => 'http://www.pinterest.com',
                'enable' => 1,
                'button_design' => 'vertical'
            ),
            array(
                'name' => 'tumblr',
                'index' => 7,
                'website' => 'Tumblr',
                'link' => 'http://www.tumblr.com',
                'enable' => 1,
                'button_design' => '1'
            )
        );

        $rows = json_decode($this->value);
        
        if (count($rows) == count($default)) {
            $value = $rows;
        } else {
            $value = JArrayHelper::toObject($default);
        }

        /* Table head */
        $html = '<table width="100%" id="social_options" class="table table-striped">
                    <thead>
                        <tr>
                            <th width="1%" class="index sequence nowrap center"></th>
                            <th width="1%" class="index sequence nowrap center">#</th>
                            <th width="40%" class="nowrap">Website</th>
                            <th width="10%" class="nowrap center isactive">Enable</th>
                            <th width="20%" class="">Button Design</th>
                        </tr>
                    </thead>
                    <tbody>';

        $count = 0;

        foreach ($value as $item) {
            /* array of button design value */
            $layouts = $layout_button[$item->name];
            $options = array();
            /* Build option list for button design select */
            foreach ($layouts as $key => $layout) {
                $options[] = JHtml::_('select.option', $key, JText::_($layout));
            }
            $html .= '<tr class="row' . $count . '">
                                    <td class="nowrap center" name="' . $item->name . '"><i class="icon-move hasTooltip" data-original-title="Drag and drop to change position of button"></i></td>
                                    <td class="index sequence order nowrap center">' . $item->index . '</td>
                                    <td class="left">
                                        <a href="' . $item->link . '" title="twitter">' . $item->website . '</a>
                                    </td>

                                     <td class="center">
                                        ' . $this->renderEnable($key, $item->enable) . '
                                    </td>

                                    <td class="">
                                        ' . JHtml::_('select.genericlist', $options, $item->name . '_button_design', 'class="inputbox"', 'value', 'text', $item->button_design, $item->name . '_button_design') . '
                                    </td>

                                </tr>';
            $count++;
        }


        $html .= '</tbody>
                </table>
            ';

        $js = 'jQuery("#social_options > tbody").sortable({
                        beforeStop: Zo2Social.updateIndex,
                        stop: Zo2Social.saveConfig
                    });';

        $assets->addScriptDeclaration($js);

        return $html . parent::getInput();
    }

    function renderEnable($name, $value = 0) {

        $name = 'enable_' . $name;
        $on = ($value) ? 'active btn-success' : '';
        $off = (!$value) ? 'active btn-danger' : '';
        $html = '
            <fieldset name="fs_' . $name . '" class="radio btn-group social-onoff ' . ((!$value) ? 'toggle-off' : '') . '">
                <input name="' . $name . '" id="' . $name . '" type="radio" value="' . $value . '">
                <label for="' . $name . '" class="btn on ' . $on . '">Yes</label>
                <label for="' . $name . '" class="btn off ' . $off . '">No</label>
            </fieldset>
        ';

        return $html;
    }

    function renderPosition($name, $active = 'top', $type = 'normal') {

        $array = array();

        if ($type == 'normal') {
            $array[] = JHtml::_('select.option', 'top', JText::_('Top'));
            $array[] = JHtml::_('select.option', 'bottom', JText::_('Bottom'));
        } else if ($type == 'floating') {
            $array[] = JHtml::_('select.option', 'float_left', JText::_('Float left'));
            $array[] = JHtml::_('select.option', 'float_right', JText::_('Float right'));
        }

        return JHtml::_('select.genericlist', $array, $name . '_position', 'class="inputbox"', 'value', 'text', $active, $name . '_position');
    }

}
