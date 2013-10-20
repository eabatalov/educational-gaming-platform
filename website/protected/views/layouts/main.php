<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>Studify
            <?php echo $this->pageTitle == EGPControllerBase::$DEFAULT_PAGE_TITLE ? "" : (" : " . $this->pageTitle)?>
        </title>
	<link rel="stylesheet" href="/media/css/style.css" type="text/css">
</head>
<body>
	<div id="header">
		<div>
			<div class="logo">
                                <?php echo CHtml::link("Studify",$this->createUrl("site/index")); ?>
                                <p>
                                    Educational gaming platform
                                </p>
			</div>
			<ul id="navigation">
				<li>
                                    <?php echo CHtml::link("Home",$this->createUrl("site/index")); ?>
				</li>
                                <li>
                                    <?php echo CHtml::link("User",$this->createUrl("user/index")); ?>
                                </li>
			</ul>
		</div>
	</div>
        <!-- FIXME: some html div mess-->
        <div id="contents">
            <div id="adbox">
                <div class="clearfix">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
	<div id="footer">
		<div class="clearfix">
			<div id="connect">
				<a href="" target="_blank" class="facebook"></a>
                                <a href="" target="_blank" class="googleplus"></a>
                                <a href="" target="_blank" class="twitter"></a>
                                <a href="" target="_blank" class="tumbler"></a>
			</div>
			<p>
                            All rights reserved but for what? :)
			</p>
		</div>
	</div>
</body>
</html>