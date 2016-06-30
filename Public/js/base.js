var SKY = new Object();
jQuery(function($){
    $.Template = function(html,data){
        return doT.template(html).apply(null,[data]);
    }

    $.ShowBox = function(title, content, width){
        if(typeof width == "undefined"){
            width = 400;
        }

        var box;
        var options = {
            width:width,
            title:title,
            content:'<div style="padding:1em 0;">' + content + '</div>',
            closeButton:'title',
            animation:{open: 'slide:top',close:'flip'},
            onCloseComplete:function(){
                box.destroy();
            }
        };
        box = new jBox('Modal',options).open();
    }

    $.ShowAlert = function(msg){
        var box;
        var options = {
            width:300,
            title:'操作提示',
            content:'<div style="padding:1em 0; text-align:center;">' + msg + '</div>',
            closeButton:'title',
            animation:{open: 'slide:top',close:'flip'},
            onCloseComplete:function(){
                box.destroy();
            }
        };
        box = new jBox('Modal',options).open();
    }

    /**
     * 错误提示
     * @param string   msg    提示内容
     * @param int      width  提示框宽度,默认300
     * @param function func   关闭时回调方法
     */
    $.showError = function(msg,width,func){
        if(typeof width == "undefined"){
            width = 300;
        }

        var box;
        var options = {
            width:width,
            title:'错误提示',
            content:msg,
            closeButton:'title',
            animation:{open: 'slide:top',close:'flip'},
            onCloseComplete:function(){
                if(typeof func == "function"){
                    func.call(box);
                }
                box.destroy();
            }
        };
        box = new jBox('Modal',options).open();
    }

    /**
     * 成功提示
     * @param string   msg    提示内容
     * @param int      width  提示框宽度,默认300
     * @param function func   关闭时回调方法
     */
    $.showSuccess = function(msg,width,func){
        if(typeof width == "undefined" || 1 > width){
            width = 300;
        }

        var box;
        var options = {
            width:width,
            title:'成功提示',
            content:msg,
            closeButton:'title',
            animation:{open: 'slide:top',close:'flip'},
            onCloseComplete:function(){
                if(typeof func == "function"){
                    func.call(box);
                }
                box.destroy();
            }
        };
        box = new jBox('Modal',options).open();
    }

    /**
     * 成功提示
     * @param string   msg    提示内容
     * @param function funok  确认时回调方法
     * @param function funno  取消时回调方法
     * @param int      width  提示框宽度,默认300
     */
    $.ShowConfirm = function(msg,funok,funno,width,title){
        if(typeof width == "undefined"){
            width = 300;
        }
        if(typeof title == "undefined"){
            title = '操作提示';
        }

        var box;
        var options = {
            width:width,
            title:title,
            content:msg,
            attach:'test',
            closeButton:false,
            closeOnClick:false,
            confirmButton:'确认',
            cancelButton:'取消',
            onSubmit:function() {
                if(typeof funok == "function"){
                    funok.call(box);
                }
                box.destroy();
            },
            onCancel:function() {
                if(typeof funno == "function"){
                    funno.call(box);
                }
                box.destroy();
            },
            animation:{open: 'slide:top',close:'flip'},
        };
        box = new jBox('Confirm',options).open();
    }
});