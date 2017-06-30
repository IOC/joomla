
//THIS PLUGIN MANAGES ELEMENTS FROM STUDIES
tinymce.PluginManager.add('manageelements', function(ed, link) {

    var PLUGINVARS = {
        TAGS: {
            A:                     'A',
            UL:                    'UL',
            LI:                    'LI',
            DIV:                   'DIV',
            P:                     'P',
            IMG:                   'IMG'
        },
        SELECTORS: {
            CLASS_TAB_NODE:        'nav-tabs',
            CLASS_TAB_CONTENT:     'tab-content',
            CLASS_TAB_PANE :       'tab-pane',
            CLASS_TAB_TITLE :      'tab-title',
            CLASS_TITLE_MORE_INFO: 'accordion-toggle',
            ID_MORE_INFO:          'panel-sections',
            CLASS_PANEL_CONTENT:   'panel-collapse',
            CLASS_PANEL_NODE:      'panel',
            CLASS_PANEL_TITLE:     'panel-title',
            CLASS_PANEL_BODY:      'panel-body',
            CLASS_PANEL_HEADING:   'panel-heading',
            CLASS_PANEL_RESOURCES: 'panel-modal-resources',
            CLASS_INSERT_LINK:     'mce-i-link',
            CLASS_STUDY_DATES:     'study-dates',
            CLASS_STUDY_BLOCK:     'study-block',
            CLASS_STUDY_BUTTON:    'icon-text',
            CLASS_STUDY_DATE:      'study-date',
            CLASS_SEMIHEADER:      'semiheader',
            CLASS_MODAL:           'modal',
            CLASS_MODAL_BODY:      'body-modal-resources',
            CLASS_MODAL_BOTTOM:    'bottom-modal-resource',
            CLASS_MODAL_CONTENT:   'body-modal-content',
            CLASS_MODAL_RESOURCE:  'modal-resource',
            CLASS_MODAL_SHORTNAME: 'shortname-modal-resource',
            CLASS_MODAL_TEXT:      'text-modal-resource',
            CLASS_MODAL_TITLE:     'title-modal-resource',
            CLASS_HIDE:            'ocult',
            CLASS_RESOURCE_TITLE:  'resource-title'
        },
        ATTRIBUTES: {
            ID:                    'id',
            CLASS:                 'class',
            HREF:                  'href',
            DATA_MCE_HREF:         'data-mce-href',
            DATA_TARGET:           'data-target',
            DATA_PANEL_ID:         'data-panel-id',
            DATA_PANEL_PARENT:     'data-panel-parent',
            SRC:                   'src'
        },
        LANG: {
            NEW_TAB:               'Nova pestanyzepa',
            NEW_PANEL_INFO:        'Nou panell<span class="custom-icon plus">&nbsp;</span>',
            NEW_RESOURCE:          'Nou recurs',
            NEW_RESOURCE_PANEL:    'Nou panell de recursos',
            NEW_SECTION:           'Nova secció',
            CONTENT_TAB:           '<div><p>Contingut de la pestanya</p></div>',
            DELETE_TAB:            'Estàs segur que vols esborrar la pestanya',
            CONTENT_PANEL:         '<div><p>Text del panell</p></div>',
            DELETE_PANEL:          'Estàs segur que vols esborrar el panell',
            DELETE_SECTION:        'Estàs segur que vols esborrar la secció',
            CONTENT_DATE:          'X - X setembre 20XX',
            RESOURCE_CODE:         'MXX'
        },
        PATHS: {
            RESOURCE:              'templates/ioc/images/resources/default-resource.jpg'
        }
    };

    var currentNode;
    var parentNode;
    var node;
    var nodes;
    var contentnode;
    var anchornode;
    var divnode;
    var randomId;
    var randomId2;
    var currentId;
    var studytab;

    ed.on('click', function(evt) {
        currentNode = evt.target;
        parentNode = currentNode.parentElement;
        studytab = (currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_TAB_TITLE) > -1
                        && (parentNode.parentElement.tagName == 'LI' && parentNode.parentElement.parentElement.className.indexOf(PLUGINVARS.SELECTORS.CLASS_TAB_NODE) > -1)
                    );
        if (studytab) {
            ed.fire('EditCustomAnchors', { node: parentNode });
            node = document.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_INSERT_LINK)[0];
            node.parentElement.click();
        }
        if (currentNode.tagName == 'A' && (currentNode.parentElement.className.indexOf(PLUGINVARS.SELECTORS.CLASS_PANEL_TITLE) > -1)) {
            ed.fire('EditCustomAnchors', { node: currentNode });
            node = document.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_INSERT_LINK)[0];
            node.parentElement.click();
        }
        if (currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_STUDY_BUTTON) > -1 && currentNode.parentElement.tagName == 'A') {
            ed.fire('EditCustomAnchors', { node: parentNode });
            node = document.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_INSERT_LINK)[0];
            node.parentElement.click();
        }
        if (currentNode.tagName == PLUGINVARS.TAGS.UL || currentNode.tagName == PLUGINVARS.TAGS.LI) {
            if (currentNode.tagName == PLUGINVARS.TAGS.UL && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_TAB_NODE) > -1) {
                if (currentNode.getElementsByTagName(PLUGINVARS.TAGS.LI).length < 4) {
                    //Add new tab
                    node = currentNode.firstChild.cloneNode(true);
                    anchornode = node.getElementsByTagName(PLUGINVARS.TAGS.A)[0];
                    contentnode = anchornode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_TAB_TITLE)[0];
                    randomId = Math.random().toString(36).substring(2, 15);
                    node.removeAttribute(PLUGINVARS.ATTRIBUTES.CLASS);
                    anchornode.setAttribute(PLUGINVARS.ATTRIBUTES.HREF, '#' + randomId);
                    anchornode.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_MCE_HREF, '#' + randomId);
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
                }
                return false;
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
                return false;
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
            return false;
        } else if (currentNode.parentElement.getAttribute('id') == PLUGINVARS.SELECTORS.ID_MORE_INFO && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_PANEL_NODE) > -1) {
            contentnode = currentNode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_TITLE_MORE_INFO)[0];
            if (confirm(PLUGINVARS.LANG.DELETE_PANEL + " '" + contentnode.textContent + "'?")) {
                    //Remove tab and content tab
                    currentNode.parentElement.removeChild(currentNode);
            }
            return false;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.DIV && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_STUDY_BLOCK) > -1) {
            //Remove new section
            contentnode = currentNode.firstChild;
            if (confirm(PLUGINVARS.LANG.DELETE_SECTION + " '" + contentnode.textContent + "'?")) {
                currentNode.parentElement.removeChild(currentNode);
            }
            return false;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.DIV && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_STUDY_DATES) > -1) {
            if (currentNode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_STUDY_BLOCK).length < 4) {
                //Add new section
                node = currentNode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_STUDY_BLOCK)[0].cloneNode(true);
                contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_SEMIHEADER)[0];
                contentnode.innerHTML = PLUGINVARS.LANG.NEW_SECTION;
                contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_STUDY_DATE)[0];
                contentnode.innerHTML = PLUGINVARS.LANG.CONTENT_DATE;
                currentNode.appendChild(node);
            }
            return false;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.P && parentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_MODAL_CONTENT) > -1) {
            if (parentNode.childNodes[1] == currentNode) {
                if (currentNode.className != PLUGINVARS.SELECTORS.CLASS_HIDE) {
                    currentNode.innerHTML = '&nbsp;';
                }
                currentNode.classList.toggle(PLUGINVARS.SELECTORS.CLASS_HIDE);
                return false;
            }
            return true;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.DIV && currentNode.className == PLUGINVARS.SELECTORS.CLASS_PANEL_BODY) {
            //Add new resource
            node = currentNode.firstChild.cloneNode(true);
            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_BOTTOM)[0];
            contentnode.innerHTML = PLUGINVARS.LANG.RESOURCE_CODE;
            randomId = Math.random().toString(36).substring(2, 15);
            node.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_TARGET, '#' + randomId);
            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_TEXT)[0];
            contentnode.innerHTML = PLUGINVARS.LANG.NEW_RESOURCE;
            contentnode = node.getElementsByTagName(PLUGINVARS.TAGS.IMG)[0];
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.SRC, PLUGINVARS.PATHS.RESOURCE);
            currentNode.appendChild(node);

            // Create new resource content
            contentnode = ed.getBody();
            currentId = parentNode.parentElement.getAttribute(PLUGINVARS.ATTRIBUTES.DATA_PANEL_ID);
            node = contentnode.querySelectorAll("[" + PLUGINVARS.ATTRIBUTES.DATA_PANEL_PARENT + "='" + currentId + "']")[0];
            contentnode = node.firstChild.cloneNode(true);
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.ID, randomId);
            divnode = contentnode.getElementsByTagName(PLUGINVARS.TAGS.IMG)[0]
            divnode.setAttribute(PLUGINVARS.ATTRIBUTES.SRC, PLUGINVARS.PATHS.RESOURCE);
            divnode = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_SHORTNAME)[0]
            divnode.innerHTML = PLUGINVARS.LANG.RESOURCE_CODE;
            divnode = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_TITLE)[0]
            divnode.innerHTML = PLUGINVARS.LANG.NEW_RESOURCE;
            node.appendChild(contentnode);
            return false;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.DIV && currentNode.className == PLUGINVARS.SELECTORS.CLASS_MODAL_RESOURCE) {
            contentnode = currentNode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_TEXT)[0];
            if (confirm(PLUGINVARS.LANG.DELETE_TAB + " '" + contentnode.textContent + "'?")) {
                //Remove current resource
                currentId = contentnode.parentNode.getAttribute(PLUGINVARS.ATTRIBUTES.DATA_TARGET).slice(1);
                currentNode.parentElement.removeChild(currentNode);
                //Remove content resource
                node = ed.dom.get(currentId);
                node.parentElement.removeChild(node);
            }
            return false;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.DIV && currentNode.className.indexOf(PLUGINVARS.SELECTORS.CLASS_PANEL_RESOURCES) > -1) {
            //Add new resource panel
            node = currentNode.firstChild.cloneNode(true);
            nodes = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_RESOURCE);
            for (var i=nodes.length - 1; i>0; i--) {
                nodes.item(i).parentElement.removeChild(nodes.item(i));
            }
            randomId = Math.random().toString(36).substring(2, 15);
            node.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_PANEL_ID, randomId);
            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_BOTTOM)[0];
            contentnode.innerHTML = PLUGINVARS.LANG.RESOURCE_CODE;
            randomId2 = Math.random().toString(36).substring(2, 15);
            anchornode = node.getElementsByTagName(PLUGINVARS.TAGS.A)[0];
            anchornode.setAttribute(PLUGINVARS.ATTRIBUTES.HREF, '#' + randomId2);
            anchornode.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_MCE_HREF, '#' + randomId2);
            contentnode = anchornode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_RESOURCE_TITLE)[0];
            contentnode.innerHTML = PLUGINVARS.LANG.NEW_RESOURCE_PANEL;
            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_PANEL_CONTENT)[0];
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.ID, randomId2);
            randomId2 = Math.random().toString(36).substring(2, 15);
            divnode = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_RESOURCE)[0];
            divnode.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_TARGET, '#' + randomId2);
            contentnode = node.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_TEXT)[0];
            contentnode.innerHTML = PLUGINVARS.LANG.NEW_RESOURCE;
            contentnode = node.getElementsByTagName(PLUGINVARS.TAGS.IMG)[0];
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.SRC, PLUGINVARS.PATHS.RESOURCE);
            currentNode.appendChild(node);

            //Create new panel resource content
            contentnode = ed.getBody();
            node = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_BODY)[0]
            contentnode = node.cloneNode(true);
            nodes = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL);
            for (var i=nodes.length - 1; i>0; i--) {
                nodes.item(i).parentElement.removeChild(nodes.item(i));
            }
            contentnode.setAttribute(PLUGINVARS.ATTRIBUTES.DATA_PANEL_PARENT, randomId);
            contentnode.firstChild.setAttribute(PLUGINVARS.ATTRIBUTES.ID, randomId2);
            divnode = contentnode.getElementsByTagName(PLUGINVARS.TAGS.IMG)[0]
            divnode.setAttribute(PLUGINVARS.ATTRIBUTES.SRC, PLUGINVARS.PATHS.RESOURCE);
            divnode = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_SHORTNAME)[0]
            divnode.innerHTML = PLUGINVARS.LANG.RESOURCE_CODE;
            divnode = contentnode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_MODAL_TITLE)[0]
            divnode.innerHTML = PLUGINVARS.LANG.NEW_RESOURCE;
            currentNode.appendChild(contentnode);
            return false;
        } else if (currentNode.tagName == PLUGINVARS.TAGS.DIV
            && currentNode.className == PLUGINVARS.SELECTORS.CLASS_PANEL_HEADING
            && parentNode.hasAttribute(PLUGINVARS.ATTRIBUTES.DATA_PANEL_ID)) {
            contentnode = currentNode.getElementsByClassName(PLUGINVARS.SELECTORS.CLASS_RESOURCE_TITLE)[0];
            if (confirm(PLUGINVARS.LANG.DELETE_TAB + " '" + contentnode.textContent + "'?")) {
                //Remove current resource
                currentId = parentNode.getAttribute(PLUGINVARS.ATTRIBUTES.DATA_PANEL_ID);
                contentnode = parentNode.parentElement;
                contentnode.removeChild(parentNode);
                //Remove content resource
                node = contentnode.querySelectorAll("[" + PLUGINVARS.ATTRIBUTES.DATA_PANEL_PARENT + "='" + currentId + "']")[0];
                node.parentElement.removeChild(node);
            }
            return false;
        }
    });
});
