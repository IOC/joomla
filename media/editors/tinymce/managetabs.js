
//THIS PLUGIN MANAGES TABS FROM STUDIES
tinymce.PluginManager.add('managetabs', function(ed, link) {

    var PLUGINVARS = {
        TAGS: {
            A:                     'A',
            UL:                    'UL',
            LI:                    'LI'
        },
        SELECTORS: {
            CLASS_TAB_NODE:        'nav-tabs',
            CLASS_TAB_CONTENT:     'tab-content',
            CLASS_TAB_PANE :       'tab-pane',
            CLASS_TITLE_MORE_INFO: 'accordion-toggle',
            ID_MORE_INFO:          'panel-sections',
            CLASS_PANEL_CONTENT:   'panel-collapse',
            CLASS_PANEL_NODE:      'panel',
            CLASS_PANEL_TITLE:     'panel-title',
            CLASS_STUDY_DEF:       'study-definition',
            CLASS_INSERT_LINK:     'mce-i-link'
        },
        ATTRIBUTES: {
            ID:                    'id',
            CLASS:                 'class',
            HREF:                  'href',
            DATA_MCE_HREF:         'data-mce-href'
        },
        LANG: {
            NEW_TAB:               'Nova pestanya',
            NEW_PANEL_INFO:        'Nou panell',
            CONTENT_TAB:           '<p>Contingut de la pestanya</p>',
            DELETE_TAB:            'Estàs segur que vols esborrar la pestanya',
            CONTENT_PANEL:         '<div><p>Text del panell</p></div>',
            DELETE_PANEL:          'Estàs segur que vols esborrar el panell'
        }
    };

    var currentNode;
    var node;
    var contentnode;
    var randomId;
    var currentId;

    ed.on('click', function(evt) {
        currentNode = evt.target;

        if (currentNode.tagName == 'A' && ((currentNode.parentElement.className.indexOf(PLUGINVARS.SELECTORS.CLASS_PANEL_TITLE) > -1)
            || (currentNode.parentElement.className.indexOf(PLUGINVARS.SELECTORS.CLASS_STUDY_DEF) > -1)
            || (currentNode.parentElement.tagName == 'LI' && currentNode.parentElement.parentElement.className.indexOf(PLUGINVARS.SELECTORS.CLASS_TAB_NODE) > -1))) {
            node = document.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_INSERT_LINK)[0];
            node.parentElement.click();
        }
        if (currentNode.tagName == PLUGINVARS.TAGS.UL || currentNode.tagName == PLUGINVARS.TAGS.LI) {
            if (currentNode.tagName == PLUGINVARS.TAGS.UL && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_TAB_NODE) > -1) {
                //Add new tab
                node = currentNode.firstChild.cloneNode(true);
                contentnode = node.getElementsByTagName(PLUGINVARS.TAGS.A)[0];
                randomId = Math.random().toString(36).substring(2, 15);
                node.removeAttribute(PLUGINVARS.ATTRIBUTES.CLASS);
                contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.HREF, '#' + randomId);
                contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_MCE_HREF, '#' + randomId);
                contentnode.innerHTML = PLUGINVARS.LANG.NEW_TAB;
                currentNode.appendChild(node);

                // Create new tab content
                contentnode = ed.getBody();
                node = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_TAB_CONTENT)[0]
                contentnode = node.firstChild.cloneNode(true);
                contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.ID, randomId);
                contentnode.className = PLUGINVARS.SELECTORS.CLASS_TAB_PANE;
                contentnode.innerHTML = PLUGINVARS.LANG.CONTENT_TAB;
                node.appendChild(contentnode);
            } else if (currentNode.tagName == PLUGINVARS.TAGS.LI && currentNode.parentElement.className.indexOf(PLUGINVARS.SELECTORS.CLASS_TAB_NODE) > -1) {
                contentnode = currentNode.getElementsByTagName(PLUGINVARS.TAGS.A)[0];
                if (confirm(PLUGINVARS.LANG.DELETE_TAB + " '" + contentnode.textContent + "'?")) {
                    //Remove current tab
                    currentId = contentnode.getAttribute(PLUGINVARS.ATTRIBUTES.DATA_MCE_HREF).slice(1);
                    node = ed.dom.get(currentId);

                    //Remove tab and content tab
                    currentNode.parentElement.removeChild(currentNode);
                    node.parentElement.removeChild(node);
                }
            }
        } else if (currentNode.getAttribute('id') == PLUGINVARS.SELECTORS.ID_MORE_INFO) {
            node = currentNode.firstChild.cloneNode(true);
            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_TITLE_MORE_INFO)[0];
            randomId = Math.random().toString(36).substring(2, 15);
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.HREF, '#' + randomId);
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_MCE_HREF, '#' + randomId);
            contentnode.innerHTML = PLUGINVARS.LANG.NEW_PANEL_INFO;

            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_PANEL_CONTENT)[0];
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.ID, randomId);
            contentnode = contentnode.firstChild;
            contentnode.innerHTML = PLUGINVARS.LANG.CONTENT_PANEL;

            currentNode.appendChild(node);
        } else if (currentNode.parentElement.getAttribute('id') == PLUGINVARS.SELECTORS.ID_MORE_INFO && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_PANEL_NODE) > -1) {
            contentnode = currentNode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_TITLE_MORE_INFO)[0];
            if (confirm(PLUGINVARS.LANG.DELETE_PANEL + " '" + contentnode.textContent + "'?")) {
                    //Remove tab and content tab
                    currentNode.parentElement.removeChild(currentNode);
            }
        }
    });
});
