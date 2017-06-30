tinymce.PluginManager.add('customizedanchors', function(ed, link) {
    var htmlcontent = '';
    var node;

    ed.on('EditCustomAnchors', function(e) {
        htmlcontent = e.node.outerHTML.trim();
        node = e.node;
    });

    ed.on('change', function(e) {
        if (htmlcontent != '') {
            ed.focus();
            var myRegexp = /(?:href=)(".*?")/g;
            var matchold = myRegexp.exec(htmlcontent);
            var matchnew = myRegexp.exec(node.outerHTML);
            if (1 in matchold && 1 in matchnew && matchold[1] != matchnew[1] ) {
                htmlcontent = htmlcontent.replace(new RegExp(matchold[1], 'g'), matchnew[1]);
            }
            var newcontent = node.innerText.trim();
            if (newcontent.length > 0) {
                newcontent = '>' + newcontent[0].toUpperCase() + newcontent.substr(1).toLowerCase();
                var tmp = document.createElement("DIV");
                tmp.innerHTML = htmlcontent;
                var oldcontent = '>' + tmp.innerText.trim();
                node.innerHTML = htmlcontent.replace(oldcontent, newcontent);
            } else {
                node.innerHTML = htmlcontent;
            }
            htmlcontent = '';
            ed.execCommand('mceInsertContent', false, node.innerHTML);
        }
    });

    ed.on('focusin', function(e) {
        ed.fire('GotoCurrentScroll');
    });
});
