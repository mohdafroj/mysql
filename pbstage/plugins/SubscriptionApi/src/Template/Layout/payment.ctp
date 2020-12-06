<?=$this->Html->docType()?>
<html>
    <head>
        <?=$this->Html->charset()?>
        <?=$this->Html->meta('favicon.ico', '/subscription_api/favicon.ico', ['type'=>'icon'])?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo PC['COMPANY']['tag']?>: <?php echo $title ?? PC['COMPANY']['title']; ?></title>
        <?php echo $this->Html->css('SubscriptionApi.bootstrap.min'); ?>
        <?php echo $this->Html->css('SubscriptionApi.fontawesome.min'); ?>
        <link href="https://fonts.googleapis.com/css?family=Oxygen:300,400,700&display=swap" rel="stylesheet">
        <?php echo $this->Html->css('SubscriptionApi.custom'); ?>
        <?php echo $this->Html->css('SubscriptionApi.payment-gateway'); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	</head>
    <body>
        <div id="topHeader" class="sr-only"></div>
        <div class="wrapper section_gradient pattern_image"><!-- start of wrapper -->
            <header class="header-main"><!-- start of header -->
            
                <nav class="navbar navbar-expand-md navbar-light nav-top"><!-- start of top_navigation -->
                    <div class="container p-0"><!-- start of container -->
                        <a class="navbar-brand" href="<?php echo PC['COMPANY']['website']; ?>"><?php echo $this->Html->image('SubscriptionApi.logo.svg', ['alt'=>PC['COMPANY']['tag'], 'class'=>'img-fluid']); ?></a>
                    </div><!-- end of container -->
                </nav><!-- end of top_navigation -->
                
            </header><!-- end of header -->
            <div class="content gatewayContent"><!-- start of middle_page -->
                <section class="container-fluid"><!-- start of section_box -->
                    <div class="container">
                        <?=$this->fetch('content')?>
                    </div>
                </section><!-- end of section_box -->
    		</div><!-- end of middle_page -->
            <footer class="footer gatewayFooter"><!--start of footer -->
                <div class="container-fluid footer_bottom"><!-- start of bottom_footer -->
                    <div class="col-12 disclaimer_text"><!-- start of disclaimer_line -->
                    <p>
                    Shop 100% authentic products with confidence. All transactions on <?php echo PC['COMPANY']['tag']; ?> are secured by SSL and secured payment gateway.
                    </p>
                    <p><?php echo PC['COMPANY']['tag'];?> © 2016-2017, All rights reserved.</p>
                    </div><!-- end of disclaimer_line -->
                </div><!-- end of bottom_footer -->
            </footer><!--end of footer -->
        </div>
    </body>
</html>
