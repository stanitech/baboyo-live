<?= view_cell('\App\Libraries\ReusableComponents::newsTicker',null,60) ?>
<div class="header">
    <div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="left-area">
                        <ul>
                            <li>
                                <span class="icon">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                                <span class="text small">
                                    <span id="date"></span>
                                    <span id="month"></span>
                                    <span id="year"></span>
                                </span>
                            </li>
                            <li>
                                <span class="icon">
                                    <i class="far fa-clock"></i>
                                </span>
                                <span class="text clocks small">
                                    <span id="hours"></span>:<span id="minutes"></span>:<span id="seconds"></span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-4">
                    <div class="right-area">
                        <ul>
                            <?php if(is_logged_in()):?>
                            <li>
                                <div class="dropdown">
                                    <a class="link" href="" data-toggle="dropdown">My Account</button>
                                    <div class="dropdown-menu ">
                                        <a class="dropdown-item small" href="/account/your-info"><i class="fa fa-user-circle mr-2"></i> My Info</a>
                                        <a class="dropdown-item small" href="/logout" onclick=" confirm('Are you sure you want to logout?')"><i class="fa fa-sign-out-alt mr-2"></i> Logout</a>
                                    </div>
                                </div>
                            </li>
                            <?php else:?>
                            <li>
                                <form action="/login" method="POST" id="login_form">
                                    <input type="hidden" name="email" value="" id="user_google_email">
                                    <input type="hidden" name="name" value="" id="user_google_name">
                                    <input type="hidden" name="redirect" value="<?=current_url()?>">
                                    <button id="signin-with-google" class="link btn border btn-sm small border-white" type="button"><i class="fab fa-google"></i> Sign In</butto>
                                </form>
                            </li>
                            <?php endif?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="navbar" class="header-bottom">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3 d-xl-flex d-lg-flex d-block align-items-center">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-6 d-xl-block d-lg-block d-flex align-items-center">
                            <div class="logo">
                                <a href="/">
                                    <img src="<?=env('app.logo')?>" alt="logo">
                                </a>
                            </div>
                        </div>
                        <div class="col-6 d-xl-none d-lg-none d-block">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-9">
                    <div class="mainmenu">
                        <nav class="navbar navbar-expand-lg">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav ml-auto ">
                                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/experts">Experts</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/news/sports">News</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/rollover">Rollover</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/teams">Teams</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<script>
new Vue({
    el:".header",
    data:{
    },
    methods:{
        showSidebar(menu){
            this.current_menu = menu;
            this.show_sidebar = true
        }
    }
})
</script>

<?php

if(!is_logged_in()):?>
<script src="https://apis.google.com/js/api:client.js"></script>
<script src="https://apis.google.com/js/platform.js"></script>
<script>
  var googleUser = {};
  var startApp = function() {
    gapi.load('auth2', function(){
      auth2 = gapi.auth2.init({
        client_id: '<?=env("app.apis.google-client-id")?>',
        cookiepolicy: '<?=base_url()?>',
      });
      auth2.attachClickHandler(document.querySelector('#signin-with-google'), {},
        function(googleUser) {
            document.querySelector("#user_google_email").value = googleUser.getBasicProfile().getEmail()
            document.querySelector("#user_google_name").value = googleUser.getBasicProfile().getName()
            document.querySelector("#login_form").submit();
        }, function(error) {
          alert(JSON.stringify(error, undefined, 2));
        });
    });
  };
  startApp();
</script>
<?php endif?>