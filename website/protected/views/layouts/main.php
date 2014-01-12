<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
        <title><?php echo Yii::app()->name;?>
            <?php echo $this->pageTitle == EGPWebFrontendController::$DEFAULT_PAGE_TITLE ? "" : (" : " . $this->pageTitle)?>
        </title>
	<link type="text/css" rel="stylesheet" href="/media/css/style.css">
        <link type="text/css" rel="stylesheet" href="/media/css/validation.css">
        <script src="/scripts/jquery-1.10.2-dev.js"></script>
        <script src="/scripts/jquery.cookie.js"></script>
        <script src="/scripts/angular.js"></script>
        <script src="/scripts/sdk/skills.js"></script>
        <script src="/scripts/sdk/learzing.js"></script>
        <script src="/scripts/learzing-angular.js"></script>
</head>
<body>
    <div id="container">
	<div id="header">
		<div>
			<div class="logo">
                                <?php $imgHtml = CHtml::image('/media/images/logo.gif');
                                    echo CHtml::link($imgHtml, $this->createUrl("site/index"));?>
			</div>
			<ul id="navigation">
				<li>
                                    <?php echo CHtml::link("Home", $this->createUrl("site/index")); ?>
				</li>
                                <li>
                                    <?php /*echo CHtml::link("User",$this->createUrl("user/index"));*/ ?>
                                </li>
			</ul>
		</div>
	</div>

        <div id="contents">
            <?php echo $content; ?>
        </div>

	<div id="footer">
            <div class="clearfix">
                <div id="connect">
                    <a href="" target="_blank" class="facebook"></a>
                    <a href="" target="_blank" class="googleplus"></a>
                    <a href="" target="_blank" class="twitter"></a>
                    <a href="" target="_blank" class="tumbler"></a>
                </div>
                <!--Don't remove this website template site's information.
                    Term of template usage requires it.
                    This web site template was designed by http://www.freewebsitetemplates.com
                -->
                <p>
                    <?php echo Yii::app()->name;?> Inc :)
                </p>
            </div>
	</div>
    </div>
</body>
</html>