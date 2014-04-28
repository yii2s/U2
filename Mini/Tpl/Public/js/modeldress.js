/**
 * Created by jack on 14-4-8.
 */

var pageElement = {
    BarcodeList : []
    ,IsHide : 0
    ,ChildrenType : 4
    ,Ischanged : 0
    ,$btnBuy : $('.buy_btn')
    ,$divBuys : $('.buy_btns')
    ,$btnExpansion : $('.syj_btn_expansion')    //右边浮动收缩模特按钮
    ,$divSyj : $('.syj')
    ,dressByBarcode:function(barcode,gender){
        Model.DressingByBarcode(barcode,gender);
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    }
    ,dressByBarcodeList:function(suitInfo){
        get_baiyi_dp(suitInfo[0],suitInfo[1]);
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    },
    clearAllSelected : function(){
        $('#watercontainer .pro-selected').removeClass('pro-selected');

        $('#watercontainer .select').removeClass('select');

        $('#watercontainer .product_color').hide();

        $('#watercontainer .product_gender a').removeData('select_barcode');
    },
    clearSelected :function(){
        var barcodeList = this.BarcodeList;

        $('.pro-selected').each(function(){
            if($.inArray($(this).data('barcode'),barcodeList) < 0){
                $(this).removeClass('pro-selected');
//                if($(this.data))
            }
        });

        $('.product_color').each(function(){
            if(!$(this).is(':hidden')){
                if($(this).find('.pro-selected').length == 0){
                    $(this).hide();
                    $(this).parent().find('.tryon').removeClass('select');
                    $(this).parent().find('.product_gender a').removeData('select_barcode');
                }
            }
        });

    }
};

(function($, window, document,undefined) {

    var ModelDress = function(){
//        this.ModelClothesList = []
    }

    ModelDress.prototype = {
        init : function(){
            this.showbaiyiModel();
            this.elementEvent();
            this.addClothesBySuitID();
        },
        showbaiyiModel : function(){
            /***新的百衣搭配间****/
            var touchid= 854;
            var key="8f1a6e3f182904ad22170f56c890e533";
            loadMymodel(touchid,key);
            Model.CurrClothesCallback = this.beu_getallclothes;
            $('.beubeu_btns').css('left','25px');
        },
        getUrlParam :function(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r!=null) return unescape(r[2]); return -1; //返回参数值
        },
        addClothesBySuitID : function(){
            var _this = this;
            var suitid = this.getUrlParam('suitid');
            if( suitid != -1){
                var gender = _this.getGenderValue(this.getUrlParam('gender'));
                get_baiyi_dp(suitid,gender);
                pageElement.$btnExpansion.click();

//                $.post(getCidItembnUrl,{suitid:suitid},function(data){
//                    var code = data.code;
//                    if( code == 1){
//                        var barcodeList = data.data;
//                        if( barcodeList != null){
//                        for(var i = 0;i<barcodeList.length;i++){
//                            Model.DressingByBarcode(barcodeList[i].barcode,gender);
//                        }
//                        pageElement.$btnExpansion.click();
//                        }
//                    }
//                });
            }
        },
        getGenderValue : function(gender){
            var genderValue = 15474;
            if(gender == 1){
                genderValue = 15474;
            }
            else if(gender == 2){
                genderValue = 15478;
            }
            else if(gender == 3){
                genderValue = 15583;
            }
            else if(gender == 4){
                genderValue = 15581;
            }
            else{
                genderValue = 15474;
            }
            return genderValue;
        },
        callDressingFunction : function(){
            var $img = $(this).find('img')
            pageElement.dressByBarcodeList($img.data('detail'));
        },
        //隐藏显示空间
        objShowOrHide : function(obj){
            if(obj.is(':hidden')){
                obj.show();
            }
            else{
                obj.hide();
            }
        },
        addBuyBtns:function(){
//            for(var barcode in  pageElement.BarcodeList ){
//                console.log(barcode);
//            }
        },
        //百衣试衣间回调函数
        beu_getallclothes : function(o){
            var _this = this;

            //清空购买列表
            var $buyUl =  pageElement.$divBuys.find('ul');
            $buyUl.html('');
            var barcode = '';
            var strLi = '';
            var title = '';
            pageElement.Ischanged = 1;
            pageElement.BarcodeList = [];
            if(o.length > 0){
                for(var i in o){
                    barcode = o[i].barcode;
                    title = o[i].name;
                    pageElement.BarcodeList.push(barcode);
                    strLi += '<li data-title="'+ title +'" ><a target="_blank" href="#" data-barcode="'+ barcode +'" title="'+ title+'"> '+ title+'</a></li>'
                }
                $buyUl.append(strLi);
                pageElement.clearSelected();

            }else{
                //如果当前模特身上没有衣服，则清空页面上所有点击后
                if(pageElement.IsHide == 0){
                    pageElement.clearAllSelected();
                }
                else{
                    pageElement.clearSelected();
                }
            }
        }
        ,
        dressing :function(barcode,gender,sex,isud,$wrapper_box,$colorLi){
            $('#beubeu_loadImg').show();
            $('#baby_fitting_room').hide();
            //如果性别为3：童装,则显示性别选择div
            if(sex == pageElement.ChildrenType ){
                $wrapper_box.find('.product_color').hide();
                var $product_gender = $wrapper_box.find('.product_gender');
                $product_gender.show();
                $product_gender.find('li a').each(function(){
                    $(this).removeClass('select');
                    if($(this).data('select_barcode') == barcode){
                        $(this).addClass('select');
                    }
                    $(this).data('barcode',barcode);
                })

            }else if(sex == 5){
                $('#beubeu_loadImg').hide();
                $('#baby_fitting_room').show();
                var imgUrl = 'http://uniqlo.bigodata.com.cn' + $colorLi.find('a').data('imgurl');
                //isud：1为上装2为下装3为配饰4为套装5为内衣6为婴幼儿
                //如果是上装则将图片显示到上部
                var title = $wrapper_box.find('h3 a').text();
                var purl = $wrapper_box.find('h3 a').attr('href');
                if(isud == 1){
                    $('#tops_bottoms').show();
                    $('#tops_bottoms img').attr('src',imgUrl).data({'prourl':purl,'title':title});

                }else if(isud == 2){   //如果是下装，则将图片显示到下部
                    $('#bottoms').show();
                    $('#bottoms img').attr('src',imgUrl).data({'prourl':purl,'title':title});
                }
                else{
                    $('#single').show();
                    $('#single img').attr('src',imgUrl).data({'prourl':purl,'title':title});
                }

                $colorLi.parent().find("li").removeClass("pro-selected");
                $colorLi.addClass('pro-selected');
                if(pageElement.$divSyj.is(':hidden')){
                    pageElement.$btnExpansion.click();
                }
            }
            else{
                pageElement.dressByBarcode(barcode,gender);
                $colorLi.parent().find("li").removeClass("pro-selected");
                $colorLi.addClass('pro-selected');
            }

        },
        getColorsHtml : function(colors){
            var colorHtml = '';
            var title = '';
            var imgurl = '';
            if(colors != null){
                for(var i = 0; i < colors.length; i++){
                    title =  colors[i].colorid + ' ' + colors[i].colorname;
                    imgurl =    '/' +  colors[i].colorcode.substring(0,colors[i].colorcode.lastIndexOf(".")) + ".jpg";
                    colorHtml += '<li data-barcode="'+ colors[i].uq + colors[i].colorid +'" title="'+title+'"><a href="javascript:;" data-colorid="'+ colors[i].colorid + '" ';
                    colorHtml += 'style="background:url('+ imgurl +') center no-repeat;background-size:cover;" ';
                    colorHtml += 'data-gender="'+ colors[i].gender+'" ';
                    colorHtml += 'data-uqcode="'+ colors[i].uq +'" ';
                    colorHtml += 'data-colorid="' + colors[i].colorid + '" ';
                    colorHtml += 'data-imgurl="'+ imgurl +'" ';
                    colorHtml += '</a><span>'+ title +'</span>';
                    colorHtml += '<i>已选中</i></li>';
                }
            }
            return colorHtml;
        },
        setBuyInfo : function(){
            pageElement.$divBuys.find('a').each(function(){
                var $this = $(this);
                //使用barcode去数据库中取回商品编号
                $.post(getJackNumiidUrl,{'item_bn':$this.data('barcode').substring(0,8)},function(data){
                    if( data.code == 1){
                        var clothesInfo = data.data
                        $this.attr('href',clothesInfo.detail_url);
                    }
                });
            });
            pageElement.Ischanged = 0;
        },
        elementEvent : function(){
            var _this = this;

            //点击模特图，将模特身上的衣服穿到白衣的模特身上
            $('#sfid').on('click','.model',_this.callDressingFunction);

            pageElement.$btnExpansion.on('click',function(){
                _this.objShowOrHide(pageElement.$divSyj);
                var $parentDiv = $(this).parent();
                if($parentDiv.hasClass('select')){
                    $parentDiv.removeClass('select');
                }
                else{
                    $parentDiv.addClass('select');
                }
            });


            pageElement.$btnBuy.on('click',function(){
                //如果当前选中的是婴儿，则将现在搭配间的衣服增加到购买列表
                if($('#beubeu_loadImg').is(':hidden')){
                    var $buyUl =  pageElement.$divBuys.find('ul');
                    $buyUl.html('');
                    var strLi = '';
                    if($('#single').is(':hidden')){
//                        strLi += '<li data-title="'+ $myImg.data('title') +'" ><a target="_blank" href="'+ $myImg.data('prourl') +'" title="'+  $myImg.data('title')+'"> '+  $myImg.data('title')+'</a></li>'
                        var $topImg = $('#tops_bottoms img');
                        strLi = '<li data-title="'+ $topImg.data('title') +'" ><a target="_blank" href="'+ $topImg.data('prourl') +'" title="'+  $topImg.data('title')+'"> '+  $topImg.data('title')+'</a></li>'
                        var $bottomsImg = $('#bottoms img');
                        strLi = '<li data-title="'+ $bottomsImg.data('title') +'" ><a target="_blank" href="'+ $bottomsImg.data('prourl') +'" title="'+  $bottomsImg.data('title')+'"> '+  $bottomsImg.data('title')+'</a></li>'
                    }else{
                        var $myImg =  $('#single img');
                        strLi = '<li data-title="'+ $myImg.data('title') +'" ><a target="_blank" href="'+ $myImg.data('prourl') +'" title="'+  $myImg.data('title')+'"> '+  $myImg.data('title')+'</a></li>'
                    }
                    $buyUl.append(strLi);
                }else{
                    if(pageElement.Ischanged == 1){
                        _this.setBuyInfo();
                    }
                }
                _this.objShowOrHide(pageElement.$divBuys);
            });

            pageElement.$divSyj.on('mouseleave',function(){
                pageElement.$divBuys.hide();
            });

            //点击衣服调用试穿按钮功能
            $('#watercontainer').on('click','.product_inf',function(){
               $(this).parent().find('.tryon').click();
            });
            //点击衣服调用试穿按钮功能
            $('#watercontainer').on('click','.product_img',function(){
                $(this).parent().parent().find('.tryon').click();
            });

            //鼠标移动到衣服上显示价格、库存等详细信息
            $('#watercontainer').on('mouseenter','.wrapper_box img',function(){
                $(this).parent().parent().find('.product_inf').show();
            });
            //鼠标移出隐藏价格、库存等详细信息
            $('#watercontainer').on('mouseleave','.wrapper_box',function(){
                var $this = $(this);
                $(this).find('.product_inf').hide();
                var $color_img = $this.find('.color-img');
                var $tryon =  $(this).find('.tryon');
                pageElement.IsHide = 0;
                $tryon.data('selected',0);
//                var selectBarcode = $children_gender.find('a').data('select_barcode');
                if( $color_img.find('.pro-selected').length == 0){
                    $this.find('.product_color').hide();
                    if($tryon.hasClass('select')){
                        $tryon.removeClass('select');
                    }
                }
            });

            //点击试穿按钮，显示颜色
            $('#watercontainer').on('click','.tryon',function(){
                pageElement.IsHide = 1;
                var $this = $(this);
                var $wrapper_box = $this.parent().parent().parent();
                var $colorImg = $wrapper_box.find('.color-img');
                $colorImg.html('').append(_this.getColorsHtml(eval($this.data('colors'))));
                $this.data('selected',1);
                $this.addClass('select');
                $wrapper_box.find('.product_color').show();
            });

            //点击色块将衣服添加到模特身上
            $('#watercontainer').on("click",".color-img li",function(){
                var $colorImg = $(this).parent();
                var $proInfo = $(this).find('a');
                var $wrapper_box =  $colorImg.parent().parent().parent();
                var barcode = $proInfo.data('uqcode') + $proInfo.data('colorid');
                var gender = $proInfo.data('gender');
                var isud = $proInfo.data('isud');
                var sex = $wrapper_box.find('.tryon').data('gendertype');
                _this.dressing(barcode,gender,sex,isud,$wrapper_box,$(this));
            });

            //点击男童、女童穿衣上身
            $('#watercontainer').on("click",".product_gender li",function(){
                var $wrapper_box = $(this).parent().parent().parent();
                var $a_gender = $(this).find('a');
                $(this).parent().find('li a').removeClass('select').removeData('select_barcode');
                $a_gender.addClass('select');
                var barcode = $a_gender.data("barcode");
                var gender = $a_gender.data("gender");
                pageElement.dressByBarcode(barcode,gender);
                $a_gender.data('select_barcode',barcode);
                $wrapper_box.find('.color-img li').each(function(){
                    if($(this).data('barcode') == barcode){
                        $wrapper_box.find(".color-img li").removeClass("pro-selected");
                        $(this).addClass('pro-selected');
                        return;
                    }
                });
            });
            //性格选择层中移出时隐藏该层
            $('#watercontainer').on("mouseleave",".product_gender",function(){
                var $this = $(this);
                var $product_color = $this.parent().find('.product_color');
                $this.hide();
                $product_color.show();
            });
        }
    }

    var modelDress = new ModelDress();
    modelDress.init();


})(jQuery, window, document);