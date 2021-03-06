$(function() {
    div = $('<div id="tabs">');
    $(div).addClass('ui-tabs');
    $(div).addClass('ui-widget');
    $(div).addClass('ui-widget-content');
    $(div).addClass('ui-corner-all');
    $('dl.zend_form').wrap(div);


    ul = $('<ul id="item_tabs" class="subsection_tabs">');
    $(ul).append($('<li class="tab"><a href="#tab1">Details</a></li>'));
    $(ul).append($('<li class="tab"><a href="#tab2">Theme and Tabs</a></li>'));

    dl_tab1 = $('<dl class="zend_form" id="tab1">');
    $('dt#name-label').appendTo(dl_tab1);
    $('dd#name-element').appendTo(dl_tab1);

    $('dt#code-label').appendTo(dl_tab1);
    $('dd#code-element').appendTo(dl_tab1);

    $('dt#description-label').appendTo(dl_tab1);
    $('dd#description-element').appendTo(dl_tab1);

    $('dt#title-label').appendTo(dl_tab1);
    $('dd#title-element').appendTo(dl_tab1);


    dl_tab2 = $('<dl class="zend_form" id="tab2">');
    $('dt#theme-label').appendTo(dl_tab2);
    $('dd#theme-element').appendTo(dl_tab2);

    $('dt#tabs-label').appendTo(dl_tab2);
    $('dd#tabs-element').appendTo(dl_tab2);

    dl_notab = $('<dl class="" id="notab">');
    $('dt#save-label').appendTo(dl_notab);
    $('dd#save-element').appendTo(dl_notab);


    $('dl.zend_form').remove();
    $('#tabs').prepend(dl_notab);
    $('#tabs').prepend(dl_tab2);
    $('#tabs').prepend(dl_tab1);
    $('#tabs').prepend(ul);

    $('dd#save-element').append($('<span>&nbsp;&nbsp;</span>'));
    $('dd#save-element').append($('<a href="#" onClick="history.back(); return false;">cancel</a>'));

    $('#tabs').tabs();
    $('input#name').focus();
});
