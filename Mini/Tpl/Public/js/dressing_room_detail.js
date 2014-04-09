/**
 * Created by jack on 14-4-8.
 */

$(function(){
    $(".select_city").each(function(){
        var s=$(this);
        var z=parseInt(s.css("z-index"));
        var dt=$(this).children("dt");
        var dd=$(this).children("dd");
        var _show=function(){dd.slideDown(200);dt.addClass("cur");s.css("z-index",z+1);};   //展开效果
        var _hide=function(){dd.slideUp(200);dt.removeClass("cur");s.css("z-index",z);};    //关闭效果
        dt.click(function(){dd.is(":hidden")?_show():_hide();});
        dd.find("a").click(function(){dt.html($(this).html());_hide();});     //选择效果（如需要传值，可自定义参数，在此处返回对应的"value"值 ）
        $("body").click(function(i){ !$(i.target).parents(".select_city").first().is(s) ? _hide():"";});
    })

    var miniMask = $('div.mini-mask');
    $("#shopinfo").on("click",function(){
        $("#mapdiv").show();
        miniMask.show();
        H.locateByCity(remote_ip_info);
    });

    $("#closemap").on("click",function(){
        $("#mapdiv").hide();
        miniMask.hide();
    });
    miniMask.on('click',function(){
        $("#mapdiv").hide();
    });


    $('#mini-activate-succ').click(function(){
        $('.mini-activate-succ').hide();
        miniMask.hide();
    })
    window.imgpath = imgpath;
    $.weather.init({
        imgpath : imgpath,
        city : cityn || null,
        callback: function(city, temper, info){
            var avg = getavg(temper.high,temper.low);
            $.weather.avg = avg;
            $.weather.getgurl = goodurl;
            // var pron,cookcity;
            //     cookcity = provi;
            // if(cookcity){
            // 	pron = provi;
            // }else{
            // 	pron = remote_ip_info.province;
            // }
            $.pron = provi ? provi : remote_ip_info.province;

            $.uniqlo.index.gender.find('a').first().click()
            // $.post(getgood,{tem:avg,pro:pron},function(data,status){
            // var da = eval("("+data+")");
            // if(da.ustr){
            //           $('#upc').html(da.ustr);
            // }else{
            //           $('#upc').html('');
            // }
            // if(da.dstr){
            //            $('#downc').html(da.dstr);
            // }else{
            //          $('#downc').html('');
            // }
            // $.uniqlo.kvSlider();
            // if(!da.ustr && !da.dstr){
            // $('#tishi').css('display','block');
            // }else{
            //           $('#tishi').css('display','none');
            // 	}

            // });
        }
    });

    $.uniqlo.index.week.on('click', 'li', function(){                  // 首页天气切换
        var that = $(this)
        $.uniqlo.index.togClass(that, 'mini-checked')
        $.weather.init({
            index : that.index() + 1,
            imgpath : window.imgpath,
            callback: function(city, temper, info){
                var avg = getavg(temper.high,temper.low);
                $.weather.avg = avg;
                $.post(goodurl,{
                    tem : avg,
                    cid : $.uniqlo.occasion,
                    sid : $.weather.sex,
                    tid : $.weather.set,
                    fid : $.uniqlo.fid,
                    zid : $.uniqlo.zid
                },function(data,status){
                    var da = eval("("+data+")");
                    if(da){
                        if(da.flag1=='p'){
                            if($.weather.set==1){
                                if(da.ustr){
                                    $('#upc').html(da.ustr);
                                }
                                if(da.dstr){
                                    $('#downc').html(da.dstr);
                                }
                            }else{
                                //如果是婴幼儿走这里
                                if($.weather.sex==4){
                                    if(da.fl==1){
                                        $('#upc').html(da.ustr);
                                        $('#downc').html(da.dstr);
                                    }else{
                                        $('#taoz').html(da.ustr);
                                    }
                                }else{
                                    $('#upc').html(da.ustr);
                                    $('#downc').html(da.dstr);
                                }
                            }
                        }
                        if(da.fl==1){
                            if($.weather.sex==4){
                                $('.index-single').removeClass('none');
                                $('.index-suit').addClass('none');
                                $('.index-suit').css('display','none');
                                $('.index-single').css('display','block');
                            }
                            $('#tishi').css('display','none');
                            $('#qtm').removeClass('none');
                        }else{
                            if($.weather.set==1){
                                $('.index-single').addClass('none');
                                $('.index-suit').removeClass('none');
                                $('.index-suit').css('display','block');
                                $('.index-single').css('display','none');
                            }else{
                                if($.weather.sex==4){
                                    $('.index-single').addClass('none');
                                    $('.index-suit').removeClass('none');
                                    $('.index-suit').css('display','block');
                                    $('.index-single').css('display','none');
                                }else{
                                    $('.index-single').removeClass('none');
                                    $('.index-suit').addClass('none');
                                    $('.index-suit').css('display','none');
                                    $('.index-single').css('display','block');
                                }
                            }
                            $('#tishi').css('display','none');
                            $('#qtm').addClass('none');
                        }
                        if(da.flag=='t'){
                            $('#taoz').html(da.tstr);
                        }else{
                            if($.weather.set==1){
                                $('#taoz').html('');
                            }
                        }
                        $.uniqlo.kvSlider();
                        if($.weather.set==1){
                            if(!da.ustr && !da.dstr && !da.tstr){
                                $('#tishi').css('display','block');
                            }else if(da.tstr){
                                $('#tishi').css('display','none');
                            }
                        }
                        /*else{
                         if(!da.ustr && !da.dstr){
                         $('#tishi').css('display','block');
                         }else{
                         $('#tishi').css('display','none');
                         }
                         }*/

                    }
                });
            }
        })
    })

    /* =============== close popup form =============== David */
    $(function(){
        var allpopform = {
            formregister: $('form.mini-register'),
            formlogin: $('form.mini-login'),
            formfetch: $('form.mini-fetch')
        }

        allpopform.formregister.on('click', 'a.mini-form-close', function(){
            allpopform.formregister.hide();
            miniMask.hide();
        })
        allpopform.formlogin.on('click', 'a.mini-form-close', function(){
            allpopform.formlogin.hide();
            miniMask.hide();
        })
        allpopform.formfetch.on('click', 'a.mini-form-close', function(){
            allpopform.formfetch.hide();
            miniMask.hide();
        })
    })
    /* =============== mini-aside =============== */

    !(function($){
        var aside = {
            msg : $('li.mini-aside-msg'),
            form: $('form.mini-aside-form'),
            formIsOpen: false,
            select: $('div.mini-select'),
            input: $('#mini-select'),
            options: $('ul.mini-options'),
            optionsIsOpen: false,
            succ : $('div.mini-aside-succ'),
            tips: $('div.mini-aside-tips')
        }

        aside.succ.on('click', 'button', function(){
            this.parentNode.style.display = 'none'
        })

        aside.msg.on('click', function(){
            aside.form.trigger(aside.formIsOpen? 'hidden' : 'shown')
        })

        aside.form.on('click', 'a.mini-form-close', function(){
            aside.form.trigger('hidden')
        }).on('click', function(e){
            e.stopPropagation()
        }).on('shown', function(){
            aside.form.show()
            // aside.msg.addClass('mini-msg-checked')
            aside.formIsOpen = true
            aside.select.text('请选择主题')
            aside.input.val('')
        }).on('hidden', function(){
            aside.form.hide()
            // aside.msg.removeClass('mini-msg-checked')
            aside.formIsOpen = false
        })

        aside.select.on('click', function(){
            aside.options.trigger(aside.optionsIsOpen? 'optionsHide' : 'optionsShow')
        })

        aside.options.on('optionsHide', function(){
            aside.options.hide()
            aside.optionsIsOpen = false
        }).on('optionsShow', function(){
            aside.options.show()
            aside.optionsIsOpen = true
        }).on('click', 'li', function(){
            var that = this
            aside.select.text(that.innerHTML)
            aside.input.val(that.id)
            aside.options.trigger('optionsHide')
        })
        //意见反馈

        aside.form.submit(function(event){
            event.preventDefault();
            var minivalue = aside.input.val();
            var convalue = $('#propid').val();
            if(minivalue<=0){
                aside.tips.html('请选择主题').show();
                return false;
            }
            convalue = stripHTML(convalue);
            if(convalue){
                $.post(addpropurl,{cate:minivalue,con:convalue},function(data,status){
                    if(data){
                        aside.succ.show();
                        aside.tips.hide();
                    }
                    aside.input.val('');
                    $('#propid').val('');
                });
            }else{
                aside.tips.html('请填写内容').show();
            }
        });

        aside.succ.on('click', function(){
            aside.succ.hide();
            aside.tips.hide();
            aside.form.hide();
            aside.formIsOpen = false;
        })
    }($))

    /* ============ login && logout ============ */



})
function getgoods(zid,fid,cid,sid,tid){
    $.post(goodurl,{
        tem : $.weather.avg,
        zid : zid,
        fid : fid,
        cid : cid,
        sid : sid,
        pro : $.pron,
        tid : tid
    },function(data,status){
        var da = eval("("+data+")");
        if(da){
            if(da.flag1=='p'){
                if(tid==1){
                    if(da.ustr){
                        $('#upc').html(da.ustr);
                    }
                    if(da.dstr){
                        $('#downc').html(da.dstr);
                    }
                }else{
                    //如果是婴幼儿走这里
                    if(sid==4){
                        if(da.fl==1){
                            $('#upc').html(da.ustr);
                            $('#downc').html(da.dstr);
                        }else{
                            $('#taoz').html(da.ustr);
                        }
                    }else{
                        $('#upc').html(da.ustr);
                        $('#downc').html(da.dstr);
                    }
                }
            }
            if(da.fl==1){
                if(sid==4){
                    $('.index-single').removeClass('none');
                    $('.index-suit').addClass('none');
                    $('.index-suit').css('display','none');
                    $('.index-single').css('display','block');
                }
                $('#tishi').css('display','none');
                $('#qtm').removeClass('none');
            }else{
                if(tid==1){
                    $('.index-single').addClass('none');
                    $('.index-suit').removeClass('none');
                    $('.index-suit').css('display','block');
                    $('.index-single').css('display','none');
                }else{
                    if(sid==4){
                        $('.index-single').addClass('none');
                        $('.index-suit').removeClass('none');
                        $('.index-suit').css('display','block');
                        $('.index-single').css('display','none');
                    }else{
                        $('.index-single').removeClass('none');
                        $('.index-suit').addClass('none');
                        $('.index-suit').css('display','none');
                        $('.index-single').css('display','block');
                    }
                }

                $('#tishi').css('display','none');
                $('#qtm').addClass('none');
            }
            if(da.flag=='t'){
                $('#taoz').html(da.tstr);
            }else{
                if(tid==1){
                    $('#taoz').html('');
                }
            }
            $.uniqlo.kvSlider();
            if(tid==1){
                if(!da.ustr && !da.dstr && !da.tstr){
                    $('#tishi').css('display','block');
                }else if(da.tstr){
                    $('#tishi').css('display','none');
                }
            }
            /*else{
             if(!da.ustr && !da.dstr){
             $('#tishi').css('display','block');
             }else{
             $('#tishi').css('display','none');
             }
             }*/
        }
    });
}
function delgo(id){
    if(id>0){
//删除cokkie里的数据
        var flagisud = '';
        $.post(isuturl,{id:id},function(data,status){
            if(data['code']==1){
                if(data['isud']==1){
                    flagisud = 'u';
                }else if(data['isud']==2){
                    flagisud = 'd';
                }
                var noiid = getCookie('nologiid'+flagisud);
                if(noiid){
                    var arrnoiid = noiid.split('_'),aalength = arrnoiid.length;
                    var str = '';
                    for(var u=0;u<aalength;u++){
                        if(id!=arrnoiid[u] && arrnoiid[u]){
                            str+=arrnoiid[u]+'_';
                        }
                    }
                    addCookie('nologiid'+flagisud,str);
                }
            }
        });
//删除cokkie里的数据
        $.post(gurl,{id:id},function(data,status){
        });
    }
}
$(window).on('beforeunload', function(){
    addCookie('nologiidu','',-1);
    addCookie('nologiidd','',-1);
})
function addbuy(id,flag,isdel){
    if(id>0){
        $.post(buyurl,{id:id,flag:flag,isdel:isdel},function(data,status){

        });
    }
}
//取出添加到大配件衣服的tag信息
function adddapei(id,po){
    if(id>0){
        $.post(addda,{id:id,po:po},function(data,status){
            $(po).siblings('.mini-cab-slide').find('ul').html(data);
            $.uniqlo.cabSlider();
        });
    }
}
function getavg(high,low){
    //修复气温求平均值没有转换为INT类型的bug
    var avg = Math.ceil((parseInt(low)+parseInt(high))/2);
    return avg;
}
function addwardrobe(id, onFailCallback, onSuccCallback){
    if(id>0){
        //没有登录的时候可以加三件
        if(!luid){
            var flagisud = '';
            $.post(isuturl,{id:id},function(data,status){
                if(data['code']==1){
                    if(data['isud']==1){
                        flagisud = 'u';
                    }else if(data['isud']==2){
                        flagisud = 'd';
                    }

                    var noiid = getCookie('nologiid'+flagisud);
                    if(noiid){
                        var arrnoiid = noiid.split('_'),aalength = arrnoiid.length;
                        if(aalength<=3){
                            var k = 0;
                            for(var i=0;i<aalength;i++){
                                if(arrnoiid[i]){
                                    if(arrnoiid[i]!=id){
                                        k = 1;
                                    }else{
                                        k = 0;
                                        onFailCallback();
                                        break;
                                    }
                                }
                            }
                            if(k==1){
                                addCookie('nologiid'+flagisud,noiid+id+'_',0);
                                onSuccCallback();
                            }
                        }else{
                            $.Register.showMask().register.show();
                        }
                    }else{
                        addCookie('nologiid'+flagisud,id+'_',0);
                        onSuccCallback();
                    }

                }else if(data['code']==-2){
                    onFailCallback();
                }
            });
            //没有登录的时候可以加三件
        }else{
            $.post(addwarurl,{id:id},function(data,status){
                if(data['code'] < 0){
                    //未登录状态收入衣柜
                    if(data['code'] == -1 ){
                        $.Register.showMask().register.show();
                    }else if(data['code'] == -2){
                        onFailCallback();
                    }else if(data['code'] == -3){
                        $('.mini-mask').show();
                        $('.mini-relate').show();
                    }//else if(data['code'] == -4){
                    //$('#relateMobile').val(data['msg']);
                    //$('.mini-mask').show();
                    //$('.mini-activate-notice').show();
                    //}
                }else{
                    onSuccCallback();
                }
            });
        }
    }
}

function addCookie(objName,objValue,objHours){
    var str = objName + "=" + escape(objValue);
    if(objHours > 0){
        var date = new Date();
        var ms = objHours*3600*1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString();
    }
    document.cookie = str;
}

function getCookie(objName){
    var arrStr = document.cookie.split("; ");
    for(var i = 0;i < arrStr.length;i ++){
        var temp = arrStr[i].split("=");
        if(temp[0] == objName) return unescape(temp[1]);
    }
}
function sendcity(pro,city){
    $.post(sendurl+'index.php/Sendcity/sendpro',{proname:pro,cityname:city},function(data,status){

    });
}

function stripHTML(msg)
{
    return msg=msg.replace(/<[^>]*>/g, "");
}

!(function($){
    /*
     $('#user_name').blur(function(){
     var user_name	= $('#user_name').val();
     if(user_name){
     $.post(ckeckuserurl,{user_name:user_name},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });
     }
     });

     $('#mobile').blur(function(){
     var mobile	= $('#mobile').val();
     if(mobile){
     $.post(ckeckmobileurl,{mobile:mobile},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });
     }
     });

     $('#f_mobile').blur(function(){
     var user_name	= $('#f_user_name').val();
     var mobile	= $('#f_mobile').val();
     if(user_name && mobile){
     $.post(ckeckusermobileurl,{user_name:user_name,mobile:mobile},function(data){
     if(data['code'] < 0){
     $('#f_error_msg').html(data['msg']);
     return false;
     }
     });
     }
     });
     */
    $(".mini-activate-fail").click(function(){
        $('.mini-activate-fail').hide();
        $('.mini-activate').show();
    })
    $(".mini-activate-succ").click(function(){
        window.location.reload();
    })
}($))

function getFuncCode(){
    var mobile		= $('#mobile').val();
    if(!mobile){
        $('#msg_error').html('请填写手机号码');
        return false;
    }else{
        var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(!mobile_reg.test(mobile)){
            $('#msg_error').html('手机号码格式错误');
            return false;
        }else{
            $.post(ckeckmobileurl,{mobile:mobile},function(data){
                if(data['code'] < 0){
                    $('#msg_error').html(data['msg']);
                    return false;
                }else{
                    $.post(activephone,{mobile:mobile},function(data){
                        if(data['code'] < 0){
                            $('#msg_error').html(data['msg']);
                            clearInterval($.uniqlo.activePhone);
                            return false;
                        }else{
                            $('#getTimeCode').css('display','inline');
                            $('#getCode').css('display','none');
                            $('#getNextCode').css('display','none');
                            var i=0;
                            $.uniqlo.activePhone = setInterval( function(){
                                i++;
                                if( 180 - i > 0 ){
                                    $('#time_show').html(180-i);
                                }else{
                                    $('#time_show').html(180);
                                }
                                if( i > 179 ){
                                    $('#getTimeCode').css('display','none');
                                    $('#getNextCode').css('display','inline-block');
                                    clearInterval($.uniqlo.activePhone);
                                }
                            }, 1000);
                        }
                    });
                }
            });
        }
    }
}


function getFuncCode2(){
    var mobile		= $('#userMobileCode').val();
    $.post(activephone,{mobile:mobile},function(data){
        if(data['code'] < 0){
            clearInterval($.uniqlo.activePhone2);
            return false;
        }else{
            $('#getTimeCode2').css('display','inline');
            $('#getCode2').css('display','none');
            $('#getNextCode2').css('display','none');
            var i=0;
            $.uniqlo.activePhone2 = setInterval( function(){
                i++;
                if( 180 - i > 0 ){
                    $('#time_show2').html(180-i);
                }else{
                    $('#time_show2').html(180);
                }
                if( i > 179 ){
                    $('#getTimeCode2').css('display','none');
                    $('#getNextCode2').css('display','inline-block');
                    clearInterval($.uniqlo.activePhone2);
                }
            }, 1000);
        }
    });
}


function activate_succ(){
    var verCode = $('#verCode').val();
    if(!verCode){
        //alert('验证码错误');return;
        $('.mini-activate-fail').show();
    }else{
        $.post(activesucc,{verCode:verCode},function(data){
            if(data['code'] > 0){
                $('.mini-activate-succ').show();
            }else{
                $('.mini-activate-fail').show();
            }
            $('.mini-activate').hide(); // 始终关闭激活层
        })
    }
}

function do_register(){
    var taobao_name	= $('#taobao_name').val();
    var password	= $('#password').val();
    var re_password = $('#re_password').val();
    var mobile		= $('#mobile').val();
    var c_phone = $('#checkbox-phone').val();
    var c_none = $('#checkbox-none').val();
    var verifying_code = $('#verifying_code').val();
    var isChecked = 1;
    /*
     if(!user_name){
     $('#msg_error').html('请填写用户名');
     return false;
     }else{
     var reg = /^[a-zA-Z0-9-_\u4e00-\u9fa5]{3,25}$/;
     if(!reg.test(user_name)){
     $('#msg_error').html('用户名格式错误');
     return false;
     }else{
     /*
     $.post(ckeckuserurl,{user_name:user_name},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });

     }
     }
     */
    if(!mobile){
        $('#msg_error').html('请填写手机号码');
        return false;
    }else{
        var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(!mobile_reg.test(mobile)){
            $('#msg_error').html('手机号码格式错误');
            return false;
        }
    }
    if(!password){
        $('#msg_error').html('请输入密码');
        return false;
    }else{
        if(password.length < 6 || password.length > 16){
            $('#msg_error').html('密码格式错误');
            return false;
        }
    }
    if(!re_password){
        $('#msg_error').html('请再次输入密码');
        return false;
    }else{
        if(password.length < 6 || password.length > 16){
            $('#msg_error').html('密码格式错误');
            return false;
        }
    }
    if(password != re_password){
        $('#msg_error').html('您两次输入的密码不一致');
        return false;
    }
    /*
     if($('#checkbox-phone').prop('checked')){
     if(!mobile){
     $('#msg_error').html('请填写手机号码');
     return false;
     }else{
     var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
     if(!mobile_reg.test(mobile)){
     $('#msg_error').html('手机号码格式错误');
     return false;
     }else{
     $.post(ckeckmobileurl,{mobile:mobile},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });
     }
     }
     }else{
     mobile = '';
     }
     */
    if($('#checkbox-phone').prop('checked')){
        if(!taobao_name){
            $('#msg_error').html('请填写淘宝登录名');
            return false;
        }else{
            if(taobao_name.length < 5 || taobao_name.length > 25){
                $('#msg_error').html('淘宝登录名格式错误');
                return false;
            }
        }
    }else{
        taobao_name = '';
    }
    if($('#js-checked').prop('checked')==false){
        if(!verifying_code){
            $('#msg_error').html('请填写验证码');
            return false;
        }
    }
    if($('#t_agree').prop('checked')==false){
        $('#msg_error').html('请勾选“已阅读隐私条款”');
        return false;
    }
    if($('#js-checked').prop('checked')){
        isChecked = 0;
    }
    $.post(ckeckmobileurl,{mobile:mobile},function(data){
        if(data['code'] < 0){
            $('#msg_error').html(data['msg']);
            return false;
        }else{
            if($('#checkbox-phone').prop('checked')){
                $.post(ckeckuserurl,{user_name:taobao_name},function(data){
                    if(data['code'] < 0){
                        $('#msg_error').html(data['msg']);
                        return false;
                    }else{
                        $.post(registerurl,{taobao_name:taobao_name,password:password,mobile:mobile,verifying_code:verifying_code,isChecked:isChecked,nologiidu:getCookie('nologiidu'),nologiidd:getCookie('nologiidd')},function(data){
                            if(data['code'] > 0){
                                $('.mini-reg-submit').addClass('disabled');
                                $('.mini-reg-submit').attr('disabled','disabled');
                                //$('.mini-register').hide();
                                //$('.mini-activate-notice').show();
                                window.location.reload();
                            }else{
                                $('#msg_error').html(data['msg']);
                                return false;
                            }
                        });
                    }
                });
            }else{
                $.post(registerurl,{taobao_name:taobao_name,password:password,mobile:mobile,verifying_code:verifying_code,isChecked:isChecked,nologiidu:getCookie('nologiidu'),nologiidd:getCookie('nologiidd')},function(data){
                    if(data['code'] > 0){
                        $('.mini-reg-submit').addClass('disabled');
                        $('.mini-reg-submit').attr('disabled','disabled');
                        //$('.mini-register').hide();
                        //$('.mini-activate-notice').show();
                        window.location.reload();
                    }else{
                        $('#msg_error').html(data['msg']);
                        return false;
                    }
                });
            }
        }
    });

}

function do_login(){
    var user_name	= $('#d_user_name').val();
    var password	= $('#d_password').val();
    if(!user_name || !password){
        $('#login_error_msg').html('请输入正确的用户名或密码');
        return false;
    }
    var is_remember_login = 0;
    if($('#remember_login').prop('checked')){
        is_remember_login = 1;
    }
    $.post(loginurl,{user_name:user_name,password:password,is_remember_login:is_remember_login,nologiidu:getCookie('nologiidu'),nologiidd:getCookie('nologiidd')},function(data){
        if(data['code'] > 0){
            //删除没有登录时添加的三件衣服
            addCookie('nologiidu','',-1);
            addCookie('nologiidd','',-1);
            window.location.reload()
        }else{
            $('#login_error_msg').html(data['msg']);
            return false;
        }
    });
}

function do_find_pwd(){
    // var user_name	= $('#f_user_name').val();
    var mobile	= $('#f_mobile').val();
    var verify	= $('#verify').val();
    // if(!user_name){
    // 	$('#f_error_msg').html('请输入正确的淘宝登录名或手机号码');
    // 	return false;
    // }
    if(!mobile){
        $('#f_error_msg').html('请输入正确的手机号码');
        return false;
    }
    /*
     if(!verify){
     $('#f_error_msg').html('请填写验证码');
     return false;
     }
     */
    $.post(findpwdurl,{mobile:mobile,verify:verify},function(data){
        if(data['code'] > 0){
            $('.mini-fetch-submit').addClass('disabled');
            $('.mini-fetch-submit').attr('disabled','disabled');
            $('.mini-fetch-succ').show();
        }else{
            $('#f_error_msg').html(data['msg']);
            return false;
        }
    });
}

function change_pwd(){
    var old_password = $('#c_old_password').val();
    var new_password = $('#c_new_password').val();
    var reg_new_password = $('#c_reg_new_password').val();
    if(old_password.length < 6 || old_password.length > 16){
        $('#pwd_error_msg').html('请输入正确的旧密码');
        return false;
    }
    if(!new_password){
        $('#pwd_error_msg').html('请填写新密码');
        return false;
    }else{
        if(new_password.length < 6 || new_password.length > 16){
            $('#pwd_error_msg').html('密码格式错误');
            return false;
        }else{
            if(old_password == new_password){
                $('#pwd_error_msg').html('新密码不能与旧密码相同');
                return false;
            }
        }
    }

    if(!reg_new_password){
        $('#pwd_error_msg').html('请再次输入密码');
        return false;
    }else{
        if(new_password != reg_new_password){
            $('#pwd_error_msg').html('您两次输入的密码不一致');
            return false;
        }
    }
    $.post(changepwdurl,{old_password:old_password,new_password:new_password},function(data){
        if(data['code'] > 0){
            //$('.mini-change-password').hide();
            $('.mini-change-succ').show();
            $('#pwd_error_msg').html('');
        }else{
            $('#pwd_error_msg').html(data['msg']);
            return false;
        }
    });
}

function do_active(){
    var a_user_name = $('#a_user_name').val();
    var a_mobile = $('#a_mobile').val();
    if(!a_mobile){
        $('#active_mobile_msg').html('请填写手机号码');
        return false;
    }else{
        var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(!mobile_reg.test(a_mobile)){
            $('#active_mobile_msg').html('手机号码格式错误');
            return false;
        }
    }

    if($('#activeAgree').prop('checked')==false){
        $('#active_mobile_msg').html('请勾选“已阅读隐私条款”');
        return false;
    }
    $.post(activeurl,{user_name:a_user_name,mobile:a_mobile},function(data){
        if(data['code'] > 0){
            $('.mini-relate').hide();
            $('.mini-relate-notice').show();
            $('#relateMobile').val(a_mobile);
        }else{
            $('#active_mobile_msg').html(data['msg']);
            return false;
        }
    });
}

function do_relate(){
    var relateMobile = $('#relateMobile').val();
    if(relateMobile){
        $.post(relateurl,{mobile:relateMobile},function(data){
            if(data['code'] > 0){
                $('.mini-activate-notice').hide();
                $('.mini-relate-notice').hide();
                $('.mini-activate-succ').show();
            }else{
                $('.mini-activate-notice').hide();
                $('.mini-relate-notice').hide();
                $('.mini-activate').show();
                var i=0;
                $.uniqlo.timerFind1 = setInterval( function(){
                    // 查询数据库 成功？  clearInterval(find)
                    $.post(relateurl,{mobile:relateMobile},function(data){
                        if(data['code'] > 0){
                            $('.mini-activate').hide();
                            $('.mini-activate-succ').show();
                            clearInterval($.uniqlo.timerFind1);
                        }else{
                            i++;
                            if( i > 10 ){
                                $('.mini-activate').hide();
                                $('.mini-activate-timeout').show();
                                clearInterval($.uniqlo.timerFind1);
                            }
                        }
                    });
                }, 3000);
                return false;
            }
        });
    }
}

function do_refresh_relate(){
    var relateMobile = $('#relateMobile').val();
    if(relateMobile){
        $('.mini-activate-timeout').hide();
        $('.mini-activate').show();
        var i=0;
        $.uniqlo.timerFind2 = setInterval( function(){
            // 查询数据库 成功？  clearInterval(find)
            $.post(relateurl,{mobile:relateMobile},function(data){
                if(data['code'] > 0){
                    $('.mini-activate').hide();
                    $('.mini-activate-succ').show();
                    clearInterval($.uniqlo.timerFind2);
                }else{
                    i++;
                    if( i > 10 ){
                        $('.mini-activate').hide();
                        $('.mini-activate-timeout').show();
                        clearInterval($.uniqlo.timerFind2);
                    }
                }
            });
        }, 3000);
    }
}

function fleshVerify(){
    //重载验证码
    var time = new Date().getTime();
    document.getElementById('verifyImg').src= '__APP__/Login/verify/'+time;
}


$(function(){
    $('.js-checked').change(function(){
        if ($('.js-checked:checked').length == 1) {
            $('.verifying_code').attr('disabled',true)
        }else{
            $('.verifying_code').attr('disabled',false)
        };
    });
    $('.js-click').click(function(e){
        e.preventDefault;
        var idname = $(this).attr('data-id');
        $(idname).show().siblings('.mini-pop').hide();
        $('.mini-mask').show()
    });


})