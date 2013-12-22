/**
 * JS of Pdeditor_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


/**
 * Handles navigation for attribute selectbox.
 *
 * @return void
 */
function pdeditor_selectAttr(baseUrl) {
    var attr = document.getElementById('pdeditor_attr');
    attr = attr.options[attr.options.selectedIndex].value;
    window.location.href = baseUrl + attr;
}
