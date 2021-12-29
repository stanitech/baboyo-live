<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <!--<![endif]-->
    <!--[if (gte mso 9)|(IE)]>
        <xml>
          <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
          </o:OfficeDocumentSettings>
        </xml>
      <![endif]-->
    <!--[if (gte mso 9)|(IE)]>
        <style type="text/css">
          body {width: 600px;margin: 0 auto;}
          table {border-collapse: collapse;}
          table, td {mso-table-lspace: 0pt;mso-table-rspace: 0pt;}
          img {-ms-interpolation-mode: bicubic;}
        </style>
      <![endif]-->
    <title>[Baboyo]</title>
</head>

<body style="background-color:#fff;margin:0px;font-family:arial,helvetica">
  <div>
    <nav style="padding:1rem;background-color:#ccc">
      <a href="<?=base_url()?>"><img loading="lazy" src="<?=base_url(env("app.logo"))?>" alt="<?=env("app.name")?>" style="height:2rem"></a>
    </nav> 
    <main style="min-height:40vh;padding:1rem">
      <?= isset($mail_body) ? $mail_body : "" ?>
    </main>
    <aside style="padding:1rem">
      <p style="box-shadow:0px 0px 5px lightgrey;padding:1rem;border-left:5px solid orange">Need assistance? Our team here at <?=env('app.name')?> is ready to help with any questions you might have. Feel free to reach out by sending an email to <a href="mailto:<?=env("app.email")?>"><?=env("app.email")?></a> </p>
    </aside>
    <footer style="padding:1rem;box-shadow:0px 0px 2px lightgrey; text-align:center;line-height:1.3rem">
      <div>
        <small style="color: #a5a5a5e7; font-size: 0.8rem; margin-bottom: 0.5rem;">You are receiving this email because you are using the <?=env('app.name')?> App.</small><br>
          <small >Copyright <?=date("Y")?> <?=env('app.name')?>. All rights reserved.</small>
      </div>
    </footer>
    
  </div>
</body>

</html>