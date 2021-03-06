$(function() {
    div = $('<div id="tabs">');
    $(div).addClass('ui-tabs');
    $(div).addClass('ui-widget');
    $(div).addClass('ui-widget-content');
    $(div).addClass('ui-corner-all');
    $('dl.zend_form').wrap(div);

    ul = $('<ul id="item_tabs" class="subsection_tabs">');
    $(ul).append($('<li class="tab"><a href="#tab1">Details</a></li>'));
    $(ul).append($('<li class="tab"><a href="#tab2">Validation</a></li>'));
    $(ul).append($('<li class="tab"><a href="#tab3">Select Lists</a></li>'));
    $(ul).append($('<li class="tab"><a href="#tab4">Options</a></li>'));

    dl_tab1 = $('<dl class="zend_form" id="tab1">');
    $('dt#label-label').appendTo(dl_tab1);
    $('dd#label-element').appendTo(dl_tab1);

    $('dt#code-label').appendTo(dl_tab1);
    $('dd#code-element').appendTo(dl_tab1);

    $('dt#description-label').appendTo(dl_tab1);
    $('dd#description-element').appendTo(dl_tab1);

    $('dt#renderer-label').appendTo(dl_tab1);
    $('dd#renderer-element').appendTo(dl_tab1);


    dl_tab2 = $('<dl class="zend_form" id="tab2">');
    $('dt#regex-label').appendTo(dl_tab2);
    $('dd#regex-element').appendTo(dl_tab2);

    $('dt#error-label').appendTo(dl_tab2);
    $('dd#error-element').appendTo(dl_tab2);


    dl_tab3 = $('<dl class="zend_form" id="tab3">');
    $('dt#options-label').appendTo(dl_tab3);
    $('dd#options-element').appendTo(dl_tab3);


    dl_tab4 = $('<dl class="zend_form" id="tab4">');
    $('dt#isRequired-label').appendTo(dl_tab4);
    $('dd#isRequired-element').appendTo(dl_tab4);

    $('dt#isUnique-label').appendTo(dl_tab4);
    $('dd#isUnique-element').appendTo(dl_tab4);

    $('dt#isIncludedInSummary-label').appendTo(dl_tab4);
    $('dd#isIncludedInSummary-element').appendTo(dl_tab4);


    dl_notab = $('<dl class="" id="notab">');
    $('dt#save-label').appendTo(dl_notab);
    $('dd#save-element').appendTo(dl_notab);

    $('dl.zend_form').remove();
    $('#tabs').prepend(dl_notab);
    $('#tabs').prepend(dl_tab4);
    $('#tabs').prepend(dl_tab3);
    $('#tabs').prepend(dl_tab2);
    $('#tabs').prepend(dl_tab1);
    $('#tabs').prepend(ul);

    $('dd#save-element').append($('<span>&nbsp;&nbsp;</span>'));
    $('dd#save-element').append($('<a href="#" onClick="history.back(); return false;">cancel</a>'));

    $('#tabs').tabs();
    $('input#label').focus();
});
