<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>404 Page Not Found</title>
	<meta name="description" content="Football (soccer) statistics, team information, match predictions, bet tips, expert reviews, bet information and user predictions">
	<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body>
    <!-- breadcrumb begin -->
    <div class="breadcrumb-bettix" style="background:url(<?= env("app.image.experts-banner") ?>);background-position:center center;background-size:100%">
        <div class="container">
            <div class="row">
                <div class="col-xl-7 col-lg-7">
                    <div class="breadcrumb-content py-5">
                        <h2>Error 404</h2>
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li>Error 404</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- error begin -->
    <div class="error">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-6">
                    <div class="part-img">
                        <img src="/assets/img/error.png" alt="">
                    </div>
                    <div class="part-text">
                        <h4>
                            <?php if (! empty($message) && $message !== '(null)') : ?>
                                <?= nl2br(esc($message)) ?>
                            <?php else : ?>
                                Sorry! Cannot seem to find the page you were looking for.
                            <?php endif ?>
                        </h4>
                        <!-- <h4>Sorry, This page was not found!</h4> -->
                        <a href="/" class="back-to-home-btn">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- error end -->
</body>
</html>