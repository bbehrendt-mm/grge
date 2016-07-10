goog.provide('factory');

NF = function() {};

NF.row = function(classes) {
    return $('<div />').addClass('row').addClass(classes ? classes : '');
};

NF.cell = function(pad, rw, ro, classes) {
    var tmp = $('<div />').addClass('cell');
    if (rw === undefined) rw = 12;
    if (ro === undefined) ro = 0;
    if (pad) tmp.addClass('padded');

    if (typeof rw == 'object') {
        if (rw.desktop != undefined) tmp.addClass('rw-' + rw.desktop);
        if (rw.lg != undefined) tmp.addClass('rw-lg-' + rw.lg);
        if (rw.md != undefined) tmp.addClass('rw-md-' + rw.md);
        if (rw.sm != undefined) tmp.addClass('rw-sm-' + rw.sm);
    } else tmp.addClass('rw-' + rw);

    if (typeof ro == 'object') {
        if (ro.desktop != undefined) tmp.addClass('ro-' + ro.desktop);
        if (ro.lg != undefined) tmp.addClass('ro-lg-' + ro.lg);
        if (ro.md != undefined) tmp.addClass('ro-md-' + ro.md);
        if (ro.sm != undefined) tmp.addClass('ro-sm-' + ro.sm);
    } else tmp.addClass('ro-' + ro);

    return tmp.addClass(classes ? classes : '');
};

NF.scell = function(pad, rw, ro, classes) {
    var tmp = $('<div />').addClass('cell-small');
    if (rw === undefined) rw = 12;
    if (ro === undefined) ro = 0;
    if (pad) tmp.addClass('smallpad');

    if (typeof rw == 'object') {
        if (rw.desktop != undefined) tmp.addClass('rw-' + rw.desktop);
        if (rw.lg != undefined) tmp.addClass('rw-lg-' + rw.lg);
        if (rw.md != undefined) tmp.addClass('rw-md-' + rw.md);
        if (rw.sm != undefined) tmp.addClass('rw-sm-' + rw.sm);
    } else tmp.addClass('rw-' + rw);

    if (typeof ro == 'object') {
        if (ro.desktop != undefined) tmp.addClass('ro-' + ro.desktop);
        if (ro.lg != undefined) tmp.addClass('ro-lg-' + ro.lg);
        if (ro.md != undefined) tmp.addClass('ro-md-' + ro.md);
        if (ro.sm != undefined) tmp.addClass('ro-sm-' + ro.sm);
    } else tmp.addClass('ro-' + ro);

    return tmp.addClass(classes ? classes : '');
};

NF.n = function(node, classes, content, html) {
    var tmp = $('<' + node + ' />').addClass(classes ? classes : '');
    if (typeof content == 'undefined') return tmp;
    else if (typeof content == 'string') return html ? tmp.html(content) : tmp.text(content);
    else return tmp.append(content);
};

NF.input = function(type, content) {
    return NF.n('input', 'form_input').val(content).attr('type',type);
};

NF.separator = function(n) {
    return NF.n(n ? n : 'span', 'separator');
};

NF.img = function(src, classes) {
    return NF.n('img').attr('src', src).addClass(classes ? classes : '');
};

NF.icon = function(src, txt, classes) {
    var txt_is_null = !txt && txt !== 0;
    return NF.n('div', 'mlicon ' + classes).append(NF.img(src)).append(NF.n('span', '', !txt_is_null ? txt : '&nbsp;', txt_is_null))
};

NF.fa = function(name, spin) {
    return NF.n('i', 'fa ' + ((name.substr(0, 3) == 'fa-') ? name : ('fa-' + name)) + (spin ? ' fa-spin' : ''));
};

NF.select = function(options, preselect) {
    var s = NF.n('select');
    $.each(options, function(val,name) {
        var o = NF.n('option','',name).attr('value', val);
        if (preselect !== undefined && val == preselect) o.attr('selected','selected');
        s.append(o)
    });
    return s;
};