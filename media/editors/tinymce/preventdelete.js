
//THIS PLUGIN PREVENTS DELETION OF CUSTOM ELEMENTS WHICH HAS DEFINED IN CUSTOMELEMENTS LIST
tinymce.PluginManager.add('preventdelete', function(ed, link) {

    var style;
    var dashed = false;
    //List of custom elements not to delete
    var customElements = [
                        "imatge-noticia",
                        "ioc-news",
                        "ioc-news-content",
                        "more-section",
                        "more-section-content",
                        "nav-tabs",
                        "panel-title",
                        "tab-pane",
                        "study-frame",
                        "study-term",
                        "study-definition",
                        "study-tabs",
                        "study-registration",
                        "study-date",
                        "semiheader",
                        "col-sm-2",
                        "col-sm-4",
                        "col-sm-8",
                        "col-sm-10"
    ];

    var getCaretCharacterOffsetWithin = function(element) {
        var caretOffset = 0;
        var doc = element.ownerDocument || element.document;
        var win = doc.defaultView || doc.parentWindow;
        var sel;
        if (typeof win.getSelection != "undefined") {
            sel = win.getSelection();
            if (sel.rangeCount > 0) {
                var range = win.getSelection().getRangeAt(0);
                var preCaretRange = range.cloneRange();
                preCaretRange.selectNodeContents(element);
                preCaretRange.setEnd(range.endContainer, range.endOffset);
                caretOffset = preCaretRange.toString().length;
            }
        } else if ( (sel = doc.selection) && sel.type != "Control") {
            var textRange = sel.createRange();
            var preCaretTextRange = doc.body.createTextRange();
            preCaretTextRange.moveToElementText(element);
            preCaretTextRange.setEndPoint("EndToEnd", textRange);
            caretOffset = preCaretTextRange.text.length;
        }
        return caretOffset;
    }


    ed.on('keydown', function(evt) {

        var node            = ed.selection.getNode();
        var range           = ed.selection.getRng();
        var currentNode     = range.endContainer.parentElement;
        var currentWrapper  = currentNode.className;
        var parentNode      = currentNode.parentElement;
        var parentWrapper   = parentNode.className;
        var startOffset     = getCaretCharacterOffsetWithin(currentNode);
        var endOffset       = range.endOffset;

        console.log('startOffset ' +  startOffset);

        // if delete Keys pressed
        if (evt.keyCode == 8 || evt.keyCode == 46) {

            // If no prent div iterate until we find it!
            if (parentNode.tagName != 'DIV') {
                while(parentNode && parentNode.tagName != 'DIV') {
                    //currentNode = currentNode.parentElement;
                    parentNode = parentNode.parentElement;
                }
            }

            if (!parentNode || parentNode.tagName != 'DIV') {
                console.log('No parent div');
                return;
            }

            console.log(parentNode.tagName);

            // Get styles from parentNode
            style = window.getComputedStyle(parentNode);
            dashed = style.getPropertyValue('border-top-style') == 'dashed';

            if (evt.keyCode == 46) {
                if(startOffset == currentNode.textContent.length
                && (parentNode.childNodes[parentNode.childNodes.length-1] === currentNode || dashed)) {
                    console.log('Spr avoided');
                    evt.preventDefault();
                    evt.stopPropagation();
                    return false;
                } else {
                    // study-tabs
                    style = window.getComputedStyle(currentNode.parentElement);
                    dashed = style.getPropertyValue('border-top-style') == 'dashed';
                    if (startOffset == currentNode.textContent.length
                        && currentNode.parentElement.tagName == 'LI' && dashed) {
                        console.log('Spr avoided on tabs');
                        evt.preventDefault();
                        evt.stopPropagation();
                        return false;
                    }
                }
            }
            if (evt.keyCode == 8) {
                style = window.getComputedStyle(currentNode.parentElement);
                dashed = style.getPropertyValue('border-top-style') == 'dashed';
                console.log('TABS '  + currentNode.parentElement.tagName);
                if (startOffset == "0" && currentNode.parentElement.tagName == 'LI' && dashed) {
                    console.log('Backspace avoided on tabs');
                    evt.preventDefault();
                    evt.stopPropagation();
                    return false;
                }
                // First level node
                style = window.getComputedStyle(currentNode);
                dashed = style.getPropertyValue('border-top-style') == 'dashed';
                if (currentWrapper.indexOf(" ") > -1) {
                    currentWrapper = currentWrapper.split(" ");
                    if (startOffset == "0"){
                        currentWrapper.forEach(function(classname) {
                          if (dashed || customElements.indexOf(classname) > -1) {
                            console.log('Backspace avoided - First level multiple class');
                            evt.preventDefault();
                            evt.stopPropagation();
                            return false;
                          }
                        });
                    }
                } else {
                    if (startOffset == "0" &&
                        (dashed || customElements.indexOf(currentWrapper) > -1)) {
                        console.log('Backspace avoided - First level single class');
                        evt.preventDefault();
                        evt.stopPropagation();
                        return false;
                    }
                }

                // Second level node
                style = window.getComputedStyle(parentNode);
                dashed = style.getPropertyValue('border-top-style') == 'dashed';
                if (parentWrapper.indexOf(" ") > -1) {
                    parentWrapper = parentWrapper.split(" ");
                    if (startOffset == "0"
                      && parentNode.childNodes[0] === currentNode) {
                        parentWrapper.forEach(function(classname) {
                            if (dashed || customElements.indexOf(classname) > -1) {
                                console.log('Backspace avoided - Second level multiple class');
                                evt.preventDefault();
                                evt.stopPropagation();
                                return false;
                            }
                        });
                    }
                } else {
                    if (startOffset == "0"
                      && (parentNode.childNodes[0] === currentNode
                        && (dashed || customElements.indexOf(parentWrapper) > -1)
                        )) {
                        console.log('Backspace avoided - Second level single class');
                        evt.preventDefault();
                        evt.stopPropagation();
                        return false;
                    }
                }
            }
        }
    });
});
