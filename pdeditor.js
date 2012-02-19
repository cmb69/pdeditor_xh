/* utf-8-marker: äöüß */

//function pdeditor_onChange(baseUrl) {
//    var tab = document.getElementById('pdeditor-tab');
//    tab = tab.options[tab.options.selectedIndex].value;
//    var page = document.getElementById('pdeditor-page');
//    page = page.options[page.options.selectedIndex].value;
//    window.location.href = baseUrl + '&tab=' + tab + '&page=' + page;
//}


function pdeditor_selectAttr(baseUrl) {
    var attr = document.getElementById('pdeditor-attr');
    attr = attr.options[attr.options.selectedIndex].value;
    window.location.href = baseUrl + '&attr=' + attr;
}


//function pdeditor_collapse(tr) {
//    var level = tr.getAttribute('class'); // TODO: className?
//    //console.log(level);
//    //console.log(tr.nextSibling.nextSibling);
//    //console.log(tr.firstChild);
//    if (tr.firstChild.firstChild.data == '-') {
//        tr.firstChild.firstChild.data = '+';
//        var nextTr = tr;
//        while ((nextTr = nextTr.nextSibling.nextSibling) != null && nextTr.getAttribute('class') > level) {
//            nextTr.style.display = 'none';
//        }
//    } else { // TODO: don't explode if children is +
//        //console.log('explode');
//        tr.firstChild.firstChild.data = '-';
//        var nextTr = tr;
//        while ((nextTr = nextTr.nextSibling.nextSibling) != null && nextTr.getAttribute('class') > level) {
//            nextTr.style.display = 'table-row';
//        }
//   }
//}



function pdeditor_collapse() {
    var tr = $(this).parents('tr');
    var lvl = tr.attr('class');
    while ((tr = tr.next()) != null && tr.attr('class') > lvl) {
        tr.hide();
    }
    $(this).removeClass('expanded');
    $(this).addClass('collapsed');
}

function pdeditor_expand() {
    var tr = $(this).parents('tr');
    var lvl = tr.attr('class');
    while ((tr = tr.next()) != null && tr.attr('class') > lvl) {
        tr.show();
    }
    $(this).removeClass('collapsed');
    $(this).addClass('expanded');
}

$(function() {
    $('.toggler').toggle(pdeditor_collapse, pdeditor_expand);
})
