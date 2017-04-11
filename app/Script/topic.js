(function() {

  var $ = require("jquery");
  var mag = require('magnific-popup');

    var myPopup = {
        handleClick: function(key) {
            $.magnificPopup.open({
                items: {
                    src: "ajax.html"
                },
                type: 'ajax',
                disableOn: 200,
                mainClass: "mfp-fade2",
                removalDelay: 200,
                preloader: true,
                fixedContentPos: false
            }, 0);
        },
        cancelClick: function() {
            $.magnificPopup.close();
        },
        okClick: function() {
            fileSelect.delFile();
            $.magnificPopup.close();
        }
    };
    var fileSelect = {
        validation: function(pdfName) {
            if (!pdfName.match(/pdf|jpg|png/i)) {
                this.delFile();
                alert('ファイルフォーマットエラー');
                return false;
            }
            this.showFile(pdfName);
        },
        showFile: function(pdfName) {
            $('.file-name').eq(0).text(': ' + pdfName);
            $('#TopicPdfName').val(pdfName);
            $('#TopicPdfRemove').val('');
            $('.upload-image-wrap').eq(0).show();
        },
        delFile: function() {
            $('.file-name').eq(0).text('');
            $('#TopicPdfName').val('');
            $('#TopicPdfRemove').val('1');
            $('.upload-image-wrap').eq(0).hide();
        }

    };

    $('#TopicPdf').on('change', function(e){
        var pdfName = document.getElementById("TopicPdf").files[0].name;
        fileSelect.validation(pdfName);
    });
    $('#del-pdf').click(function(e){
        e.preventDefault();
        myPopup.handleClick('topics');
    });
    $(document).on('click', '.del_cancel', function(){
        myPopup.cancelClick();
    });
    $(document).on('click', '.del_ok', function(){
        myPopup.okClick();
    });
})();
