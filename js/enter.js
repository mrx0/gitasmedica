	function tryAuth () {

        var login = document.getElementById("login").value;
        var password = document.getElementById("password").value;
        //console.log(document.getElementById("office"));

        if (document.getElementById("office") == null){
            office = -1;
        }else{
            var office = document.getElementById("office").value;
        }

        var errror = document.getElementById("errror");
        errror.innerHTML = '';
        var ch_office = document.getElementById("ch_office");
        ch_office.innerHTML = '';

        $.ajax({
            url:"auth.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    login: login,
                    password: password,
                    office: office,

                },
            cache: false,
            beforeSend: function() {
            },
            // действие, при ответе с сервера
            success: function(res){

                if(res.result == "success"){
                    errror.innerHTML = '<span class="query_ok">'+res.data+'</span>';
                    setTimeout(function () {
                        window.location.href = "index.php";
                    }, 1000);
                }else{
                    if(res.result == "office"){
                        //alert(6544);
                        ch_office.innerHTML = ''+res.data+'';
                    }else{
                        errror.innerHTML = '<span class="query_neok">'+res.data+'</span>';
                    }
                }

            }
        });
    }

	document.addEventListener("DOMContentLoaded", function(event) {

        document.onkeyup = function (e) {
            e = e || window.event;
            if (e.keyCode === 13) {
                //alert("Вы нажали Enter!");

                tryAuth ();
            }
            // Отменяем действие браузера
            return false;
        }

		var sbmtfrm = document.getElementById("sbmtfrm");
		sbmtfrm.onclick = function(){
			//alert(1245);
            tryAuth ();
		}
	});
		