$(document).ready(function(){
    
    var hasError = $('main').find('.ERROR');
    var hasWhitelist = $('main').find('.WHITELIST');
    if(hasError.length > 0) {
        $('body').css({'background': '#ffd8d8'});
        $('main section').css({'border-top': '10px solid #cf1b1b'});
    } else if(hasWhitelist.length > 0) {
        $('body').css({'background': 'rgb(255, 218, 170)'});
        $('main section').css({'border-top': '10px solid #ffa200'});
        $('.scan_summary p:nth-child(3) span').css('color', 'green');
    } else {
        $('body').css({'background': 'rgb(207, 255, 239)'});
        $('main section').css({'border-top': '10px solid rgb(3, 159, 64)'});
        $('.scan_summary p:nth-child(3) span').css('color', 'green');
    }

    $('.add_to_whitelist').on('click', function(){
        var self = this;

        axios.post(`${window.location.href.replace('index.php', '')}functions/updateWhitelist.php`, {
            whitelist: '\n' + $(self).next().select().val(),
            headers: { 'Content-Type': 'application/json' },
          })
          .then((res) => {
            if(res.data == 'success') {
                $(self).html('Added');
                $(self).css({'background': '#459745'});
            } else if(res.data == 'not writable') {
                $(self).html('Permission Error');
                $(self).css({'background': '#db3636'});
            } else if(res.data == 'file is not opened') {
                $(self).html('File Missing');
                $(self).css({'background': '#db3636'});
            } else {
                // do nothing
            }
          }, (error) => {
            console.log('Error: ' + error);
          });
    });

    //arrange list
    var scanList = Array.from($('.scanCont'));
    scanList.forEach(list => {
        if($(list).hasClass('ERROR')) {
            $('main section').prepend($(list));
        } else if($(list).hasClass('ERROR')) {
            $('main section').prepend($(list));
        } else {
            // do nothing
        }
    });

    
});
