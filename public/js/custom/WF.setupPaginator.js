(function(){
    window.WF = window.WF || {};
    WF.setupPaginator = (function(){
        return{
            setup   :   function(dom){
                var page = dom.children().text();
                var paginator = dom.parent();
                var currObj = paginator.find('.active');
                if(page=='prev') {
                    page=parseInt(currObj.text())-1;
                    if(parseInt(currObj.text())>1) {
                        currObj.parent().prev().children().attr('class','active');
                        currObj.attr('class','');
                    }
                }else if(page=='next'){
                    page=parseInt(currObj.text())+1;
                    if(currObj.parent().next().text() != 'next') {
                        currObj.parent().next().children().attr('class','active');
                        currObj.attr('class','');
                    }else{              
                        page = 0;
                    }
                }else {
                    currObj.attr('class','');
                    dom.children().attr('class','active');
                }
                if(paginator.find('.active').text() > 1) {
                    paginator.find('.prev').children().attr('href','javascript:{}');
                    paginator.find('.prev').children().attr('class','');
                }else {
                    paginator.find('.prev').children().removeAttr('href');
                    paginator.find('.prev').children().attr('class','disabled');
                }
                if(paginator.find('.active').parent().next().children().text() != 'next'){
                    paginator.find('.next').children().attr('href','javascript:{}');
                    paginator.find('.next').children().attr('class','');
                }else {
                    paginator.find('.next').children().removeAttr('href');
                    paginator.find('.next').children().attr('class','disabled');
                }
                return page;          
            }
        }
    })();
})();