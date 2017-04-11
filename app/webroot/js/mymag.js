var myPopup = {
    popupSrc: {
        "topics": "ajax.html",
        "icons": "icons_ajax.html"
    },
    mainClass: {
        "topics": "mfp-fade2",
        "icons": "mfp-fade"
    },
    cancelClose: function(e) {
        $('.del_ok').on('click', function(){
            console.log($(this).html());
            $.magnificPopup.close();
        });
    },
    handleClick: function(key, event) {
        $.magnificPopup.open({
            items: {
                src: this.popupSrc[key]
            },
            type: 'ajax',
            disableOn: 200,
            mainClass: this.mainClass[key],
            removalDelay: 200,
            preloader: false,
            fixedContentPos: false,
            callbacks: {
                ajaxContentAdded: function() {
                    event.cancelClick();
                    event.okClick();
                }
            }
            // You may add options here, they're exactly the same as for $.fn.magnificPopup call
            // Note that some settings that rely on click event (like disableOn or midClick) will not work here
        }, 0);
    }
};
