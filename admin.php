<?php

/**
 * Back-End of Pdeditor_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


/* utf-8-marker: äöüß */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('PDEDITOR_VERSION', '1beta1');


/**
 * Returns plugin version information.
 *
 * @return string  The (X)HTML.
 */
function pdeditor_version() {
    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Pdeditor_XH">Pdeditor_XH</a></h1>'."\n"
	    .'<p>Version: '.PDEDITOR_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2012 <a href="http://3-magi.net">Christoph M. Becker</a></p>'."\n"
	    .'<p style="text-align: justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align: justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align: justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns the attribute selectbox.
 *
 * @param string $default  The preselected attribute.
 * @return string  The (X)HTML.
 */
function pdeditor_attr_select($default) {
    global $sn, $pd_router;

    $url = '?pdeditor&amp;normal&amp;admin=plugin_main&amp;action=plugin_text&amp;pdeditor_attr=';
    $o = '<select id="pdeditor_attr" onchange="pdeditor_selectAttr(\''.$url.'\')">'."\n";
    $attrs = $pd_router->model->params;
    natcasesort($attrs);
    foreach ($attrs as $attr) {
	$sel = $attr == $default ? ' selected="selected"' : '';
	$o .= '<option'.$sel.'>'.$attr.'</option>'."\n";
    }
    $o .= '</select>'."\n";
    return $o;
}


/**
 * Returns the list of toplevel pages.
 *
 * @return array
 */
function pdeditor_toplevel_pages() {
    global $cl, $l;

    for ($i = 0; $i < $cl; $i++) {
	if ($l[$i] == 1) {$ta[] = $i;}
    }
    return $ta;
}


/**
 * Returns the list of child pages of page $i.
 *
 * @param int $i
 * @return array
 */
function pdeditor_child_pages($i) {
    global $cl, $l, $cf;

    $ta = array();
    $lc = $cf['menu']['levelcatch'];
    for ($j = $i+1; $j < $cl && $l[$j] > $l[$i]; $j++) {
	if ($l[$j] <= $lc) {
	    $ta[] = $j;
	    $lc = $l[$j];
	}
    }
    return $ta;
}


/**
 * Returns the view of all $pages displaying the attribute $attr.
 *
 * @param array $pages
 * @param string $attr
 * @return string  The (X)HTML.
 */
function pdeditor_page_list($pages, $attr) {
    global $h, $cl, $l, $pth, $pd_router, $plugin_tx;

    if (empty($pages)) {return '';}
    $ptx = $plugin_tx['pdeditor'];
    $warn = tag('img src="'.$pth['folder']['plugins'].'pdeditor/images/warn.png"'
	    .' alt="'.$ptx['message_headings'].'" title="'.$ptx['message_headings'].'"').' ';
    $o = "\n".'<ul>'."\n";
    $pd = $pd_router->find_all();
    foreach ($pages as $i) {
	$has_children = $i+1 < $cl && $l[$i+1] > $l[$i];
	$level = 'level'.$l[$i];
	$o .= '<li>'
		.($attr == 'url' && uenc($h[$i]) != $pd[$i]['url'] ? $warn : '')
		.$h[$i]
		.tag('input type="text" name="value[]" value="'.$pd[$i][$attr].'"')
		.pdeditor_page_list(pdeditor_child_pages($i), $attr)
		.'</li>'."\n";
    }
    $o .= '</ul>'."\n";
    return $o;
}


function pdeditor_admin_main() {
    global $hjs, $pth, $h, $l, $cl, $sn, $tx, $pd_router, $plugin_tx;

    $ptx = $plugin_tx['pdeditor'];
    include_once $pth['folder']['plugins'].'jquery/jquery.inc.php';
    include_jQuery();
    $hjs .= '<script type="text/javascript" src="'.$pth['folder']['plugins'].'pdeditor/pdeditor.js"></script>'."\n";
    $attr = isset($_GET['pdeditor_attr']) ? $_GET['pdeditor_attr'] : 'url';
    $o = '';
    $o .= '<div id="pdeditor">'."\n"
	    .'<table class="edit" style="width:100%">'."\n".'<tr>'."\n".'<td>'."\n".'<strong>Attribute</strong>: '."\n"
	    .pdeditor_attr_select($attr).'</td>'."\n"
	    .'<td><a href="?pdeditor&amp;admin=plugin_main&amp;action=delete&amp;pdeditor_attr='
		.$attr.'" onclick="return confirm(\''.addcslashes($ptx['warning_delete'], "\n\r\'\\").'\')">'
		.$ptx['label_delete'].'</a></td>'."\n"
	    .'</tr>'."\n".'</table>'."\n"
	    .'<form action="?pdeditor&amp;admin=plugin_main&amp;action=save&amp;pdeditor_attr='
		.$attr.'" method="POST" accept-charset="UTF-8">';
    $o .= pdeditor_page_list(pdeditor_toplevel_pages(), $attr)
	    .tag('input type="submit" class="submit" value="'.ucfirst($tx['action']['save']).'"')."\n"
	    .'</form>'."\n"
	    .'</div>'."\n";
    return $o;
}


/**
 * Saves the posted page data. Returns the main admin view.
 *
 * @return string  The (X)HTML.
 */
function pdeditor_admin_save() {
    global $cl, $pd_router;

    if (isset($_POST['value'])) {
	$pd = $pd_router->find_all();
	foreach ($_POST['value'] as $id => $value) {
	    $pd[$id][$_GET['pdeditor_attr']] = stsl($value);
	}
	$pd_router->model->refresh($pd);
    }
    return pdeditor_admin_main();
}


/**
 * Deletes a page data attribute. Returns the main admin view.
 *
 * @return string  The (X)HTML.
 */
function pdeditor_delete_attr() {
    global $pd_router;

    $attr = stsl($_GET['pdeditor_attr']);
    $key = array_search($attr, $pd_router->model->params);
    if ($key !== FALSE) {unset($pd_router->model->params[$key]);}
    for ($i = 0; $i < count($pd_router->model->data); $i++) {
	unset($pd_router->model->data[$i][$attr]);
    }
    unset($pd_router->model->temp_data[$attr]);
    $pd_router->model->save();
    return pdeditor_admin_main();
}

/**
 * Handle plugin administration.
 */
if (isset($pdeditor)) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
	case '':
	    $o .= pdeditor_version();
	    break;
	case 'plugin_main':
	    switch ($action) {
		case 'delete':
		    $o .= pdeditor_delete_attr();
		    break;
		case 'save':
		    $o .= pdeditor_admin_save();
		    break;
		default:
		    $o .= pdeditor_admin_main();
	    }
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}
