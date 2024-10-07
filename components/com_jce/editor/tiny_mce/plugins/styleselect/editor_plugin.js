/* jce - 2.9.18 | 2021-12-09 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2021 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var each=tinymce.each,PreviewCss=tinymce.util.PreviewCss;tinymce.create("tinymce.plugins.StyleSelectPlugin",{init:function(ed,url){this.editor=ed},createControl:function(n,cf){var ed=this.editor;switch(n){case"styleselect":if(ed.getParam("styleselect_stylesheets")!==!1||ed.getParam("style_formats")||ed.getParam("styleselect_custom_classes"))return this._createStyleSelect()}},convertSelectorToFormat:function(selectorText){var format,ed=this.editor;if(selectorText){var selector=/^(?:([a-z0-9\-_]+))?(\.[a-z0-9_\-\.]+)$/i.exec(selectorText);if(selector){var elementName=selector[1];if("body"!==elementName){var classes=selector[2].substr(1).split(".").join(" "),inlineSelectorElements=tinymce.makeMap("a,img");return elementName?(format={title:selectorText},ed.schema.getTextBlockElements()[elementName]?format.block=elementName:ed.schema.getBlockElements()[elementName]||inlineSelectorElements[elementName.toLowerCase()]?format.selector=elementName:format.inline=elementName):selector[2]&&(format={inline:"span",selector:"*",title:selectorText.substr(1)}),ed.settings.importcss_merge_classes!==!1?format.classes=classes:format.attributes={class:classes},format.ceFalseOverride=!0,format}}}},_createStyleSelect:function(n){function loadClasses(){ed.settings.importcss_classes||ed.onImportCSS.dispatch(),Array.isArray(ed.settings.importcss_classes)&&(ctrl.hasClasses||(each(ed.settings.importcss_classes,function(item,idx){var name="style_"+(counter+idx);"string"==typeof item&&(item={selector:item,class:"",style:""});var fmt=self.convertSelectorToFormat(item.selector);fmt&&(ed.formatter.register(name,fmt),ctrl.add(fmt.title,name,{style:function(){return item.style||""}}))}),Array.isArray(ed.settings.importcss_classes)&&(ctrl.hasClasses=!0)))}var ctrl,self=this,ed=this.editor;ctrl=ed.controlManager.createListBox("styleselect",{title:"advanced.style_select",filter:!0,max_height:384,onselect:function(name){function isTextSelection(){var rng=ed.selection.getRng();return!!(rng&&rng.startContainer&&rng.endContainer)&&(3===rng.startContainer.nodeType&&3===rng.endContainer.nodeType&&!rng.collapsed)}var removedFormat,matches=[],node=ed.selection.getNode(),bookmark=ed.selection.getBookmark();return!(node===ed.getBody()&&!isTextSelection())&&(ed.focus(),ed.undoManager.add(),each(ctrl.items,function(item){ed.formatter.matchNode(node,item.value)&&matches.push(item.value)}),ed.selection.isCollapsed()||isTextSelection()&&(node=null),each(matches,function(match){name&&match!==name||(match&&ed.formatter.remove(match,{},node),removedFormat=!0)}),removedFormat||(ed.formatter.get(name)?ed.formatter.match(name)?ed.formatter.remove(name):ed.execCommand("ApplyFormat",!1,{name:name,args:{},node:node}):(node=ed.selection.getNode(),ed.dom.hasClass(node,name)?(ed.dom.removeClass(node,name),ed.nodeChanged()):ed.execCommand("ApplyFormat",!1,{name:"classname",args:{value:name},node:ed.selection.isCollapsed()?node:null}),ctrl.add(name,name))),ed.selection.moveToBookmark(bookmark),ed.undoManager.add(),!1)}}),ed.settings.styleselect_stylesheets===!1&&(ctrl.hasClasses=!0);var counter=0;return ed.onNodeChange.add(function(ed,cm,node){var ctrl=cm.get("styleselect");if(ctrl){loadClasses(ed,ctrl);var matches=[];each(ctrl.items,function(item){ed.formatter.matchNode(node,item.value)&&matches.push(item.value)}),ctrl.select(matches[0]),each(matches,function(match,i){ctrl.mark(match)})}}),ed.onPreInit.add(function(){function isValidAttribute(name){var isvalid=!0,invalid=ed.settings.invalid_attributes;return!invalid||(each(invalid.split(","),function(val){name===val&&(isvalid=!1)}),isvalid)}var formats=ed.getParam("style_formats"),styles=ed.getParam("styleselect_custom_classes","","hash");if(ed.formatter.register("classname",{attributes:{class:"%value"},selector:"*",ceFalseOverride:!0}),formats){if("string"==typeof formats)try{formats=JSON.parse(formats)}catch(e){formats=[]}each(formats,function(fmt){var name,keys=0;if(each(fmt,function(){keys++}),keys>1){if(name=fmt.name=fmt.name||"style_"+counter++,tinymce.is(fmt.attributes,"string")){fmt.attributes=ed.dom.decode(fmt.attributes);var frag=ed.dom.createFragment("<div "+tinymce.trim(fmt.attributes)+"></div>"),attribs=ed.dom.getAttribs(frag.firstChild);fmt.attributes={},each(attribs,function(node){var key=node.name,value=""+node.value;return!isValidAttribute(key)||("onclick"!==key&&"ondblclick"!==key||(fmt.attributes[key]="return false;",key="data-mce-"+key),void(fmt.attributes[key]=ed.dom.decode(value)))})}tinymce.is(fmt.styles,"string")&&(fmt.styles=ed.dom.parseStyle(fmt.styles),each(fmt.styles,function(value,key){value=""+value,fmt.styles[key]=ed.dom.decode(value)})),ed.formatter.register(name,fmt),ctrl.add(fmt.title,name,{style:function(){return PreviewCss(ed,fmt)}})}else ctrl.add(fmt.title)})}styles&&each(styles,function(val,key){var name,fmt;val&&(val=val.replace(/^\./,""),name="style_"+counter++,fmt={classes:val,selector:"*",ceFalseOverride:!0},ed.formatter.register(name,fmt),key&&(key=key.replace(/^\./,"")),ctrl.add(ed.translate(key),name,{style:function(){return PreviewCss(ed,fmt)}}))}),ctrl.onBeforeRenderMenu.add(function(){loadClasses()})}),ctrl}}),tinymce.PluginManager.add("styleselect",tinymce.plugins.StyleSelectPlugin)}();