function WaveCMS() {
    var this_ = this;

    this.onReady = function () {
        this_.sidebarNav();
        this_.magnificPopup();
        this_.activeTab();
        this_.errorTab();
    };

    this.onLoad = function () {
        this_.sidebarHeight();
        this_.publishAjax();
    };


    this.onResize = function () {
        this_.sidebarHeight();
    };

    this.sidebarNav = function () {
        $('.sidebar .nav li a').on('click', function () {

            if ($(this).next('.nav-submenu').length) {
                $(this).next('.nav-submenu').slideToggle();
                $(this).parent().toggleClass('opened');
            }

            $(this).blur();
        });
    };

    this.sidebarHeight = function () {
        // var height = $(window).height() - $('.header').outerHeight();
        var height = $(window).height();

        $('.main').css('min-height', height + "px");
    };

    this.publishAjax = function () {

        $(document).on('click', '.grid-view .btn-publish', function (event) {
            event.preventDefault();
            var $btn = $(this);


            $.get($(this).attr('href'), function (data) {
                if (data.publish) {
                    $btn.removeClass('btn-default').addClass('btn-success');
                } else {
                    $btn.removeClass('btn-success').addClass('btn-default');
                }
                $btn.blur();
            }).fail(function (data) {
                alert(data.responseText);
            });
        });
    };

    this.magnificPopup = function () {
        $('.magnific-outer').magnificPopup({
            delegate: '.popup',
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1]
            }
        });
    };

    this.activeTab = function () {
        if ($('.wavecms-tabs').length) {

            var module_controller = $('.wavecms-tabs').attr('data-module-controller');
            var activeTab = localStorage.getItem(module_controller);

            if (activeTab) {
                if ($('a[href="' + activeTab + '"]').length) {
                    $('a[href="' + activeTab + '"]').tab('show');
                } else {
                    $('.wavecms-tabs li').first().children('a').tab('show');
                }
            } else {

                $('.wavecms-tabs li').first().children('a').tab('show');
            }


            $('body').on('click', 'a[data-toggle=\'tab\']', function (e) {
                e.preventDefault()
                var tab_name = this.getAttribute('href')
                localStorage.setItem(module_controller, tab_name)

                $(this).tab('show');
                return false;
            });
        }
    };

    this.errorTab = function () {
        $('.wavecms-form').on('afterValidate', function () {
            if ($('.wavecms-tab-content .tab-pane').length) {
                var i = 0;
                $('.wavecms-tab-content .tab-pane').each(function () {
                    $('.wavecms-tabs li').eq(i).removeClass('has-error');

                    if ($(this).find('.form-group.has-error').length) {
                        $('.wavecms-tabs li').eq(i).addClass('has-error');
                    }
                    i++;
                });
            }
        });
    };

}

WaveCMS = new WaveCMS();

$(document).on('ready', function () {
    WaveCMS.onReady();
});

$(window).on('load', function () {
    WaveCMS.onLoad();
});

$(window).on('resize', function () {
    WaveCMS.onResize();
});
