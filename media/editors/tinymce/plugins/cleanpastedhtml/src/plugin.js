tinymce.PluginManager.add('cleanpastedhtml', function(ed, link) {
    ed.on('PastePreProcess', function(e) {
        var holder = document.createElement("div");
        holder.innerHTML = e.content;
        var elementsWithStyle = holder.querySelectorAll("[style]");
        var i = 0;
        for (i = 0; i < elementsWithStyle.length; i++) {
            elementsWithStyle[i].removeAttribute("style");
        }
        var elementsWithClass = holder.querySelectorAll("[class]");
        for (i = 0; i < elementsWithClass.length; i++) {
            elementsWithClass[i].removeAttribute("class");
        }
        e.content = holder.innerHTML;
        if (/<img[^>]+src="data:/.test(e.content)) {
            e.content = "";
        }
    });
});