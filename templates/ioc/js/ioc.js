(function($){
    $(document).ready(function($) {
        var $indicators = $('ol.carousel-indicators');
        var linkhash = '';
        var isapanel = false;
        var $filtering = $('#ioc-filter-data');
        var $avisos = $('.ioc-front-page .avisos');
        var numavisos = 0;
        var current = 0;
        var nodesavisos;
        var slidingleft = false;
        var slidingright = false;
        var $menu = $('.ioc-menu');
        var $filter = $('.filter-dropdown');
        var currenthash = '#' + window.location.hash.replace(/[^a-zA-Z0-9_-]*/g, '');
        var $faqs = $('.panel-faqs');
        var originwidth = $(window).width();
        var PLUGINVARS = {
            HTML: {
                PREVIOUS:       '<div class="button-tab-nav prev">' + Joomla.JText._('TPL_IOC_TAB_PREVIOUS') + '</div>',
                NEXT:           '<div class="button-tab-nav next">' + Joomla.JText._('TPL_IOC_TAB_NEXT') + '</div>',
                BUTTONDIV:      '<div class="button-div panel-body"></div>'
            },
            SELECTORS: {
                BUTTONDIV:      '.button-div',
                PANELGROUP:     '.study-tabs .panel-group.responsive',
                PANELCOLLAPSE:  '.study-tabs .panel-collapse',
                PANELNAVBUTTON: '.study-tabs .panel-group .button-tab-nav',
                PANELTITLE:     '.panel-title a',
                PANELS:         '.study-tabs .panel-group .panel-default',
                PANELACT:       '.study-tabs .panel-group .panel-title a[aria-expanded="true"]',
                TABCONTENT:     '.study-tabs .tab-content',
                TABNAVBUTTON:   '.tab-content .button-tab-nav',
                STUDYTABS:      '.study-tabs .nav-tabs li',
                STUDYTABACT:    '.study-tabs .nav-tabs li.active'
            }
        };

        $('[data-toggle="tooltip"]').tooltip({trigger: 'hover focus'});

        if ($('#myCarousel video').length) {
            if (originwidth > 990) {
                $('#myCarousel video').attr('autoplay', 'autoplay');
            } else {
                $('#myCarousel video').removeAttr('autoplay');
            }
        }
        if ($filtering) {
            $filtering.removeClass('hidden');
        }

        if ($indicators) {
            $indicators.removeClass('hidden');
        }

        if ($menu) {
            $menu.css('height', '0');
        }

        if ($filter) {
            var togglefilter = function (node) {
                node.toggleClass('in');
                if (node.hasClass('in')) {
                    $('.filter-elements').attr('aria-hidden', false);
                } else {
                    $('.filter-elements').attr('aria-hidden', true);
                }
            };
            togglefilter($filter);
            $(document).on('click', '.filter-text', function(e) {
                togglefilter($filter);
            });
        }

        var showavisos = function () {
            var next = ((current + 1) > numavisos - 1) ? 0 : current + 1;
            var prev = ((current - 1) < 0) ? numavisos - 1 : current - 1;

            if (current == 0) {
                $avisos.find('.prev').hide();
                $('.login-campus-body').addClass('first');
            } else {
                if (numavisos > 2) {
                    $avisos.find('.prev').show();
                }
                $('.login-campus-body').removeClass('first');
            }
            $avisos.find('.prev div').remove();
            $avisos.find('.next div').remove();
            $(nodesavisos[prev]).appendTo($avisos.find('.prev'));
            $(nodesavisos[next]).appendTo($avisos.find('.next'));
            $avisos.find('.prev').attr('aria-label', $(nodesavisos[prev]).text());
            $avisos.find('.next').attr('aria-label', $(nodesavisos[next]).text());
        };

        var faqsblockpanels = function (width) {
            if (!width) {
                width = $(window).width();
            }
            if (width > 990) {
                $faqs.find('.panel-collapse').addClass('in').css('height', 'auto').attr('aria-expanded','true');
                $faqs.find('.panel-heading a').removeClass('collapsed');
                $faqs.find('.panel-heading a').attr('data-toggle', '').attr('aria-expanded','true');
            } else {
                $faqs.find('.panel-collapse').removeClass('in').css('height', '0px').attr('aria-expanded','false');;
                $faqs.find('.panel-heading a').addClass('collapsed');
                $faqs.find('.panel-heading a').attr('data-toggle', 'collapse').attr('aria-expanded','false');
            }
        };

        if ($faqs) {
            faqsblockpanels(false);
        }

        /**
         * Function to navigate inside study-tabs
         * @param  mov 'click, true, false'
         * @param  tab which element is in use
         */
        var tabnavigation = function (mov, tab) {
            mov = typeof mov !== 'undefined' ? mov : 'click';
            tab = typeof tab !== 'undefined' ? tab : true;

            var total = $(PLUGINVARS.SELECTORS.STUDYTABS).length;
            var pos = $(PLUGINVARS.SELECTORS.STUDYTABACT).index();
            var $anchor;
            // Toggle buttons depends on tab position
            if (mov == 'click' && tab) {
                $(PLUGINVARS.SELECTORS.TABNAVBUTTON + '.next').show();
                $(PLUGINVARS.SELECTORS.TABNAVBUTTON + '.prev').show();
                if (pos == total - 1) {
                    $(PLUGINVARS.SELECTORS.TABNAVBUTTON + '.next').hide();
                } else {
                    if (pos == 0) {
                        $(PLUGINVARS.SELECTORS.TABNAVBUTTON + '.prev').hide();
                    }
                }
            } else {
                if (tab) {
                    // Move to next tab
                    if (mov) {
                        if (pos + 1 < total) {
                            $anchor = $(PLUGINVARS.SELECTORS.STUDYTABS).eq( pos + 1 ).find('a');
                            $anchor.click();
                        }
                    } else {
                        // Move to previous tab
                        if (pos - 1 >= 0) {
                            $anchor = $(PLUGINVARS.SELECTORS.STUDYTABS).eq( pos - 1 ).find('a');
                            $anchor.click();
                        }
                    }
                } else {
                    total = $(PLUGINVARS.SELECTORS.PANELS).length;
                    pos = $(PLUGINVARS.SELECTORS.PANELACT).closest(PLUGINVARS.SELECTORS.PANELS).index();
                    // Move to next panel
                    if (mov) {
                        if (pos + 1 < total) {
                            $anchor = $(PLUGINVARS.SELECTORS.PANELS).eq( pos ).find(PLUGINVARS.SELECTORS.PANELTITLE);
                            $anchor = $(PLUGINVARS.SELECTORS.PANELS).eq( pos + 1 ).find(PLUGINVARS.SELECTORS.PANELTITLE);
                            gotopanel($anchor.attr('href'));
                        }
                    } else {
                        // Move to previous panel
                        if (pos - 1 >= 0) {
                            $anchor = $(PLUGINVARS.SELECTORS.PANELS).eq( pos ).find(PLUGINVARS.SELECTORS.PANELTITLE);
                            $anchor = $(PLUGINVARS.SELECTORS.PANELS).eq( pos - 1 ).find(PLUGINVARS.SELECTORS.PANELTITLE);
                            gotopanel($anchor.attr('href'));
                        }
                    }
                }
            }
        };

        var createtabnavigationbuttons = function() {
            if ($(PLUGINVARS.SELECTORS.TABCONTENT) && $(PLUGINVARS.SELECTORS.TABNAVBUTTON).length == 0) {
                $(PLUGINVARS.SELECTORS.TABCONTENT).append( PLUGINVARS.HTML.PREVIOUS, PLUGINVARS.HTML.NEXT );
                var pos = $(PLUGINVARS.SELECTORS.STUDYTABACT).index();
                if (pos == 0) {
                    $(PLUGINVARS.SELECTORS.TABNAVBUTTON + '.prev').hide();
                }
                if (pos == $(PLUGINVARS.SELECTORS.STUDYTABS).length - 1) {
                    $(PLUGINVARS.SELECTORS.TABNAVBUTTON + '.next').hide();
                }
            }
        };

        createtabnavigationbuttons();

        $(PLUGINVARS.SELECTORS.PANELGROUP).ready(function(e) {
            var panelGroups = $( PLUGINVARS.SELECTORS.PANELCOLLAPSE );
            var total = panelGroups.length;
            if (total > 1) {
                $.each( panelGroups, function ( index, panelGroup ) {
                    $( panelGroup ).append( PLUGINVARS.HTML.BUTTONDIV );
                    if ( index > 0 && index < total - 1 ) {
                        $( panelGroup ).find(PLUGINVARS.SELECTORS.BUTTONDIV).append( PLUGINVARS.HTML.PREVIOUS, PLUGINVARS.HTML.NEXT );
                    } else {
                        if (index == 0) {
                            $( panelGroup ).find(PLUGINVARS.SELECTORS.BUTTONDIV).append( PLUGINVARS.HTML.NEXT );
                        } else {
                            $( panelGroup ).find(PLUGINVARS.SELECTORS.BUTTONDIV).append( PLUGINVARS.HTML.PREVIOUS );
                        }
                    }
                } );
            }
        });

        var iconstoselect = function () {
            var icons = $( '.filter-elements ul li' );
            var node = $('<select class="filter-select">');
            var aux;
            if (icons && !$('.filter-select').length) {
                $.each( icons, function ( index, icon ) {
                    aux = $('<option>')
                        .attr('value', $( icon ).data('meta-keyword') || 'all' )
                        .text( $( icon ).find('a').data('original-title') );
                    aux.appendTo( $( node ) );
                } );
                $(node).appendTo( icons.closest('.filter-dropdown') );
            }
        };

        iconstoselect();

        $(document).on('change', '.filter-select', function(e) {
            var keyword = $(this).val();
            if (keyword != 'all') {
                var $nodes = $('.substudies .nav.navbar-nav').addClass('filtering');
                var $selected = $nodes.find("li[data-meta-keyword='" + keyword + "']");
                $nodes.find('li').not($selected).hide(800, function(){
                    $selected.show(800);
                });
            } else {
                $('.substudies .nav.navbar-nav').removeClass('filtering').find('li').removeClass('third').show(800);
            }
        });

        $(document).on('click', PLUGINVARS.SELECTORS.TABNAVBUTTON, function(e) {
            tabnavigation($(this).hasClass('next'));
        });

        $(document).on('click', PLUGINVARS.SELECTORS.PANELNAVBUTTON, function(e) {
            tabnavigation($(this).hasClass('next'), false);
        });

        $(document).on('shown.bs.tab', PLUGINVARS.SELECTORS.STUDYTABS + ' a', function(e) {
            tabnavigation('click');
        });

        $(document).on('shown.bs.modal', '.modal', function(e) {
            $(this).find('[autofocus]').focus();
        });

        $(document).on('shown.bs.collapse', '.form-search', function(e) {
            if (e.currentTarget.id == 'search') {
                $(this).find('[type="search"]').focus();
            }
        });

        $(document).on('show.bs.collapse', '.ioc-menu', function(e) {
            $('header').addClass('bck-displayed');
        });

        $(document).on('hidden.bs.collapse', '.ioc-menu', function(e) {
            $('header').removeClass('bck-displayed');
        });

		$(document).on('click', '.ioc-menu .navbar-nav a[href*="#"]', function(e) {
			var $hamburger = $('.navbar-toggle');
			if ($hamburger.is(':visible') && $hamburger.hasClass('open')) {
				e.preventDefault();
				var href = this.getAttribute('href');
				var hash = href.split('#')[1];
				var target = $('#' + hash);
				$hamburger.trigger('click');
				setTimeout(function() {
					if (target.length) {
						$('html, body').animate({scrollTop: target.offset().top}, 500);
					}
				}, 400);
			}
		});

        $(document).on('shown.bs.collapse', '.ioc-menu', function(e) {
            $('.social, .ioc-languages').addClass('bck-displayed');
        });

        $(document).on('hide.bs.collapse', '.ioc-menu', function(e) {
            $('.social, .ioc-languages').removeClass('bck-displayed');
        });

        $(document).on('click', '.custom-icon.delete', function(e) {
            var $node = $(this).siblings('input');
            $node.val('');
            $node.focus();
        });

        $(document).on('click', '#form-error-campus', function(e) {
            $(this).hide();
            $('#sigin').removeClass('form-error');
            $('#username').focus();
        });

        $(document).on('show.bs.collapse', '.form-search', function(e) {
            $('header').addClass('big');
            $('.form-search .btn-search').addClass('big');
        });

        $(document).on('hide.bs.collapse', '.form-search', function(e) {
            $('header').removeClass('big');
        });

        $(document).on('hidden.bs.collapse', '.form-search', function(e) {
            $('.form-search .btn-search').removeClass('big');
        });

        $( window ).resize(function() {
            if ($(window).width() != originwidth) {
                originwidth = $(window).width();
                var $header = $('header');
                if ($faqs) {
                    faqsblockpanels($(this).width());
                }
                if ($('#myCarousel video').length) {
                    if (originwidth > 990) {
                        $('#myCarousel video').attr('autoplay', 'autoplay');
                    } else {
                        $('#myCarousel video').removeAttr('autoplay');
                    }
                }
                if ($(this).width() > 990 && $header.hasClass('bck-displayed')) {
                    $header.removeClass('bck-displayed');
                    $('.social, .ioc-languages').removeClass('bck-displayed');
                } else {
                    if ($menu.hasClass('in') && $(this).width() < 991 && !$header.hasClass('bck-displayed')) {
                        $header.addClass('bck-displayed');
                        $('.social, .ioc-languages').addClass('bck-displayed');
                    }
                    if ($(this).width() < 991 && $avisos.length > 0 && !$('#myCarousel').find('.carousel-indicators li').eq(0).hasClass('active')) {
                        $('#myCarousel').find('.carousel-indicators li').eq(0).click();
                    }
                }
            }
        });

        $.ajax({
            url: '/campus/local/loggedinas.php',
            success: function(responseText){
                if (responseText){
                    if (responseText.length > 20){
                        responseText = responseText.substr(0,20) + '...';
                    }
                    $(".login-text").text(responseText);
                    $(".login-campus").attr('data-target', '').on('click', function(e) {e.stopPropagation();e.preventDefault();window.location ='/campus/my';});
                    $(".login-clone-campus").attr('data-target', '').on('click', function(e) {e.stopPropagation();e.preventDefault();window.location ='/campus/my';});
                    $(".login-campus-mobile button").attr('title', responseText).attr('data-target', '').on('click', function(e) {e.stopPropagation();e.preventDefault();window.location ='/campus/my';});
                }
            }
        });

        $(document).on('show.bs.collapse', '#collapse-tabs .panel-collapse', function (e) {
            $(e.currentTarget).closest('.panel').siblings().find('.panel-collapse').collapse('hide');
        });

        $(document).on('show.bs.collapse', '#footer-collapse .panel-collapse', function (e) {
            $('#footer-collapse').find('.panel-collapse').not(e.currentTarget).collapse('hide');
        });

        $(document).on('click', 'a', function(e) {
            $('.back-to-top').attr('href', '#');
        });

        $(document).on('click', '.study-buttons a[href^=#], .study-tabs .tab-content a[href^=#], .subpage-group-buttons a[href^=#], .panel-body a[href^=#], .faqsindex a[href^=#]' , function(e) {
            e.preventDefault();
            linkhash = '#' + $(this).prop('hash').replace(/[^a-zA-Z0-9_-]*/g, '');
            isapanel = $(this).closest('.panel-group, .study-tabs').hasClass('panel-group');
            gotopanel(linkhash);
            if ($(this).closest('.study-tabs').length) {
                $('.back-to-top').attr('href', $('#tabs > li.active a').attr('href'));
            }
        });

        $(document).on('click', '#collapse-tabs .panel-title a[href^=#], #panel-sections .panel-title a[href^=#], .study-tabs .nav-tabs li a[href^=#], .panel-modal-resources a[href^=#], .faqsindex a[href^=#]', function(e) {
            e.preventDefault();
            isapanel = $(this).closest('.panel-group, .study-tabs').hasClass('panel-group');
            linkhash = $(this).prop('hash');
        });

        $(document).on('hidden.bs.collapse', '#collapse-tabs, #panel-sections, .study-tabs, .panel-modal-resources, .faqsindex', function(e) {
            if (history.pushState) {
                history.replaceState({}, '', window.location.href.replace(window.location.hash, ''));
            }
        });

        $(document).on('shown.bs.collapse', '#collapse-other-info, #collapse-tabs, #panel-sections, .panel-modal-resources, .faqsindex', function(e) {
            var position = $(linkhash).position();
            var adjust = 0;
            if (position) {
                adjust = (isapanel ? 105 : 170);
                $("html, body").animate({ scrollTop: position.top - adjust}, 800);
            }
            if (history.pushState) {
                history.replaceState({}, '', window.location.href.replace(window.location.hash, '') + linkhash);
            }
            linkhash = '';
            isapanel = false;
        });

        $(document).on('click', '#ioc-filter-data li', function(e) {
            e.preventDefault();
            var keyword = $(this).data("meta-keyword");
            $(this).siblings().removeClass('ioc-keyword-selected');
            $(this).toggleClass('ioc-keyword-selected');
            if ($(this).hasClass('ioc-keyword-selected') && keyword) {
                var $nodes = $('.substudies .nav.navbar-nav').addClass('filtering');
                var $selected = $nodes.find("li[data-meta-keyword='" + keyword + "']");
                $nodes.find('li').removeClass('third').not($selected).hide(800, function(){
                    $selected.show(800);
                });
                $.each($nodes, function( index, node) {
                    $selected = $(node).find("li[data-meta-keyword='" + keyword + "']");
                    if ($selected.size() > 2) {
                        $.each($selected, function( index, node) {
                            if ((index + 1) % 3 == 0 ) {
                                $(node).addClass('third');
                            }
                        });
                    }
                });
            } else {
                $('.substudies .nav.navbar-nav').removeClass('filtering').find('li').removeClass('third').show(800);
            }
        });

        $(document).on('click', '.nav.navbar-nav a', function(e) {
            if ($(this).attr('aria-disabled') == 'true') {
                e.preventDefault();
            }
        });

        var gotopanel = function (linkhash) {
            if (!$(linkhash).length) {
                return;
            }
            window.location.hash = linkhash;
            if (linkhash == '#matricula') {
                var position = $(linkhash).position();
                $("html, body").animate({ scrollTop: position.top}, 800);
            }
            if ($(linkhash).hasClass('tab-pane') && !$(linkhash).hasClass('active')) {
                $('.nav-tabs li a[href="' + linkhash +'"]').trigger('click');
            } else {
                if ($(linkhash).hasClass('panel-collapse') && !$(linkhash).hasClass('in')) {
                    $('.panel-group .panel-heading a[href="' + linkhash +'"]').trigger('click');
                }
            }
            var position = $(linkhash).position();
            var adjust = (isapanel ? 105 : 170);
            $("html, body").animate({ scrollTop: position.top - adjust}, 800);
        };

        if ( $(currenthash).length ) {
            gotopanel(currenthash);
        }

        if ($('.avisos')) {
            $('.avisos').addClass('display');
        }

        if ($avisos) {
            nodesavisos = $avisos.find('.next div').detach();
            numavisos = nodesavisos.length;
            showavisos();
        }

        $('#myCarousel').on('slid.bs.carousel', function () {
            if (slidingleft) {
                current = ((current - 1) < 0) ? numavisos - 1 : current - 1;
                slidingleft = false;
            } else if (slidingright) {
                current = ((current + 1) > numavisos - 1) ? 0 : current + 1;
                slidingright = false;
            }
            showavisos();
        });

        $(document).on('click', '.carousel-control.left, .avisos .prev', function(e) {
            slidingleft = true;
        });

        $(document).on('click', '.carousel-control.right, .avisos .next', function(e) {
            slidingright = true;
        });

        $(document).on('click', '.carousel-indicators li', function(e) {
            current = parseInt($(this).data('slide-to'), 10);
        });

        $(document).on('click', '#header .navbar-toggle', function(e) {
            $(this).toggleClass('open');
        });
        $(document).on('click', '.modal-dialog', function(e) {
            if (e.offsetY < 0) {
                $(this).closest('.modal').modal('hide');
            }
        });

        $(document).on('click', '.study-matricula .slide-container [data-target]', function(e) {
            e.preventDefault();
            var orig = $(this).closest('.slide-container');
            var dest = $(this).data('target');
            orig.removeClass('slide-active').addClass('slide-hidden');
            $('#' + dest).removeClass('slide-hidden').addClass('slide-active');
            var position = $('#matricula .slide-active').position();
            $("html, body").animate({ scrollTop: position.top - 70}, 800);
        });

        if(window.location.search.length > 0) {
            var matches = window.location.search.match(/errorcode\=(\d)/);
            var missatge = '';
            if (matches && matches.length > 1) {
                if(matches[1] == 1) {
                    missatge = Joomla.JText._('TPL_IOC_ERROR_CAMPUS_1');
                } else if(matches[1] == 2) {
                    missatge = Joomla.JText._('TPL_IOC_ERROR_CAMPUS_2');
                } else if(matches[1] == 3) {
                    missatge = Joomla.JText._('TPL_IOC_ERROR_CAMPUS_3');
                } else if(matches[1] == 4) {
                    missatge = Joomla.JText._('TPL_IOC_ERROR_CAMPUS_4');
                }
                if (missatge.length > 0) {
                    $('#form-error-campus').text(missatge);
                    $('#sigin').addClass('form-error');
                    $('button[data-target="#login-campus"]').click();
                    if(history.pushState) {
                        history.pushState({}, '', window.location.href.replace(window.location.search, ''));
                    }
                }
            }
        }

        // Google Analytics
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-36486089-1', 'auto');
        ga('send', 'pageview');
    });
})(jQuery);
