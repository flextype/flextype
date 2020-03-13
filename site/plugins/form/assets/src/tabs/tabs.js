$(".tabs__nav").on("click", ".tabs__nav__link:not(.tabs__nav__link--active)", function () {
    $(this).addClass("tabs__nav__link--active").siblings().removeClass("tabs__nav__link--active");
    $("div.tabs__content").removeClass("tabs__content--active").eq($(this).index()).addClass("tabs__content--active");
});
