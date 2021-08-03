tinymce.PluginManager.add('scrollfixed', function(ed, link) {

    var scrollPosition = 0;

    ed.on('GotoCurrentScroll', function(e) {
        ed.getWin().scrollTo(0, scrollPosition);
    });

    ed.on('init', function() {
         ed.getWin().onscroll = function( e ){
            scrollPosition = e.pageY;
        };
    });
});
