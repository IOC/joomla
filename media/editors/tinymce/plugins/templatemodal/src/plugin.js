tinymce.PluginManager.add('templatemodal', function(ed, link) {

    // class names for parent nodes
    var avoidElements = [
                    'file-modal-module',
                    'modal-modules',
                    'nav-tabs',
                    'panel-body',
                    'panel-collapse',
                    'study-button',
                    'tab-pane',
                    'ioc-news-content',
                    'columns',
                    'column'
    ];

    // class names for current nodes
    var specialElements = [
                       'body-modal-module',
                       'bottom-modal-module',
                       'icon-text',
                       'tab-title',
                       'ioc-news',
                       'ioc-news-content',
                       'body-modal-resource',
                       'panel-body'
    ];

    var show = function (node) {
        ed.windowManager.open( {
            title: tinymce.util.I18n.translate("Text"),
            body: {
                type: 'textbox',
                name: 'content',
                value: node.innerHTML,
                multiline: true,
                minWidth: 700,
                minHeight: 150,
            },
            onsubmit: function( e ) {
                var myRegexp = /^(\n+)|(\n)+$|/g;
                e.data.content = e.data.content.replace(myRegexp, '');
                if (e.data.content.length == 0) {
                    e.data.content = '&nbsp;';
                }
                node.innerHTML = e.data.content;
            },
            onclose: function ( e ) {
                ed.fire('GotoCurrentScroll');
            }
        });
    };

    ed.on('click', function( evt ) {
        var range           = ed.selection.getRng();
        var currentNode     = range.endContainer.parentElement;
        var parentNode      = currentNode.parentElement || currentNode;
        var currentClass    = '';
        var avoidElement = false;

        // If no parent div, iterate until we find it!
        style = window.getComputedStyle(parentNode);
        if (parentNode.tagName != 'DIV' || style.getPropertyValue('border-top-style') != 'dashed') {
            while(parentNode && parentNode.tagName != 'HTML' && (parentNode.tagName != 'DIV' || style.getPropertyValue('border-top-style') != 'dashed')) {
                parentNode = parentNode.parentElement;
                style = window.getComputedStyle(parentNode);
            }
        }

        currentClass = parentNode.className.split(' ');

        currentClass.forEach(function(classname) {
            avoidElement = avoidElement || avoidElements.includes(classname);
        });

        currentClass = currentNode.className.split(' ');

        currentClass.forEach(function(classname) {
            avoidElement = avoidElement || specialElements.includes(classname);
        });

        console.log(currentNode.className);

        if (currentNode.tagName != 'A'
            && !avoidElement
            && (window.getComputedStyle(currentNode).getPropertyValue('border-top-style') == 'dashed'
            || window.getComputedStyle(parentNode).getPropertyValue('border-top-style') == 'dashed')) {
            show(currentNode);
        }
    });
});
