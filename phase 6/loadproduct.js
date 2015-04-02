
	function loadproduct(a) {
$.ajax({
            url: "process.php?action=loadproduct&catid="+a+"&rnd="+new Date().getTime(), // random num to prevent IE caching
            type: "GET",
            async: false,
            dataType: "JSON",
            success: function(json){
                    if (!json){
                        // nothing?
                    }else{
                        for (var options = [], listItems = [],
					i = 0, product; product = json[i]; i++) {
				listItems.push('<li><div class="item"><div class="itemimg"><a href="prod.php?pid=',product.pid,'"><image src="incl/img/',product.pid,'.jpg"/>',
				'</div><p><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2F54.200.131.210%2Fprod.php%3Fpid%3D',product.pid,'&amp;width&amp;',
				'layout=standard&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=35" scrolling="no" frameborder="0" style="border:none;',
				'overflow:hidden; height:35px;" allowTransparency="true"></iframe>',product.name,'<br>price:',product.price,'/kg</a><br></p><span><image ' ,
				'src="images/addtocart.png" onclick="addToCart(',product.pid,');initShopList();" /></span></div></li>');
			}
			el('itemContainer').innerHTML = listItems.join('');
			  

 /* initiate plugin */
    // $("div.holder").jPages({
      // containerID: "itemContainer"
    // });

                    }
            }
    });
	}
	function getUrlParam(name)
{
var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
var r = window.location.search.substr(1).match(reg);  //匹配目标参数
if (r!=null) return unescape(r[2]); return null; //返回参数值
} 
			
	var a=getUrlParam("catid")	;
	loadproduct(a);
	
	