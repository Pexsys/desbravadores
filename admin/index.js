if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}
var origDocument;
function skinChanger() {
    $('.right-sidebar .demo-choose-skin li').on('click', function () {
        var $body = $('body');
        var $this = $(this);
        var existTheme = $('.right-sidebar .demo-choose-skin li.active').data('theme');
        $('.right-sidebar .demo-choose-skin li').removeClass('active');
        $body.removeClass('theme-' + existTheme);
        $this.addClass('active');
        $body.addClass('theme-' + $this.data('theme'));
        jsLIB.ajaxCall({
            url: jsLIB.rootDir+'rules/login.php',
            data: { MethodName : 'setTheme', data : { theme: $this.data('theme') } },
        });
    });
}
function setSkinListHeightAndScroll(isFirstTime) {
    var height = $(window).height() - ($('.navbar').innerHeight() + $('.right-sidebar .nav-tabs').outerHeight());
    var $el = $('.demo-choose-skin');
    if (!isFirstTime){
      $el.slimScroll({ destroy: true }).height('auto');
      $el.parent().find('.slimScrollBar, .slimScrollRail').remove();
    }
    $el.slimscroll({
        height: height + 'px',
        color: 'rgba(0,0,0,0.5)',
        size: '6px',
        alwaysVisible: false,
        borderRadius: '0',
        railBorderRadius: '0'
    });
}
function setSettingListHeightAndScroll(isFirstTime) {
    var height = $(window).height() - ($('.navbar').innerHeight() + $('.right-sidebar .nav-tabs').outerHeight());
    var $el = $('.right-sidebar .demo-settings');
    if (!isFirstTime){
      $el.slimScroll({ destroy: true }).height('auto');
      $el.parent().find('.slimScrollBar, .slimScrollRail').remove();
    }
    $el.slimscroll({
        height: height + 'px',
        color: 'rgba(0,0,0,0.5)',
        size: '6px',
        alwaysVisible: false,
        borderRadius: '0',
        railBorderRadius: '0'
    });
}
function activateNotificationAndTasksScroll() {
    $('.navbar-right .dropdown-menu .body .menu').slimscroll({
        height: '254px',
        color: 'rgba(0,0,0,0.5)',
        size: '4px',
        alwaysVisible: false,
        borderRadius: '0',
        railBorderRadius: '0'
    });
}
function getContent(content){
    jsLIB.ajaxCall({
        type: "GET",
        url: './view/screens/viewer.php',
        data: {MethodName:'getContent',data:{id:content}},
        success: function( data, jqxhr ) {
            document = origDocument;
            $("#main-content").html(data);
        }
    });
}


$(function () {
    var edge = 'Microsoft Edge';
    var ie10 = 'Internet Explorer 10';
    var ie11 = 'Internet Explorer 11';
    var opera = 'Opera';
    var firefox = 'Mozilla Firefox';
    var chrome = 'Google Chrome';
    var safari = 'Safari';
    var data = [], totalPoints = 110;

    $.AdminBSB = {};
    $.AdminBSB.options = {
        colors: {
            red: '#F44336',
            pink: '#E91E63',
            purple: '#9C27B0',
            deepPurple: '#673AB7',
            indigo: '#3F51B5',
            blue: '#2196F3',
            lightBlue: '#03A9F4',
            cyan: '#00BCD4',
            teal: '#009688',
            green: '#4CAF50',
            lightGreen: '#8BC34A',
            lime: '#CDDC39',
            yellow: '#ffe821',
            amber: '#FFC107',
            orange: '#FF9800',
            deepOrange: '#FF5722',
            brown: '#795548',
            grey: '#9E9E9E',
            blueGrey: '#607D8B',
            black: '#000000',
            white: '#ffffff'
        },
        leftSideBar: {
            scrollColor: 'rgba(0,0,0,0.5)',
            scrollWidth: '4px',
            scrollAlwaysVisible: false,
            scrollBorderRadius: '0',
            scrollRailBorderRadius: '0',
            scrollActiveItemWhenPageLoad: true,
            breakpointWidth: 1170
        },
        dropdownMenu: {
            effectIn: 'fadeIn',
            effectOut: 'fadeOut'
        }
    }
    $.AdminBSB.leftSideBar = {
        activate: function () {
            var _this = this;
            var $body = $('body');
            var $overlay = $('.overlay');
            $(window).click(function (e) {
                var $target = $(e.target);
                if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }
                if (!$target.hasClass('bars') && _this.isOpen() && $target.parents('#leftsidebar').length === 0) {
                    if (!$target.hasClass('js-right-sidebar')) $overlay.fadeOut();
                    $body.removeClass('overlay-open');
                }
            });
            $.each($('.menu-toggle.toggled'), function (i, val) {
                $(val).next().slideToggle(0);
            });
            $.each($('.menu .list li.active'), function (i, val) {
                var $activeAnchors = $(val).find('a:eq(0)');
                $activeAnchors.addClass('toggled');
                $activeAnchors.next().show();
            });
            $('.menu-toggle').on('click', function (e) {
                var $this = $(this);
                var $content = $this.next();
                if ($($this.parents('ul')[0]).hasClass('list')) {
                    var $not = $(e.target).hasClass('menu-toggle') ? e.target : $(e.target).parents('.menu-toggle');
                    $.each($('.menu-toggle.toggled').not($not).next(), function (i, val) {
                        if ($(val).is(':visible')) {
                            $(val).prev().toggleClass('toggled');
                            $(val).slideUp();
                        }
                    });
                }
                $this.toggleClass('toggled');
                $content.slideToggle(320);
            });
            _this.setMenuHeight();
            _this.checkStatuForResize(true);
            $(window).resize(function () {
                _this.setMenuHeight();
                _this.checkStatuForResize(false);
            });
            Waves.attach('.menu .list a', ['waves-block']);
            Waves.init();
        },
        setMenuHeight: function (isFirstTime) {
            if (typeof $.fn.slimScroll != 'undefined') {
                var configs = $.AdminBSB.options.leftSideBar;
                var height = ($(window).height() - ($('.legal').outerHeight() + $('.user-info').outerHeight() + $('.navbar').innerHeight()));
                var $el = $('.list');
                $el.slimscroll({
                    height: height + "px",
                    color: configs.scrollColor,
                    size: configs.scrollWidth,
                    alwaysVisible: configs.scrollAlwaysVisible,
                    borderRadius: configs.scrollBorderRadius,
                    railBorderRadius: configs.scrollRailBorderRadius
                });
                if ($.AdminBSB.options.leftSideBar.scrollActiveItemWhenPageLoad) {
                    var activeItemOffsetTop = $('.menu .list li.active')[0].offsetTop
                    if (activeItemOffsetTop > 150) $el.slimscroll({ scrollTo: activeItemOffsetTop + 'px' });
                }
            }
        },
        checkStatuForResize: function (firstTime) {
            var $body = $('body');
            var $openCloseBar = $('.navbar .navbar-header .bars');
            var width = $body.width();

            if (firstTime) {
                $body.find('.content, .sidebar').addClass('no-animate').delay(1000).queue(function () {
                    $(this).removeClass('no-animate').dequeue();
                });
            }
            if (width < $.AdminBSB.options.leftSideBar.breakpointWidth) {
                $body.addClass('ls-closed');
                $openCloseBar.fadeIn();
            }
            else {
                $body.removeClass('ls-closed');
                $openCloseBar.fadeOut();
            }
        },
        isOpen: function () {
            return $('body').hasClass('overlay-open');
        }
    };
    $.AdminBSB.rightSideBar = {
        activate: function () {
            var _this = this;
            var $sidebar = $('#rightsidebar');
            var $overlay = $('.overlay');
            $(window).click(function (e) {
                var $target = $(e.target);
                if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }

                if (!$target.hasClass('js-right-sidebar') && _this.isOpen() && $target.parents('#rightsidebar').length === 0) {
                    if (!$target.hasClass('bars')) $overlay.fadeOut();
                    $sidebar.removeClass('open');
                }
            });
            $('.js-right-sidebar').on('click', function () {
                $sidebar.toggleClass('open');
                if (_this.isOpen()) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
            });
        },
        isOpen: function () {
            return $('.right-sidebar').hasClass('open');
        }
    }
    $.AdminBSB.navbar = {
        activate: function () {
            var $body = $('body');
            var $overlay = $('.overlay');
            $('.bars').on('click', function () {
                $body.toggleClass('overlay-open');
                if ($body.hasClass('overlay-open')) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
            });
            $('.nav [data-close="true"]').on('click', function () {
                var isVisible = $('.navbar-toggle').is(':visible');
                var $navbarCollapse = $('.navbar-collapse');
                if (isVisible) {
                    $navbarCollapse.slideUp(function () {
                        $navbarCollapse.removeClass('in').removeAttr('style');
                    });
                }
            });
        }
    }
    $.AdminBSB.input = {
        activate: function () {
            //On focus event
            $('.form-control').focus(function () {
                $(this).parent().addClass('focused');
            });
            $('.form-control').focusout(function () {
                var $this = $(this);
                if ($this.parents('.form-group').hasClass('form-float')) {
                    if ($this.val() == '') { $this.parents('.form-line').removeClass('focused'); }
                }
                else {
                    $this.parents('.form-line').removeClass('focused');
                }
            });
            $('body').on('click', '.form-float .form-line .form-label', function () {
                $(this).parent().find('input').focus();
            });
            $('.form-control').each(function () {
                if ($(this).val() !== '') {
                    $(this).parents('.form-line').addClass('focused');
                }
            });
        }
    }
    $.AdminBSB.select = {
        activate: function () {
            if ($.fn.selectpicker) { $('select:not(.ms)').selectpicker(); }
        }
    }
    $.AdminBSB.dropdownMenu = {
        activate: function () {
            var _this = this;
            $('.dropdown, .dropup, .btn-group').on({
                "show.bs.dropdown": function () {
                    var dropdown = _this.dropdownEffect(this);
                    _this.dropdownEffectStart(dropdown, dropdown.effectIn);
                },
                "shown.bs.dropdown": function () {
                    var dropdown = _this.dropdownEffect(this);
                    if (dropdown.effectIn && dropdown.effectOut) {
                        _this.dropdownEffectEnd(dropdown, function () { });
                    }
                },
                "hide.bs.dropdown": function (e) {
                    var dropdown = _this.dropdownEffect(this);
                    if (dropdown.effectOut) {
                        e.preventDefault();
                        _this.dropdownEffectStart(dropdown, dropdown.effectOut);
                        _this.dropdownEffectEnd(dropdown, function () {
                            dropdown.dropdown.removeClass('open');
                        });
                    }
                }
            });
            Waves.attach('.dropdown-menu li a', ['waves-block']);
            Waves.init();
        },
        dropdownEffect: function (target) {
            var effectIn = $.AdminBSB.options.dropdownMenu.effectIn, effectOut = $.AdminBSB.options.dropdownMenu.effectOut;
            var dropdown = $(target), dropdownMenu = $('.dropdown-menu', target);
            if (dropdown.length > 0) {
                var udEffectIn = dropdown.data('effect-in');
                var udEffectOut = dropdown.data('effect-out');
                if (udEffectIn !== undefined) { effectIn = udEffectIn; }
                if (udEffectOut !== undefined) { effectOut = udEffectOut; }
            }
            return {
                target: target,
                dropdown: dropdown,
                dropdownMenu: dropdownMenu,
                effectIn: effectIn,
                effectOut: effectOut
            };
        },
        dropdownEffectStart: function (data, effectToStart) {
            if (effectToStart) {
                data.dropdown.addClass('dropdown-animating');
                data.dropdownMenu.addClass('animated dropdown-animated');
                data.dropdownMenu.addClass(effectToStart);
            }
        },
        dropdownEffectEnd: function (data, callback) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            data.dropdown.one(animationEnd, function () {
                data.dropdown.removeClass('dropdown-animating');
                data.dropdownMenu.removeClass('animated dropdown-animated');
                data.dropdownMenu.removeClass(data.effectIn);
                data.dropdownMenu.removeClass(data.effectOut);

                if (typeof callback == 'function') {
                    callback();
                }
            });
        }
    }
    $.AdminBSB.browser = {
        activate: function () {
            var _this = this;
            var className = _this.getClassName();
            if (className !== '') $('html').addClass(_this.getClassName());
        },
        getBrowser: function () {
            var userAgent = navigator.userAgent.toLowerCase();
            if (/edge/i.test(userAgent)) {
                return edge;
            } else if (/rv:11/i.test(userAgent)) {
                return ie11;
            } else if (/msie 10/i.test(userAgent)) {
                return ie10;
            } else if (/opr/i.test(userAgent)) {
                return opera;
            } else if (/chrome/i.test(userAgent)) {
                return chrome;
            } else if (/firefox/i.test(userAgent)) {
                return firefox;
            } else if (!!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/)) {
                return safari;
            }
            return undefined;
        },
        getClassName: function () {
            var browser = this.getBrowser();
            if (browser === edge) {
                return 'edge';
            } else if (browser === ie11) {
                return 'ie11';
            } else if (browser === ie10) {
                return 'ie10';
            } else if (browser === opera) {
                return 'opera';
            } else if (browser === chrome) {
                return 'chrome';
            } else if (browser === firefox) {
                return 'firefox';
            } else if (browser === safari) {
                return 'safari';
            } else {
                return '';
            }
        }
    }

    $.AdminBSB.browser.activate();
    $.AdminBSB.rightSideBar.activate();
    $.AdminBSB.navbar.activate();
    $.AdminBSB.dropdownMenu.activate();
    $.AdminBSB.input.activate();
    $.AdminBSB.select.activate();

    skinChanger();
    activateNotificationAndTasksScroll();
    setSkinListHeightAndScroll(true);
    setSettingListHeightAndScroll(true);

    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }
        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }

        setSkinListHeightAndScroll(false);
        setSettingListHeightAndScroll(false);
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

    origDocument = document;
    $(origDocument).ready(function(){
        $("#myBtnLogout").click(function(){
            jsLIB.ajaxCall({
                waiting : true,
                type: "GET",
                url: jsLIB.rootDir+'rules/login.php',
                data: { MethodName : 'logout' },
                success: function( data, jqxhr ) {
                    window.location.replace( jsLIB.rootDir+'index.php')
                }
            });
        });

        jsLIB.ajaxCall({
            type: "GET",
            url: jsLIB.rootDir+'rules/login.php',
            data: { MethodName : 'getMenu' },
            success: function( data, jqxhr ) {
                $("div.menu").html(data.menu);
                getContent(data.active);
                $.AdminBSB.leftSideBar.activate();

                $("[attr-menu]").on('click', function() {
                    getContent($(this).attr("attr-menu"));
                });

                setTimeout(function () { $('.page-loader-wrapper').fadeOut(); }, 50);
            }
        });
        //updateNotifications();
    });
});
