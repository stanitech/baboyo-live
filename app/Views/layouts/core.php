
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : null ?> - Baboyo - </title>
    <meta name="description" content="Football (soccer) statistics, team information, match predictions, bet tips, expert reviews, bet information and user predictions">
    <meta property="og:title" content="<?= isset($title) ? esc($title) : null ?> - Baboyo - ">
    <meta property="og:description" content="<?= isset($description) ? esc($description) : "Football (soccer) statistics, team information, match predictions, bet tips, expert reviews, bet information and user predictions" ?> ">
    <meta property="og:type" content="website">
    <meta property="og:image" itemprop="image" content="<?= isset($image) ? $image : base_url(env("app.logo")) ?>">
    <meta property="og:url" content="<?= current_url() ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/bootstrap-vue/bootstrap-vue.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="/assets/fonts/flaticon.css">
    <link rel="stylesheet" href="/assets/css/animate.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link rel="stylesheet" href="/assets/snackbar/snackbar.min.css" />
    <script src="/assets/jquery/jquery.min.js"></script>
    <script src="/assets/vue/vue.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/bootstrap-vue/bootstrap-vue.min.js"></script>
    <script src="/assets/ckeditor/ckeditor.js"></script>
    <script src="/assets/ckeditor/ckeditor-vue.min.js"></script>
    <script src="/assets/snackbar/snackbar.min.js"></script>
    <script src="/assets/js/js.cookie.min.js"></script>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://unpkg.com/vue-chartjs/dist/vue-chartjs.min.js"></script>

    <?php if (env('CI_ENVIRONMENT') !== 'development') : ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H92TS85HKW"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-H92TS85HKW');
    </script>
    
    <?php endif?>
    <style>
        .min-75vh{
            min-height:75vh;
        }
        .info-box{
            width:2rem;
            height:2rem;
            display:flex;
            align-items: center;
            justify-content: center;
            margin:1px;
            background: #e3e3e3;
            padding: 4px 0;
            cursor:pointer
        }
        .info-box:hover{
            opacity:.8;
        }

        .color-1{background-color:#ff5757}
        .color-2{background-color:#ff6e6e}
        .color-3{background-color:#ff8f8f}
        .color-4{background-color:#7878a3}
        .color-5{background-color:#5c91ff}
        .color-6{background-color:#82adff}
        .color-7{background-color:#ffad1f}
        .color-8{background-color:#ffc445}
        .color-9{background-color:#ffd65e}
        .color-10{background-color:#73ab73}
        .color-11{background-color:#8cb88c}
        
    </style>
    <script>
        Vue.use(CKEditor);
    </script>
</head>
<body>
<?= session()->getFlashData('notification')?>
    <?= $this->renderSection("layout") ?>
    <script src="/assets/js/main.js"></script>
</body>
</html>