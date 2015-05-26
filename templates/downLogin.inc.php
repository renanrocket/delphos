<?php
    include "inc/copy.inc";
    include "chat/chat.php";
    echo $codImg;
?>
	</center>

        
        
        <!--<script src="http://code.jquery.com/jquery-1.8.2.min.js" type="text/javascript"></script>-->
        <!--
        <link href="plugins/Pusher-chat---jQuery-plugin-master/css/chat-style.css" rel="stylesheet">
        <script src="http://js.pusher.com/1.12/pusher.min.js" type="text/javascript"></script> 
        <script src="plugins/Pusher-chat---jQuery-plugin-master/js/jquery.pusherchat.js" type="text/javascript"></script>
        <script src="plugins/Pusher-chat---jQuery-plugin-master/js/jquery.playSound.js" type="text/javascript"></script>


        <!--***************************************************** pusher chat html *******************************************************-->
        <!--
        <div id="pusherChat">
            <div id="membersContent">                
                <span id="expand"><span class="close">&#x25BC;</span><span class="open">&#x25B2;</span></span>
                <h2><span id="count">0</span>online</h2>
                <div class="scroll">
                    <div id="members-list"></div>
                </div>
            </div>

            <!-- chat box template -->
            <!--
            <div id="templateChatBox">
                <div class="pusherChatBox">
                    <span class="state">
                        <span class="pencil">
                            <img src="plugins/Pusher-chat---jQuery-plugin-master/assets/pencil.gif" />
                        </span>
                        <span class="quote">
                            <img src="plugins/Pusher-chat---jQuery-plugin-master/assets/quote.gif" />
                        </span>
                    </span>
                    <span class="expand"><span class="close">&#x25BC;</span><span class="open">&#x25B2;</span></span>
                    <span class="closeBox">x</span>
                    <h2><a href="#" title="Ir para o perfil"><img src="" class="imgFriend" /></a> <span class="userName"></span></h2>
                    <div class="slider">
                        <div class="logMsg">
                            <div class="msgTxt">
                            </div>
                        </div>
                        <form method="post" name="#123">
                            <textarea  name="msg" rows="3" ></textarea>
                            <input type="hidden" name="from" class="from" />
                            <input type="hidden" name="to"  class="to"/>
                            <input type="hidden" name="typing"  class="typing" value="false"/>
                        </form>
                    </div>
                </div>
            </div>
            <!-- chat box template end -->
            <!--
            <div class="chatBoxWrap">
                <div class="chatBoxslide"></div>
                <span id="slideLeft"> <img src="plugins/Pusher-chat---jQuery-plugin-master/assets/quote.gif" />&#x25C0;</span> 
                <span id="slideRight">&#x25B6; <img src="plugins/Pusher-chat---jQuery-plugin-master/assets/quote.gif" /></span>
            </div>
        </div>

         <script type="text/javascript">
            /*
             * this part is only for demo you don't need this
             */
            function getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                    vars[key] = value;
                });
                return vars;
            }


            //var id = '999';
            //var name = 'God';
            
            //var id = getUrlVars()['user_id'];
            //var name = getUrlVars()['name'];
            
            var id = '<?php echo $_COOKIE["id_empresa"]."_".getIdCookieLogin($_COOKIE["login"]); ?>';
            var name = '<?php echo $_COOKIE["login"]; ?>';
            

            if (id=="undefined") {
                id=""; 
            } else $('#user_'+id).hide();
            if (name=="undefined") name="";
            if (!id) $('#pusherChat').remove();
            if (name)
                $('.connexion').html('Você está conectado como '+name.replace('%20',' '));
            /*
             * this part is only for demo you don't need this
             */
        </script>

        <script>
            $.fn.pusherChat({
                'pusherKey':'75ce44c5dbbb32800128',
                'authPath': 'plugins/Pusher-chat---jQuery-plugin-master/server/pusher_auth.php?user_id='+id+'&name='+name,
                'friendsList' : 'plugins/Pusher-chat---jQuery-plugin-master/ajax/friends-list.php',
                'serverPath' : 'plugins/Pusher-chat---jQuery-plugin-master/server/server.php',
                'profilePage':true,
                'onFriendConnect': function(member){
                    if (member.id) $('#user_'+member.id).hide();  
                    if (!$('.account a:visible').html()) $('.hide').show();
                },
                'onFriendLogOut': function(member){
                    if (member.id) $('#user_'+member.id).show();  
                    if ($('.account a:visible').html()) $('.hide').hide();
                },
                'onSubscription':function(members){
                    if ($('.account a:visible').html()) $('.hide').hide();
                    $.each(members._members_map, function(val){
                        $('#user_'+val).hide();
                    });            
                }
            });
        </script>
        <!--***************************************************** end pusher chat html *******************************************************-->
		


<?php
    mysqli_close($conexao);
    unset($_POST);
    unset($_GET);
?>
	</body>
</html>
<script type="text/javascript">
	//evitar que o usuario saia antes de carregar totalmente a pagina
	sairPagina.confirm = false;
</script>
