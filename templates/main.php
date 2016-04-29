<?php
script('files_report', 'script');
style('files_report', 'style');
?>

<div id="app">
	<div id="app-navigation">
	    <?php print_unescaped($this->inc('part.settings')); ?>
	    <?php print_unescaped($this->inc('part.navigation')); ?>
    </div>
	<div id="app-content">
		<div id="container">
			<?php print_unescaped($this->inc('part.content')); ?>
		</div>
        <div id="loading_reports" class="icon-loading"></div>
	</div>
</div>
